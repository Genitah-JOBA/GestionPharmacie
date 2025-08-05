<template>
  <div class="p-6 bg-gray-50 dark:bg-gray-800 min-h-screen font-sans text-gray-900 dark:text-gray-200">
    <h2 class="text-3xl font-extrabold mb-6 text-center text-green-700 tracking-wide">
      Archives des Ventes
    </h2>
    <p class="text-center text-gray-600 dark:text-gray-400 mb-8 max-w-3xl mx-auto">
      Retrouvez ici toutes les ventes archivées et historiques, avec statistiques et filtres avancés.
    </p>

    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white dark:border border-white dark:bg-gray-700 rounded-lg shadow p-6 flex flex-col items-center dark:hover:bg-gray-900">
        <p class="text-gray-500 dark:text-gray-400 uppercase text-sm font-semibold mb-2">Total Ventes</p>
        <p class="text-4xl font-bold text-green-600">{{ totalVentes }}</p>
      </div>
      <div class="bg-white dark:border border-white dark:bg-gray-700 rounded-lg shadow p-6 flex flex-col items-center dark:hover:bg-gray-900">
        <p class="text-gray-500 dark:text-gray-400 uppercase text-sm font-semibold mb-2">Chiffre d'affaires (Ar)</p>
        <p class="text-4xl font-bold text-green-600">Ar {{ chiffreAffairesFormatte }}</p>
      </div>
    </div>

    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 shadow rounded p-6 mb-8">
      <form @submit.prevent="fetchVentes" class="flex flex-wrap gap-4 items-end justify-between">
        <div class="flex flex-col w-full sm:w-auto">
          <label class="mb-1 font-semibold text-gray-700 dark:text-gray-300" for="search">Recherche client/produit</label>
          <input
            id="search"
            type="text"
            v-model="searchTerm"
            placeholder="Nom client, produit..."
            class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200"
          />
        </div>
        <div class="flex flex-col w-full sm:w-auto">
          <label class="mb-1 font-semibold text-gray-700 dark:text-gray-300" for="startDate">Date début</label>
          <input
            id="startDate"
            type="date"
            v-model="filterStartDate"
            class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200"
          />
        </div>
        <div class="flex flex-col w-full sm:w-auto">
          <label class="mb-1 font-semibold text-gray-700 dark:text-gray-300" for="endDate">Date fin</label>
          <input
            id="endDate"
            type="date"
            v-model="filterEndDate"
            class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200"
          />
        </div>
        <div class="flex gap-2">
          <button
            type="submit"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded transition"
          >
            Filtrer
          </button>
          <button
            type="button"
            @click="resetFilters"
            class="bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold px-5 py-2 rounded transition"
          >
            Réinitialiser
          </button>
        </div>
      </form>
    </div>

    <div v-if="error" class="max-w-5xl mx-auto bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 p-4 rounded mb-6 text-center">
      {{ error }}
    </div>
    <div v-if="isLoading" class="max-w-5xl mx-auto text-center mb-6">
      <svg
        class="animate-spin h-10 w-10 mx-auto text-green-600"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8v8z"
        ></path>
      </svg>
      <p class="text-green-600 mt-2 font-semibold">Chargement des archives...</p>
    </div>

    <div v-if="!isLoading && ventes.length" class="max-w-5xl mx-auto bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
      <table class="w-full text-left table-auto border-collapse border border-gray-200 dark:border-gray-700">
        <thead>
          <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
            <th>Client</th>
            <th>Nombre d'achat</th>
            <th>Montant total</th>
            <th>Dernière achat</th>
            <th>Pour plus d'information</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="5" class="text-center text-gray-400 dark:text-gray-500">***************************************</td>
          </tr>
          <tr v-for="client in ventes" :key="client.nom_complet" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <td>{{ client.nom_complet }}</td>
            <td>{{ client.nombre_ventes }}</td>
            <td>Ar {{ Number(client.montant_total).toFixed(2) }}</td>
            <td>{{ formatDate(client.derniere_vente) }}</td>
            <td>
              <router-link :to="`/archives/ventes/${encodeURIComponent(client.nom_complet)}`" class="text-4xm text-green-600 underline">
                En savoir plus
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="!isLoading && !ventes.length" class="max-w-5xl mx-auto bg-white dark:bg-gray-800 shadow rounded p-6 text-center text-gray-500 dark:text-gray-400 italic">
      Aucune archive à afficher pour le moment.
    </div>

    <div
      v-if="pageCount > 1"
      class="max-w-5xl mx-auto mt-6 flex justify-center space-x-2"
      role="navigation"
      aria-label="Pagination"
    >
      <button
        @click="changePage(currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-3 py-1 rounded border border-green-600 text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-green-600 hover:text-white transition"
      >
        Précédent
      </button>

      <button
        v-for="page in pageCount"
        :key="page"
        @click="changePage(page)"
        :class="[
          'px-3 py-1 rounded border border-green-600 hover:bg-green-600 hover:text-white transition',
          currentPage === page ? 'bg-green-600 text-white' : 'text-green-600'
        ]"
        aria-current="page"
      >
        {{ page }}
      </button>

      <button
        @click="changePage(currentPage + 1)"
        :disabled="currentPage === pageCount"
        class="px-3 py-1 rounded border border-green-600 text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-green-600 hover:text-white transition"
      >
        Suivant
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

