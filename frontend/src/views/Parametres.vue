<template>
  <div class="max-w-5xl mx-auto p-8 text-gray-900 dark:text-gray-200 relative bg-white dark:bg-gray-800">
    <DarkToggle class="absolute top-4 right-4" />

    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-2">Paramètres</h1>
      <p class="text-gray-600 dark:text-gray-400">Gérez votre compte et vos préférences</p>
    </div>

    <div class="space-y-6">
      <div
        v-for="section in sections"
        :key="section.name"
        class="bg-gray-100 dark:bg-gray-800 rounded-xl shadow"
      >
        <div
          class="flex justify-between items-center p-6 cursor-pointer bg-gray-400 dark:bg-black transition rounded-t-xl"
          @click="toggleSection(section.name)"
        >
          <h2 class="text-lg font-semibold text-black dark:text-gray-200">{{ section.title }}</h2>
          <svg
            class="w-5 h-5 text-gray-600 dark:text-gray-300 transform transition-transform duration-200"
            :class="{ 'rotate-180': openSections[section.name] }"
            fill="none"
            viewBox="0 0 20 20"
            stroke="currentColor"
          >
            <path d="M5 7.5L10 12.5L15 7.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
        <div
          v-if="openSections[section.name]"
          class="border-t border-gray-300 dark:border-gray-600 divide-y divide-gray-300 dark:divide-gray-600"
        >
          <div
            v-for="item in section.items"
            :key="item.title"
            class="flex justify-between items-center p-6 cursor-pointer transition hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md"
            :class="{ 'text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20': item.danger }"
            @click="item.action"
          >
            <div>
              <h3 class="font-medium" :class="item.danger ? 'text-red-600' : 'text-gray-800 dark:text-gray-200'">
                {{ item.title }}
              </h3>
              <p
                class="text-sm"
                :class="item.danger ? 'text-red-500 dark:text-red-400' : 'text-gray-600 dark:text-gray-400'">
                {{ item.description }}
              </p>
            </div>
            <svg
              class="w-5 h-5"
              :class="item.danger ? 'text-red-500' : 'text-gray-500 dark:text-gray-300'"
              viewBox="0 0 20 20"
              fill="none"
            >
              <path d="M7.5 5L12.5 10L7.5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <transition name="fade">
      <div
        v-if="notification.visible"
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-xl shadow-lg z-50 transition"
        :class="notification.type === 'success' ? 'bg-black text-white' : 'bg-gray-700 text-white'"
      >
        {{ notification.message }}
      </div>
    </transition>
  </div>
</template>

<script>
import DarkToggle from '@/components/SombreClair.vue'

export default {
  components: { DarkToggle },
  data() {
    return {
      role: 'admin',
      isDarkMode: false,
      showModal: false,
      notification: {
        visible: false,
        message: '',
        type: 'success'
      },
      openSections: {
        account: false,
        privacy: false
      },
      sections: [
        {
          name: 'account',
          title: 'Compte',
          items: [
            {
              title: 'Modification du mot de passe',
              description: 'Changez votre mot de passe régulièrement',
              action: () => this.$router.push('/changePassword'),
              danger: false
            },
            {
              title: 'Suppression du compte',
              description: 'Supprimez définitivement votre compte',
              action: () => this.showNotification('Suppression déclenchée.', 'warning'),
              danger: true
            }
          ]
        },
        {
          name: 'privacy',
          title: 'Confidentialité',
          items: [
            {
              title: 'Guide',
              description: 'Contrôlez vos données personnelles',
              action: () => this.$router.push('/utilisation'),
              danger: false
            }
          ]
        }
      ]
    }
  },
  mounted() {
    this.checkDarkMode()
  },
  methods: {
    toggleSection(name) {
      this.openSections[name] = !this.openSections[name]
    },
    checkDarkMode() {
      this.isDarkMode = document.documentElement.classList.contains('dark')
    },
    confirmAction() {
      this.showModal = false
      this.showNotification('Action confirmée.')
    },
    showNotification(message, type = 'success') {
      this.notification.message = message
      this.notification.type = type
      this.notification.visible = true
      setTimeout(() => {
        this.notification.visible = false
      }, 3000)
    }
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
