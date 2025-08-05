<script setup>
import Sidebar from '@/components/SideBar.vue'
import { ref, onMounted } from 'vue'

const user = ref(null)
const role = ref(null)

onMounted(() => {
  const u = localStorage.getItem('user')
  const r = localStorage.getItem('role')
  user.value = u ? JSON.parse(u) : { Prenom: 'Utilisateur' }
  role.value = r ? r : 'Invité'
})
</script>

<template>
  <div class="flex">
    <Sidebar :user="user" :userRole="role" />
    <main class="ml-64 p-6 w-full bg-gradient-to-br from-white to-gray-100 min-h-screen">
      <router-view />
    </main>
  </div>
</template>
