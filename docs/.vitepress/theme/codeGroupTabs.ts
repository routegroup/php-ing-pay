import { inBrowser, onContentUpdated } from 'vitepress'

// Every guide shows its samples as `[PHP]` / `[Laravel]` code groups. A reader
// works in one of the two, so picking a tab picks it in every group on the
// site, and the choice survives navigation and reloads.

const STORAGE_KEY = 'ing-pay:code-group-tab'

function read(): string | null {
  try {
    return localStorage.getItem(STORAGE_KEY)
  } catch {
    return null
  }
}

function write(title: string): void {
  try {
    localStorage.setItem(STORAGE_KEY, title)
  } catch {
    // Private windows may refuse storage; the tabs still sync on this page.
  }
}

function titleOf(input: HTMLInputElement): string | undefined {
  return input.parentElement?.querySelector(`label[for="${input.id}"]`)?.textContent?.trim()
}

// Switches the groups directly rather than clicking their inputs: VitePress's
// click handler scrolls each clicked tab into view, which would jump the page.
function select(title: string): void {
  document.querySelectorAll<HTMLElement>('.vp-code-group').forEach((group) => {
    const inputs = Array.from(group.querySelectorAll<HTMLInputElement>(':scope > .tabs > input'))
    const i = inputs.findIndex((input) => titleOf(input) === title)
    const blocks = group.querySelector(':scope > .blocks')?.children
    if (i < 0 || !blocks?.[i]) return

    inputs[i].checked = true
    Array.from(blocks).forEach((block, j) => block.classList.toggle('active', j === i))
  })
}

export function syncCodeGroupTabs(): void {
  if (!inBrowser) return

  onContentUpdated(() => {
    const title = read()
    if (title) select(title)
  })

  window.addEventListener('click', (event) => {
    const input = event.target
    if (!(input instanceof HTMLInputElement) || !input.matches('.vp-code-group input')) return

    const title = titleOf(input)
    if (!title) return

    write(title)
    select(title)
  })
}
