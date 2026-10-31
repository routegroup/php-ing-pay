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

  markdown: {
    config: (md) => md.use(rejectInlineSamples),
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
