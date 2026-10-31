// VitePress would fail on the first [Laravel] import with a bare ENOENT.
import { existsSync } from 'node:fs'

if (!existsSync(new URL('../.laravel/composer.json', import.meta.url))) {
  console.error(
    [
      'docs/.laravel is missing: the [Laravel] tabs import their samples from a laravel-ing-pay checkout there.',
      'Clone routegroup/laravel-ing-pay next to php-ing-pay and link it from docs/:',
      '',
      '  git clone https://github.com/routegroup/laravel-ing-pay.git ../laravel-ing-pay',
      '  ln -s ../../laravel-ing-pay docs/.laravel',
      '',
      '(both run from the php-ing-pay root)',
    ].join('\n'),
  )
  process.exit(1)
}
