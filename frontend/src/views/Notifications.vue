<template>
  <div class="max-w-5xl mx-auto p-8 text-gray-900 dark:text-gray-200 relative bg-white dark:bg-gray-800">
    <DarkToggle class="absolute top-4 right-4" />

    <div class="flex items-center justify-between mb-8">
      <h1 class="text-4xl font-bold text-green-800 tracking-tight">📢 Notifications</h1>
    </div>

    <div class="flex flex-wrap gap-3 mb-6">
      <button
        v-for="type in filterTypes"
        :key="type"
        @click="filterType = type"
        :class="[ 
          'px-5 py-2 rounded-full font-medium transition text-sm shadow-sm',
          filterType === type
            ? type === 'all'
              ? 'bg-green-700 text-white'
              : typeColors[type].bg + ' ' + typeColors[type].text + ' border border-opacity-30'
            : 'bg-gray-200 text-gray-600 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'
        ]"
      >
        {{ typeLabels[type] }}
      </button>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-20">
      <svg class="animate-spin h-10 w-10 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
      </svg>
    </div>

    <ul v-else class="space-y-4">
      <li
        v-for="notif in paginatedNotifications"
        :key="notif.id"
        class="p-5 rounded-lg flex items-start gap-4 shadow-sm border-l-8 bg-white dark:bg-gray-800 transition-colors"
      >
        <component
          :is="getIconComponent(notif.type)"
          :class="[
            'w-8 h-8 mt-1',
            notif.read ? 'text-gray-400' : typeColors[notif.type]?.text
          ]"
        />

        <div class="flex-1">
          <div class="flex justify-between items-start">
            <p
              class="font-semibold text-lg inline-block px-3 py-1 rounded"
              :class="notif.read
                ? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200'
                : typeColors[notif.type]?.bg + ' ' + typeColors[notif.type]?.text"
            >
              {{ notif.title }}
            </p>
            <button
              v-if="!notif.read"
              @click="markRead(notif)"
              class="text-sm text-white hover:text-white underline cursor-pointer ml-4"
            >
              Marquer comme lu
            </button>
          </div>

          <p class="text-gray-700 dark:text-gray-300 mt-2">{{ notif.message }}</p>

          <router-link
            v-if="notif.type === 'info' && notif.link"
            :to="notif.link"
            class="text-blue-600 hover:underline mt-2 inline-block text-sm"
          >
            Voir plus
          </router-link>

          <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ formatDate(notif.date) }}</p>
        </div>
      </li>
    </ul>

    <div class="flex justify-center mt-10 space-x-2">
      <button
        @click="changePage(currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-3 py-1 rounded border text-sm border-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-50"
      >
        ◀
      </button>
      <button
        v-for="page in totalPages"
        :key="page"
        @click="changePage(page)"
        :class="[
          'px-3 py-1 text-sm rounded border transition',
          page === currentPage
            ? 'bg-green-600 text-white border-green-600'
            : 'text-gray-700 dark:text-gray-200 border-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
        ]"
      >
        {{ page }}
      </button>
      <button
        @click="changePage(currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 rounded border text-sm border-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-50"
      >
        ▶
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { AlertTriangle, Bell, Clock } from 'lucide-vue-next'
import { useNotificationsStore } from '../stores/notificationsStore'

export default {
  name: 'Notifications',
  components: { AlertTriangle, Bell, Clock },
  setup() {
    const store = useNotificationsStore()
    const filterType = ref('all')
    const currentPage = ref(1)
    const pageSize = 5

    onMounted(() => {
      store.fetchNotifications()
      window.Echo.channel('medicaments').listen('.medicament.notification', (e) => {
        console.log('Reçu :', e)
        const alertMessages = e.alerts.join(', ')
        store.addNotification({
          id: e.id || Date.now(),
          title: 'Alerte Médicament',
          message: `Le médicament "${e.nom}" présente une alerte : ${alertMessages}`,
          type: e.type || 'info',
          date: new Date().toISOString(),
          read: false,
        })
      })
    })

    const filteredNotifications = computed(() => {
      if (filterType.value === 'all') return store.notifications || []
      return (store.notifications || []).filter(n => n.type === filterType.value)
    })

    const sortedNotifications = computed(() =>
      [...filteredNotifications.value].sort((a, b) => new Date(b.date) - new Date(a.date))
    )

    const totalPages = computed(() =>
      Math.ceil(sortedNotifications.value.length / pageSize)
    )

    const paginatedNotifications = computed(() => {
      const start = (currentPage.value - 1) * pageSize
      return sortedNotifications.value.slice(start, start + pageSize)
    })

    const hasUnread = computed(() =>
      (store.notifications || []).some(n => !n.read)
    )

    const filterTypes = ['all', 'danger', 'warning', 'info']
    const typeLabels = {
      all: 'Toutes',
      danger: 'Urgentes',
      warning: 'À surveiller',
      info: 'Infos',
    }

    function getIconComponent(type) {
      if (type === 'danger') return AlertTriangle
      if (type === 'warning') return Clock
      return Bell
    }

    function formatDate(dateStr) {
      if (!dateStr) return 'Date inconnue'
      const d = new Date(dateStr)
      if (isNaN(d.getTime())) return 'Date invalide'
      return d.toLocaleString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    }

    function markRead(notif) {
      store.markRead(notif.id)
    }

    function markAllRead() {
      if (confirm('Voulez-vous vraiment marquer toutes les notifications comme lues ?')) {
        store.markAllRead()
      }
    }

    function changePage(page) {
      if (page < 1 || page > totalPages.value) return
      currentPage.value = page
    }

    return {
      filterType,
      currentPage,
      paginatedNotifications,
      totalPages,
      hasUnread,
      filterTypes,
      typeLabels,
      loading: computed(() => store.loading),
      formatDate,
      getIconComponent,
      markRead,
      markAllRead,
      changePage,
      typeColors: {
        danger: { bg: 'bg-red-100 dark:bg-red-900', text: 'text-red-700 dark:text-red-400' },
        warning: { bg: 'bg-yellow-100 dark:bg-yellow-900', text: 'text-yellow-700 dark:text-yellow-300' },
        info: { bg: 'bg-green-100 dark:bg-green-900', text: 'text-green-700 dark:text-green-300' },
      },
    }
  },
}
</script>

<style scoped>
li {
  transition: background-color 0.3s ease, transform 0.3s ease;
}
li:hover {
  transform: scale(1.01);
  background-color: #f0fff4;
}
@media (prefers-color-scheme: dark) {
  li:hover {
    background-color: #111827;
  }
}
</style>
