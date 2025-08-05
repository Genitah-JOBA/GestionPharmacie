import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const theme = ref(localStorage.getItem('theme') || 'light')

  const applyTheme = (value) => {
    if (value === 'dark') {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
    localStorage.setItem('theme', value)
  }

  applyTheme(theme.value)

  watch(theme, (newVal) => {
    applyTheme(newVal)
  })

  const toggleTheme = () => {
    theme.value = theme.value === 'dark' ? 'light' : 'dark'
  }

  const isDark = computed(() => theme.value === 'dark')

  return { theme, toggleTheme, isDark }
})
