<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold mb-4">Médicaments à faible fréquence</h1>
    <table class="min-w-full bg-white border">
      <thead>
        <tr>
          <th class="border px-4 py-2">Référence</th>
          <th class="border px-4 py-2">Nom</th>
          <th class="border px-4 py-2">Quantité</th>
          <th class="border px-4 py-2">Fréquence</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="med in medicaments" :key="med.reference">
          <td class="border px-4 py-2">{{ med.reference }}</td>
          <td class="border px-4 py-2">{{ med.nom }}</td>
          <td class="border px-4 py-2">{{ med.quantite }}</td>
          <td class="border px-4 py-2">{{ med.frequence_utilisation }}</td>
        </tr>
      </tbody>
    </table>
    <button
      @click="imprimer"
      class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
    >
      Imprimer
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const medicaments = ref([])

const fetchMedicaments = async () => {
  const res = await axios.get('http://localhost:8000/api/medicaments/faible-frequence')
  medicaments.value = res.data
}

const imprimer = () => {
  window.print()
}

onMounted(fetchMedicaments)
</script>

<style>
@media print {
  button {
    display: none !important;
  }
}
</style>
