<template>
  <div class="flex min-h-screen bg-gray-50 font-sans">
    <aside class="w-64 bg-white shadow-lg flex flex-col justify-between p-8" aria-label="Sidebar navigation">
      <h2 class="text-2xl font-bold text-green-700">Pharmacie</h2>
      <nav>
        <ul class="space-y-4 text-sm">
          <li v-for="link in links" :key="link.path">
            <router-link
              :to="link.path"
              class="flex items-center gap-3 text-gray-700 hover:text-green-700 transition font-medium"
              active-class="text-green-700 font-semibold"
            >
              <span class="text-xl">{{ link.icon }}</span>
              {{ link.label }}
            </router-link>
          </li>
        </ul>
      </nav>
      <button
        @click="logout"
        class="flex items-center gap-2 text-red-600 hover:text-red-800 font-semibold text-sm mt-10"
      >
        🚪 Déconnexion
      </button>
    </aside>

    <main class="flex-1 p-10 flex flex-col">
      <header class="flex justify-between items-center mb-12 border-b border-gray-300 pb-4 select-none">
        <div class="text-gray-500 text-sm italic" aria-label="Dernière connexion">
          Dernière connexion : 
          <time :datetime="user?.last_login">
            {{ user?.last_login ? new Date(user.last_login).toLocaleString() : 'Jamais' }}
          </time>
        </div>

        <div class="flex items-center space-x-4">
          <div class="text-right">
            <p class="text-lg font-semibold text-gray-900">
              {{ user ? `${user.Prenom} ${user.Nom}` : 'Utilisateur' }}
            </p>
            <p class="text-sm uppercase text-green-700 font-bold tracking-wide">
              {{ user?.role || '' }}
            </p>
          </div>
          <img
            :src="user?.photo || 'https://via.placeholder.com/48?text=User'"
            alt="utilisateur"
            class="w-12 h-12 rounded-full object-cover shadow-md"
          />
        </div>
      </header>

      <!-- Stat Cards -->
      <div class="cards">
        <div class="card">
          <p class="text-sm text-gray-500">Médicaments en stock</p>
          <p class="text-3xl font-bold text-green-600 mt-2">{{ stats.medicaments }}</p>
        </div>
        <div class="card">
          <p class="text">Ventes du jour</p>
          <p class="text-3xl font-bold text-blue-600 mt-2">{{ stats.ventes_journalieres }} Ar</p>
        </div>
        <div class="card">
          <p class="text-sm text-gray-500">Ventes totales</p>
          <p class="text-3xl font-bold text-purple-600 mt-2">{{ stats.ventes_total }} Ar</p>
        </div>
      </div>

      <!-- Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button
          @click="$router.push('/ventes')"
          class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl shadow flex items-center justify-center gap-2"
        >
          🛒 Gérer les ventes
        </button>
        <button
          @click="$router.push('/medicaments')"
          class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl shadow flex items-center justify-center gap-2"
        >
          💊 Gérer les médicaments
        </button>
        <button
          v-if="user?.role === 'admin'"
          @click="$router.push('/AdminDashboard')"
          class="bg-gray-700 hover:bg-black text-white px-5 py-3 rounded-xl shadow flex items-center justify-center gap-2"
        >
          👥 Gestion utilisateurs
        </button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref(null)
const stats = ref({
  medicaments: 0,
  ventes_journalieres: 0,
  ventes_total: 0
})

const links = ref([
  { path: '/dashboard', icon: '📊', label: 'Tableau de bord' },
  { path: '/medicaments', icon: '💊', label: 'Médicaments' },
  { path: '/ventes', icon: '🛒', label: 'Ventes' },
  { path: '/changePassword', icon: '👤', label: 'Profil' }
])

function logout() {
  localStorage.removeItem('token')
  router.push('/login')
}

onMounted(async () => {
  try {
    const token = localStorage.getItem('token')

    const resUser = await fetch('http://127.0.0.1:8000/api/user', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    if (resUser.ok) {
      user.value = await resUser.json()
    }

    const resStats = await fetch('http://127.0.0.1:8000/api/pharmacien/stats', {
      headers: { 'autorisation': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    if (resStats.ok) {
      stats.value = await resStats.json()
    }
  } catch (e) {
    console.error('Erreur chargement:', e)
  }
})
</script>

<style scoped>
img[alt=" utilisateur"] {
  transition: box-shadow 0.3s ease;
}
img[alt="utilisateur"]:hover {
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.6);
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1.5rem;
}

.card {
  background:rgb(243, 173, 82);
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 1.5em 1em;
  transition: transform 0.3s ease;
  cursor: default;
}

.card:hover {
  transform: scale(1.05);
}

.icon {
  font-size: 2.5rem;
  margin-bottom: 0.6rem;
}

.card h3 {
  color: #34495e;
  margin: 0;
  font-weight: 600;
}

.card strong {
  display: block;
  margin-top: 0.5rem;
  font-size: 1.5rem;
  color: #2c3e50;
}
</style>
