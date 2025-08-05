<script setup>
import { useRoute } from 'vue-router'
import Sidebar from './components/SideBar.vue'
import { ref, onMounted, computed } from 'vue'

const route = useRoute()
const user = ref(null)
const role = ref(null)
const isReady = ref(false)
const notifications = ref([])

onMounted(() => {
  const u = localStorage.getItem('user')
  const r = localStorage.getItem('role')
  user.value = u ? JSON.parse(u) : { Prenom: 'Utilisateur' }
  role.value = r ? r : 'Invité'
  isReady.value = true
  
  window.Echo.channel('medicaments')
    .listen('MyEvent', (data) => {
      notifications.value.push(data)
      console.log('Nouvelle notification :', data)
    })
})

const showSidebar = computed(() => {
  if (!isReady.value) return false
  if (route.path === '/login') return false
  if (route.path.startsWith('/facture')) return false
  if (route.path.startsWith('/MotDePasseOublie')) return false
  if (route.path.toLowerCase().startsWith('/medicaments/faible-frequence')) return false
  return true
})
</script>

<template>
  <div id="app">
    <Sidebar 
      v-if="showSidebar"
      :user="user"
      :userRole="role"
    />

    <main :class="showSidebar ? 'ml-64 p-6' : 'w-full p-6'">
      <router-view />
    </main>
  </div>
</template>
