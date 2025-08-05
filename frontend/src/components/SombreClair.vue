<template>
  <button
    @click="toggle"
    class="flex items-center gap-2 px-3 py-2 rounded-md bg-gray-800 text-white hover:bg-gray-700 transition"
    aria-label="Toggle dark mode"
  >
    <span v-if="isDark">🌞 Clair</span>
    <span v-else>🌙 Sombre</span>
  </button>
</template>

<script setup>
import { useThemeStore } from '@/stores/ThemeStore'
import { computed } from 'vue'

const themeStore = useThemeStore()
const isDark = computed(() => themeStore.isDark)
const toggle = () => themeStore.toggleTheme()
</script>
