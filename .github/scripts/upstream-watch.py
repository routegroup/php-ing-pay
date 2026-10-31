#!/usr/bin/env python3
"""Open package-adjustment issues when the imoje API changelog moves.

ING Pay runs on imoje's gateway, so its API changelog is this package's
upstream: https://bump.sh/pgw/doc/imoje-api/changes
"""

from __future__ import annotations

import json
import os
import re
import subprocess
import sys
import urllib.error
import urllib.request
import xml.etree.ElementTree as ET
from datetime import datetime, timezone
from email.utils import parsedate_to_datetime
from html.parser import HTMLParser
from typing import Any

RSS_URL = "https://bump.sh/pgw/doc/imoje-api/changes.rss"
USER_AGENT = "routegroup/php-ing-pay upstream-watch"
TRACKING_TITLE = "Upstream watcher RSS baseline"
TRACKING_MARKER = "watcher-baseline:imoje-rss"
IMOJE_MARKER_PREFIX = "imoje-api:"
LABEL_PACKAGE_ADJUSTMENT = "package-adjustment"
LABEL_ENHANCEMENT = "enhancement"
GUID_RE = re.compile(
    r"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}",
    re.IGNORECASE,
)


class _TextExtractor(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self._chunks: list[str] = []

    def handle_data(self, data: str) -> None:
        self._chunks.append(data)

    def text(self) -> str:
        return re.sub(r"\n{3,}", "\n\n", "".join(self._chunks)).strip()


def html_to_text(raw: str) -> str:
    parser = _TextExtractor()
    parser.feed(raw)
    parser.close()
    return parser.text()


def fetch(url: str) -> bytes:
    request = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    try:
        with urllib.request.urlopen(request, timeout=30) as response:
            if response.status != 200:
                raise RuntimeError(f"{url} returned HTTP {response.status}")
            return response.read()
    except urllib.error.URLError as error:
        raise RuntimeError(f"failed to fetch {url}: {error}") from error


def parse_rss(xml_bytes: bytes) -> list[dict[str, str]]:
    root = ET.fromstring(xml_bytes)
    items: list[dict[str, str]] = []
    for item in root.findall("./channel/item"):
        guid = (item.findtext("guid") or "").strip()
        if not guid:
            continue
        pub_raw = item.findtext("pubDate") or ""
        try:
            published = parsedate_to_datetime(pub_raw)
        except (TypeError, ValueError):
            published = datetime.now(timezone.utc)
        items.append(
            {
                "guid": guid,
                "title": (item.findtext("title") or "").strip(),
                "link": (item.findtext("link") or "").strip(),
                "description": item.findtext("description") or "",
                "date": published.date().isoformat(),
            }
        )
    return items


def repo() -> str:
    env = os.environ.get("GH_REPO", "").strip()
    if env:
        return env
    value = subprocess.check_output(
        ["gh", "repo", "view", "--json", "nameWithOwner", "--jq", ".nameWithOwner"],
        text=True,
    ).strip()
    if not value:
        raise RuntimeError("could not determine GH_REPO")
    return value


def gh_api(path: str, method: str = "GET", data: dict[str, Any] | None = None) -> Any:
    command = ["gh", "api", "--method", method, path]
    if method == "GET":
        command.insert(2, "--paginate")
    if data is None:
        completed = subprocess.run(command, check=False, capture_output=True, text=True)
    else:
        completed = subprocess.run(
            command + ["--input", "-"],
            check=False,
            capture_output=True,
            text=True,
            input=json.dumps(data),
        )
    if completed.returncode != 0:
        raise RuntimeError(
            f"gh api {method} {path} failed ({completed.returncode}): {completed.stderr.strip()}"
        )
    if not completed.stdout.strip():
        return None
    parsed = json.loads(completed.stdout)
    if isinstance(parsed, list) and parsed and isinstance(parsed[0], list):
        flat: list[Any] = []
        for page in parsed:
            flat.extend(page)
        return flat
    return parsed


def list_issues(repository: str) -> list[dict[str, Any]]:
    raw = gh_api(f"repos/{repository}/issues?state=all&per_page=100") or []
    if not isinstance(raw, list):
        raise RuntimeError("unexpected issues list payload")
    return [issue for issue in raw if "pull_request" not in issue]


def issue_markers(issues: list[dict[str, Any]]) -> set[str]:
    seen: set[str] = set()
    for issue in issues:
        body = issue.get("body") or ""
        for match in re.finditer(rf"{re.escape(IMOJE_MARKER_PREFIX)}({GUID_RE.pattern})", body, re.I):
            seen.add(match.group(1).lower())
    return seen


def find_tracking_issue(issues: list[dict[str, Any]]) -> dict[str, Any] | None:
    matches = [issue for issue in issues if TRACKING_MARKER in (issue.get("body") or "")]
    if len(matches) > 1:
        raise RuntimeError("multiple RSS baseline tracking issues found")
    return matches[0] if matches else None


def tracking_guids(body: str) -> set[str]:
    return {match.group(0).lower() for match in GUID_RE.finditer(body)}


def ensure_label(repository: str, name: str, color: str, description: str) -> None:
    encoded = urllib.request.quote(name)
    command = ["gh", "api", f"repos/{repository}/labels/{encoded}"]
    completed = subprocess.run(command, check=False, capture_output=True, text=True)
    if completed.returncode == 0:
        return
    if "404" not in completed.stderr and "Not Found" not in completed.stderr:
        raise RuntimeError(f"failed to read label {name}: {completed.stderr.strip()}")
    gh_api(
        f"repos/{repository}/labels",
        method="POST",
        data={"name": name, "color": color, "description": description[:100]},
    )


def create_issue(
    repository: str,
    title: str,
    body: str,
    labels: list[str] | None = None,
) -> dict[str, Any]:
    payload: dict[str, Any] = {"title": title, "body": body}
    if labels:
        payload["labels"] = labels
    created = gh_api(f"repos/{repository}/issues", method="POST", data=payload)
    if not isinstance(created, dict) or "number" not in created:
        raise RuntimeError(f"issue create returned unexpected payload: {created}")
    print(f"opened #{created['number']}: {title}", file=sys.stderr)
    return created


def close_issue(repository: str, number: int) -> None:
    gh_api(f"repos/{repository}/issues/{number}", method="PATCH", data={"state": "closed"})


def tracking_body(guids: list[str]) -> str:
    lines = "\n".join(f"- `{guid}`" for guid in guids)
    return (
        "Operational store for the upstream watcher. Do not delete. "
        "Do not edit the guid list.\n\n"
        f"{TRACKING_MARKER}\n\n"
        "## Seen RSS guids (first-run snapshot)\n\n"
        f"{lines}\n"
    )


def issue_title(item: dict[str, str]) -> str:
    # Date only — imoje OpenAPI info.version is not a product SemVer.
    breaking = "breaking" in item["title"].lower()
    kind = "imoje API breaking" if breaking else "imoje API"
    return f"Adjust package: {kind} ({item['date']})"


def issue_body(item: dict[str, str]) -> str:
    summary = html_to_text(item["description"]) or "(no summary in RSS)"
    return (
        f"New imoje API changelog entry. ING Pay rides on the same gateway, so "
        f"this may need a change in `src/`, the fixtures, or the docs.\n\n"
        f"- [Changelog entry]({item['link']})\n"
        f"- RSS guid: `{item['guid']}`\n\n"
        f"```\n{summary}\n```\n\n"
        f"{IMOJE_MARKER_PREFIX}{item['guid']}\n"
    )


def main() -> int:
    rss_items = parse_rss(fetch(RSS_URL))
    if not rss_items:
        raise RuntimeError(f"no items in {RSS_URL}")

    repository = repo()
    issues = list_issues(repository)
    seen = issue_markers(issues)
    tracking = find_tracking_issue(issues)

    ensure_label(
        repository,
        LABEL_PACKAGE_ADJUSTMENT,
        "1d76db",
        "Upstream watcher: the imoje API moved and the package may need a change",
    )
    ensure_label(repository, LABEL_ENHANCEMENT, "a2eeef", "New feature or request")

    if tracking is None:
        if seen:
            raise RuntimeError(
                "RSS baseline tracking issue is missing, but watcher markers already exist"
            )
        created = create_issue(
            repository,
            TRACKING_TITLE,
            tracking_body([item["guid"] for item in rss_items]),
        )
        close_issue(repository, int(created["number"]))
        return 0

    body = tracking.get("body") or ""
    baseline = tracking_guids(body)
    if not baseline:
        raise RuntimeError("tracking issue has no RSS guids")

    for item in rss_items:
        guid = item["guid"].lower()
        if guid in baseline or guid in seen:
            continue
        create_issue(
            repository,
            issue_title(item),
            issue_body(item),
            labels=[LABEL_PACKAGE_ADJUSTMENT, LABEL_ENHANCEMENT],
        )

    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except Exception as error:  # noqa: BLE001 — fail the Actions job
        print(error, file=sys.stderr)
        raise SystemExit(1)