export default {
  name: 'ArchivesVentes',
  setup() {
    const ventes = ref([])
    const isLoading = ref(false)
    const error = ref(null)

    const currentPage = ref(1)
    const itemsPerPage = 10

    const searchTerm = ref('')
    const filterStartDate = ref('')
    const filterEndDate = ref('')

    const totalVentes = ref(0)
    const chiffreAffaires = ref(0)

    const chiffreAffairesFormatte = computed(() => {
      return Number(chiffreAffaires.value).toFixed(2)
    })

    async function fetchVentes() {
      isLoading.value = true
      error.value = null
      try {
        const params = {
          page: currentPage.value,
          per_page: itemsPerPage,
          search: searchTerm.value || undefined,
          start_date: filterStartDate.value || undefined,
          end_date: filterEndDate.value || undefined,
        }
        const response = await axios.get('/ventes/archives', { params })

        ventes.value = (response.data.data || []).sort((a, b) => {
          return new Date(b.derniere_vente) - new Date(a.derniere_vente)
        })

        totalVentes.value = response.data.total || 0
        chiffreAffaires.value = response.data.chiffreAffaires || 0
      } catch (err) {
        error.value = 'Erreur lors du chargement des archives.'
        console.error(err)
      } finally {
        isLoading.value = false
      }
    }

    watch([currentPage, searchTerm, filterStartDate, filterEndDate], () => {
      fetchVentes()
    })

    onMounted(() => {
      fetchVentes()
    })

    const pageCount = computed(() => {
      return totalVentes.value > 0 ? Math.ceil(totalVentes.value / itemsPerPage) : 1
    })

    function resetFilters() {
      searchTerm.value = ''
      filterStartDate.value = ''
      filterEndDate.value = ''
      currentPage.value = 1
    }

    function changePage(page) {
      if (page < 1 || page > pageCount.value) return
      currentPage.value = page
    }

    function formatDate(dateStr) {
      const d = new Date(dateStr)
      return d.toLocaleDateString('fr-FR')
    }

    function regrouperVentes(data) {
      const ventesMap = new Map()

      data.forEach(row => {
      if (!ventesMap.has(row.vente_id)) {
        ventesMap.set(row.vente_id, {
          id: row.vente_id,
          client: row.nom_client,
          medicaments: [],
          montant: row.montant_total,
          date: row.created_at,
        })
    }
    const vente = ventesMap.get(row.vente_id)
    vente.medicaments.push({ nom: row.nom_medicament, quantite: row.quantite })
  })

  return Array.from(ventesMap.values())
}


    return {
      ventes,
      isLoading,
      error,
      currentPage,
      itemsPerPage,
      searchTerm,
      filterStartDate,
      filterEndDate,
      totalVentes,
      chiffreAffaires,
      chiffreAffairesFormatte,
      pageCount,
      resetFilters,
      changePage,
      formatDate,
    }
  },
}
</script>

<style scoped>


</style>
