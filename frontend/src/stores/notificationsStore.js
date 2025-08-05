import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useNotificationsStore = defineStore('notifications', {
  state: () => ({
    notifications: [],
  }),
  getters: {
    unreadCount: (state) => {
      return state.notifications.filter(n => !n.read).length
    },
  },
  actions: {
    async fetchNotifications() {
      try {
        const res = await axios.get('/notifications')
        this.notifications = res.data
      } catch (error) {
        console.error('Erreur notifications :', error)
      }
    },

    addNotification(notification) {
      if (!this.notifications.some(n => n.id === notification.id)) {
        this.notifications.unshift(notification)
      }
    },

    async markRead(id) {
      try {
        await axios.put(`/notifications/${id}/read`)
        const notif = this.notifications.find(n => n.id === id)
        if (notif) notif.read = true
      } catch (error) {
        console.error('Erreur lors du marquage :', error)
      }
    },

    async markAllRead() {
      try {
        await axios.put('/notifications/mark-all-read')
        this.notifications.forEach(n => { n.read = true })
      } catch (error) {
        console.error('Erreur lors du marquage global :', error)
      }
    },
  },
})
