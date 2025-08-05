<template>
  <MessageModal
    v-if="showModal"
    :show="showModal"
    title="Erreur"
    :message="errorMessage"
    type="error"
    buttonText="Fermer"
    @close="showModal = false"
  />

  <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-200">
    <h1 class="text-3xl font-bold">TABLEAU DE BORD</h1>
    <p class="text-sm mt-1">
      Dernière connexion :
      <strong>{{ user?.derniere_connexion ? new Date(user.derniere_connexion).toLocaleDateString('fr-FR') : 'Jamais' }}</strong>
    </p>

    <!-- Cartes -->
    <div class="flex flex-wrap gap-5 mt-8 justify-center">
      <div
        v-for="(item, index) in cards"
        :key="index"
        class="w-[150px] h-[190px] sm:w-[160px] sm:h-[200px] bg-gradient-to-br rounded-xl text-center text-sm font-semibold flex flex-col justify-center items-center border shadow-xl transition-transform hover:scale-105 duration-300"
        :class="[item.bg, item.text, item.border, 'dark:shadow-gray-800']"
      >
        <component :is="item.icon" class="w-8 h-8 mb-2" :class="[item.iconColor]" />
        <div class="uppercase tracking-widest text-xs">{{ item.label }}</div>
        <div class="text-2xl font-bold mt-1">{{ item.value }}</div>
      </div>
    </div>

    <!-- Graphique -->
    <div class="grid grid-cols-1 gap-6 mt-12">
      <div class="bg-white dark:bg-gray-900 p-4 rounded shadow-md dark:shadow-gray-800">
        <h2 class="text-lg font-semibold text-center mb-4">
          Statistique de ventes
        </h2>
        <canvas id="achatsJourChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import {
  Chart,
  ArcElement,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
} from 'chart.js'

import {
  ShoppingCart,
  BarChart2,
  AlertTriangle,
  Users,
  Pill,
  TrendingDown,
} from 'lucide-vue-next'

import MessageModal from '@/views/ModalMessage.vue'

Chart.register(
  ArcElement,
  BarController, BarElement,
  CategoryScale, LinearScale,
  Tooltip, Legend
)

export default {
  components: {
    MessageModal,
    TrendingDown,
  },
  data() {
    return {
      user: null,
      stats: {
        medicaments: 0,
        ventes_journalieres: 0,
        ventes_total: 0,
        medicaments_perimes: 0,
        utilisateurs: 0,
        ruptures_stock: 0
      },
      achatsJournaliers: [],
      showModal: false,
      errorMessage: ''
    }
  },
  computed: {
    cards() {
      return [
        {
          label: 'Médicaments en stock',
          value: this.stats.medicaments,
          icon: Pill,
          bg: 'from-green-100 to-green-400 dark:from-green-800 dark:to-green-600',
          text: 'text-green-800 dark:text-green-200',
          border: 'border-green-500 dark:border-green-700',
          iconColor: 'text-green-700 dark:text-green-300'
        },
        {
          label: 'Ventes du jour',
          value: `${this.stats.ventes_journalieres} Ar`,
          icon: ShoppingCart,
          bg: 'from-blue-100 to-blue-400 dark:from-blue-800 dark:to-blue-600',
          text: 'text-blue-800 dark:text-blue-200',
          border: 'border-blue-500 dark:border-blue-700',
          iconColor: 'text-blue-700 dark:text-blue-300'
        },
        {
          label: 'Total des ventes',
          value: `${this.stats.ventes_total} Ar`,
          icon: BarChart2,
          bg: 'from-purple-100 to-purple-400 dark:from-purple-800 dark:to-purple-600',
          text: 'text-purple-800 dark:text-purple-200',
          border: 'border-purple-500 dark:border-purple-700',
          iconColor: 'text-purple-700 dark:text-purple-300'
        },
        {
          label: 'Médicaments périmés',
          value: this.stats.medicaments_perimes,
          icon: AlertTriangle,
          bg: 'from-red-100 to-red-400 dark:from-red-800 dark:to-red-600',
          text: 'text-red-800 dark:text-red-200',
          border: 'border-red-500 dark:border-red-700',
          iconColor: 'text-red-700 dark:text-red-300'
        },
        {
          label: 'Total utilisateurs',
          value: this.stats.utilisateurs,
          icon: Users,
          bg: 'from-yellow-100 to-yellow-400 dark:from-yellow-700 dark:to-yellow-500',
          text: 'text-yellow-800 dark:text-yellow-100',
          border: 'border-yellow-500 dark:border-yellow-600',
          iconColor: 'text-yellow-700 dark:text-yellow-200'
        },
        {
          label: 'Stock faible',
          value: this.stats.ruptures_stock,
          icon: TrendingDown,
          bg: 'from-orange-100 to-orange-400 dark:from-orange-800 dark:to-orange-600',
          text: 'text-orange-800 dark:text-orange-200',
          border: 'border-orange-500 dark:border-orange-700',
          iconColor: 'text-orange-700 dark:text-orange-300'
        }
      ]
    }
  },
  async mounted() {
    try {
      const token = localStorage.getItem('token')

      const resUser = await fetch('http://127.0.0.1:8000/api/user', {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      })
      if (!resUser.ok) throw new Error('Erreur lors de la récupération de l’utilisateur')
      this.user = await resUser.json()

      const resStats = await fetch('http://127.0.0.1:8000/api/pharmacien/stats', {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      })
      if (!resStats.ok) throw new Error('Erreur lors de la récupération des statistiques')

      const apiStats = await resStats.json()
      this.stats = {
        medicaments: apiStats.medicaments || 0,
        ventes_journalieres: apiStats.ventes_journalieres || 0,
        ventes_total: apiStats.ventes_total || 0,
        medicaments_perimes: apiStats.medicaments_perimes || 0,
        utilisateurs: apiStats.utilisateurs || 0,
        ruptures_stock: apiStats.ruptures_stock || 0,
      }

      this.achatsJournaliers = apiStats.achats_journaliers || []

      this.renderAchatsJourChart()
    } catch (err) {
      console.error('Erreur :', err)
      this.errorMessage = err.message || 'Une erreur inconnue est survenue.'
      this.showModal = true
    }
  },
  methods: {
    renderAchatsJourChart() {
      const ctx = document.getElementById('achatsJourChart').getContext('2d')
      const labels = this.achatsJournaliers.map(item =>
        new Date(item.jour).toLocaleDateString('fr-FR')
      )
      const data = this.achatsJournaliers.map(item => item.total)

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Achats par Jour (Ar)',
            data: data,
            backgroundColor: '#34d399',
            borderColor: '#059669',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: true, position: 'top' }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      })
    }
  }
}
</script>

<style scoped>
* {
  color: inherit;
}
</style>
