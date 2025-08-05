<template>
  <!-- Modal de déconnexion -->
  <transition name="fade">
    <MessageModal
      v-if="showLogoutModal"
      :show="showLogoutModal"
      :title="'Déconnexion'"
      :message="'Voulez-vous vraiment vous déconnecter ?'"
      :confirmText="'Oui, se déconnecter'"
      :cancelText="'Annuler'"
      @confirm="handleLogoutConfirm"
      @cancel="showLogoutModal = false"
    >
    </MessageModal>
  </transition>

  <!-- Sidebar -->
  <aside class="sidebar fixed top-0 left-0 min-h-screen bg-green-400 dark:bg-gray-800 text-black dark:text-white shadow-xl z-50 flex flex-col w-64">
  <div class="header flex flex-col items-center py-10 border-b border-white/20 dark:border-white/30">
    <img
      src="../assets/images/logo1.jpg"
      alt="logo"
      class="logo w-32 h-32 rounded-full border-4 border-white shadow-md transition-transform duration-100 ease-in-out hover:scale-110 hover:rotate-5"
    />
    <p class="mt-2 font-semibold text-xs">Bienvenue</p>
    <p class="text-xs italic">
      {{ user?.Prenom ? (user.Nom || '') : 'Utilisateur' }}
    </p>
    <span class="text-[10px] bg-white/20 dark:bg-white/10 mt-1 px-2 py-0.5 rounded-full uppercase">
      {{ userRole }}
    </span>
  </div>

  <nav class="flex-1 mt-4 px-3">
    <ul class="space-y-1.5">
      <!-- Tableau de bord -->
      <router-link
        to="/AccueilPharmacien"
        class="flex items-center gap-2.5 px-3 py-2.5 rounded-md font-medium transition hover:scale-105 hover:bg-green-400 dark:hover:bg-green-600"
        active-class="bg-green-500 dark:bg-green-900 font-bold"
      >
        <BarChart2 class="w-5 h-5" />
        <span class="uppercase text-xs font-medium">Tableau de bord</span>
      </router-link>

      <!-- Gestion des pharmaciens -->
      <li>
        <router-link
          :to="gestionPharmaciensLink"
          class="flex items-center gap-2.5 px-3 py-2.5 rounded-md font-medium transition hover:scale-105 hover:bg-green-400 dark:hover:bg-green-600"
          active-class="bg-green-500 dark:bg-green-900 font-bold"
        >
          <LayoutDashboardIcon class="w-5 h-5" />
          <span class="uppercase text-xs font-medium">Gestion des pharmaciens</span>
        </router-link>
      </li>

      <!-- Gestion des médicaments -->
      <li>
        <router-link
          to="/medicaments"
          class="flex items-center gap-2.5 px-3 py-2.5 rounded-md font-medium transition hover:scale-105 hover:bg-green-400 dark:hover:bg-green-600"
          active-class="bg-green-500 dark:bg-green-900 font-bold"
        >
          <PackageIcon class="w-5 h-5" />
          <span class="uppercase text-xs font-medium">Gestion des médicaments</span>
        </router-link>
      </li>

      <!-- Gestion ventes -->
      <li>
        <router-link
          :to="gestionVentesLink"
          class="flex items-center gap-2.5 px-3 py-2.5 rounded-md font-medium transition hover:scale-105 hover:bg-green-400 dark:hover:bg-green-600"
          active-class="bg-green-500 dark:bg-green-900 font-bold"
        >
          <ShoppingCartIcon class="w-5 h-5" />
          <span class="uppercase text-xs font-medium">Gestion des ventes</span>
        </router-link>
      </li>

      <!-- Archives ventes -->
      <li>
        <router-link
          to="/archives-ventes"
          class="flex items-center gap-2.5 px-3 py-2.5 rounded-md font-medium transition hover:scale-105 hover:bg-green-400 dark:hover:bg-green-600"
          active-class="bg-green-500 dark:bg-green-900 font-bold"
        >
          <ArchiveIcon class="w-5 h-5" />
          <span class="uppercase text-xs font-medium">Archives des Ventes</span>
        </router-link>
      </li>

      <!-- Notifications -->
      <li>
        <router-link
          to="/notifications"
          class="flex items-center gap-2.5 px-3 py-2 rounded-md transition hover:bg-green-400 dark:hover:bg-green-600 relative"
          active-class="bg-green-500 dark:bg-green-900 font-bold"
        >
          <BellIcon class="w-5 h-5" />
          <span class="text-xs uppercase font-medium">Notifications</span>
          <span
            v-if="unreadCount > 0"
            class="absolute top-0 right-0 -mt-1 -mr-2 bg-red-600 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold animate-pulse"
          >
            {{ unreadCount }}
          </span>
        </router-link>
      </li>

      <!-- Paramètres -->
      <div class="mt-5 border-t border-white/20 dark:border-white/30 pt-3">
        <ul class="space-y-1.5">
          <li>
            <router-link
              to="/parametres"
              class="flex items-center gap-2.5 px-3 py-2 rounded-md transition hover:bg-green-400 dark:hover:bg-green-600"
              active-class="bg-green-500 dark:bg-green-900 font-bold"
            >
              <SettingsIcon class="w-5 h-5" />
              <span class="text-xs uppercase font-medium">Paramètres</span>
            </router-link>
          </li>
        </ul>
      </div>
    </ul>
  </nav>

  <!-- Bouton Déconnexion -->
  <div class="px-4 py-3 border-t border-white/20 dark:border-white/30 mt-auto">
    <button
      @click="showLogoutModal = true"
      class="flex items-center gap-2 px-4 py-2.5 w-full rounded-md bg-green-600 dark:bg-green-900 dark:text-white hover:bg-green-800 dark:hover:bg-blue-900 transition font-semibold"
    >
      <LogOutIcon class="w-5 h-5" />
      <span class="uppercase text-xs font-medium">Déconnexion</span>
    </button>
  </div>
</aside>
</template>

<script>
import {
  LayoutDashboardIcon,
  ShoppingCartIcon,
  BellIcon,
  SettingsIcon,
  LogOutIcon,
  PackageIcon,
  ArchiveIcon,
  BarChart2
} from 'lucide-vue-next'

import MessageModal from '../views/ModalMessage.vue' 
import { useNotificationsStore } from '../stores/notificationsStore'

export default {
  name: 'Sidebar',
  components: {
    LayoutDashboardIcon,
    ShoppingCartIcon,
    BellIcon,
    SettingsIcon,
    LogOutIcon,
    PackageIcon,
    ArchiveIcon,
    BarChart2,
    MessageModal
  },
  props: {
    user: { type: Object, default: () => ({ Prenom: 'Utilisateur' }) },
    userRole: { type: String, default: 'Invité' }
  },
  data() {
    return {
      showLogoutModal: false
    }
  },
  setup() {
    const notificationsStore = useNotificationsStore()
    notificationsStore.fetchNotifications()

    setInterval(() => {
      notificationsStore.fetchNotifications()
    }, 30000)

    return {
      unreadCount: notificationsStore.unreadCount
    }
  },
  computed: {
    gestionPharmaciensLink() {
      return this.userRole.toLowerCase() === 'administrateur' ? '/admin' : '/autorisation'
    },
    gestionVentesLink() {
      return this.userRole.toLowerCase() === 'pharmacien' ? '/ventes' : '/autorisation'
    }
  },
  methods: {
    handleLogoutConfirm() {
      this.showLogoutModal = false
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
