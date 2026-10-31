import DefaultTheme from 'vitepress/theme'
import type { Theme } from 'vitepress'
import { syncCodeGroupTabs } from './codeGroupTabs'

export default {
  extends: DefaultTheme,
  setup: syncCodeGroupTabs,
} satisfies Theme
