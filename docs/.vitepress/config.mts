import { existsSync, readFileSync } from 'node:fs'
import { relative } from 'node:path'
import { defineConfig, type MarkdownRenderer } from 'vitepress'

// PHP and JSON reach a page only through `<<< @/…#region` imports from tested
// files, so an inline fence in either language is a build error, not a review
// comment. Snippet imports are fences too; VitePress marks them with `src`.
function rejectInlineSamples(md: MarkdownRenderer): void {
  md.core.ruler.after('block', 'reject-inline-samples', (state) => {
    for (const token of state.tokens) {
      if (token.type !== 'fence' || (token as { src?: unknown }).src) continue

      const lang = token.info.trim().split(/[\s{[:]/)[0].toLowerCase()
      if (lang !== 'php' && lang !== 'json') continue

      const line = (token.map?.[0] ?? 0) + 1
      throw new Error(
        `${state.env.relativePath}:${line}: inline \`${lang}\` fence. ` +
          'Import the sample from a tested file with `<<< @/…#region` instead.',
      )
    }
  })
}

// VitePress imports the whole file when a `#region` is not in it, so a renamed
// or deleted region would quietly put the test wiring on the page. A missing
// file already fails the build; this makes a missing region fail it too.
function requireSnippetRegions(md: MarkdownRenderer): void {
  md.core.ruler.after('block', 'require-snippet-regions', (state) => {
    for (const token of state.tokens) {
      const [file, region] = ((token as { src?: unknown }).src ?? []) as string[]
      if (token.type !== 'fence' || !file || !region || !existsSync(file)) continue

      const marker = (tag: string) =>
        new RegExp(`^\\s*(//|#|/\\*|<!--) ?#?${tag} ${region.replace(/[*]/g, '\\*')}(?=\\s|$)`, 'm')
      const source = readFileSync(file, 'utf8')
      if (marker('region').test(source) && marker('endregion').test(source)) continue

      const line = (token.map?.[0] ?? 0) + 1
      throw new Error(
        `${state.env.relativePath}:${line}: no \`#region ${region}\` … \`#endregion ${region}\` ` +
          `in ${relative(process.cwd(), file)}.`,
      )
    }
  })
}

export default defineConfig({
  title: 'ING Pay for PHP',
  description: 'Take payments through ING Pay from plain PHP or Laravel.',
  lang: 'en-US',

  // The site is `docs/` and nothing else, so `@/../tests/…` reaches this
  // repo's samples and `@/.laravel/…` the Laravel package's.
  srcDir: '.',
  srcExclude: ['.laravel/**'],
  base: '/',
  cleanUrls: true,
  sitemap: { hostname: 'https://php-ing-pay.routegroup.pl' },

  // Guides link to pages that are planned (#13) but not written yet. Each
  // entry goes when its page lands; any other dead link still fails the build.
  ignoreDeadLinks: [
    '/notifications',
  ],

  markdown: {
    config: (md) => md.use(rejectInlineSamples).use(requireSnippetRegions),
  },

  themeConfig: {
    search: { provider: 'local' },

    // One hand-ordered journey (#13). Groups appear as their pages land:
    // Prologue → Getting Started → Taking Payments → After the Payment →
    // Your Account → Digging Deeper → Reference.
    sidebar: [
      {
        text: 'Prologue',
        items: [{ text: 'Introduction', link: '/introduction' }],
      },
      {
        text: 'Getting Started',
        items: [
          { text: 'Installation', link: '/installation' },
          { text: 'Configuration', link: '/configuration' },
          { text: 'Sending Requests', link: '/sending-requests' },
          { text: 'Choosing an Integration', link: '/choosing-an-integration' },
        ],
      },
      {
        text: 'Taking Payments',
        items: [
          { text: 'Transactions', link: '/transactions' },
          { text: 'Paywall', link: '/paywall' },
          { text: 'Payment Links', link: '/payment-links' },
          { text: 'Card Widget & One-Click', link: '/card-widget-and-one-click' },
          { text: 'Installments', link: '/installments' },
          { text: 'Carts & Invoices', link: '/carts-and-invoices' },
        ],
      },
      {
        text: 'Your Account',
        items: [
          { text: 'Services & Payment Methods', link: '/services-and-payment-methods' },
          { text: 'Trusted IPs', link: '/trusted-ips' },
        ],
      },
      {
        text: 'Digging Deeper',
        items: [
          { text: 'Error Handling', link: '/error-handling' },
          { text: 'Payloads', link: '/payloads' },
          { text: 'Testing', link: '/testing' },
        ],
      },
      {
        text: 'Reference',
        items: [{ text: 'Enums', link: '/reference/enums' }],
      },
    ],

    outline: { level: [2, 3] },

    socialLinks: [{ icon: 'github', link: 'https://github.com/routegroup/php-ing-pay' }],

    editLink: {
      pattern: 'https://github.com/routegroup/php-ing-pay/edit/main/docs/:path',
    },
  },
})
