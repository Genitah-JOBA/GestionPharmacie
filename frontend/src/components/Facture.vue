<template>
  <div class="facture">
    <div class="flex justify-between items-center mb-2">
      <img src="@/assets/images/logo.jpg" alt="Logo Connexion" class="h-12" />
      <img src="@/assets/images/logo1.jpg" alt="Logo Pharmacie" class="h-12" />
    </div>
    
    <div class="text-center mb-2">
      <h1 class="font-bold text-base">FACTURE</h1>
    </div>

    <div class="text-center mb-2 font-mono text-xs">
      {{ dateDuJour }} Pharmacie CHU Andrainjato
    </div>

    <div class="text-center mb-2">
      <h1 class="font-bold text-base">Transaction n° {{ facture.id }}</h1>
    </div>

    <div class="mb-2">
      <div
        v-for="(item, i) in facture.items"
        :key="i"
        class="flex justify-between font-mono text-sm"
      >
        <span>{{ item.nom }} x ({{ item.quantite }})--{{ item.dosage }}</span>
        <span>{{ item.sousTotal }} Ar</span>
      </div>
    </div>

    <div class="flex justify-between font-bold border-t border-dashed border-black pt-2 mb-2 font-mono text-sm">
      <span>TOTAL</span>
      <span>{{ facture.total }} Ar</span>
    </div>

    <div class="text-center mb-2">
      <p class="font-mono">Mode de paiement : <strong>{{ formatModePaiement(facture.modePaiement) }}</strong></p>
    </div>

    <div class="text-center font-bold mb-2 font-mono text-sm">
      PAIEMENT REÇU
    </div>

    <div class="text-center mb-2 font-mono text-sm">
      <p>Merci et bonne journée !</p>
    </div>

    <div class="border-t border-dashed border-black my-2"></div>

    <button
      @click="imprimer"
      class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 no-print"
    >
      🖨 Imprimer la facture
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const facture = ref({})
const route = useRoute()

function formatModePaiement(mode) {
  switch (mode) {
    case 'Espèces': return 'En espèces'
    case 'Mobile Money': return 'Par mobile money'
    case 'Virement': return 'Par virement'
    case 'Paiement en attente': return 'Paiement en attente'
    default: return mode
  }
}

async function chargerFacture() {
  const id = route.params.id
  const res = await fetch(`http://localhost:8000/api/factures/${id}`, { credentials: 'include' })
  const data = await res.json()
  facture.value = data.facture || data
}

const dateDuJour = new Date().toLocaleDateString('fr-FR', {
  day: '2-digit',
  month: '2-digit',
  year: 'numeric'
})

function imprimer() {
  window.print()
}

onMounted(() => {
  chargerFacture()
})
</script>

<style scoped>
.facture {
  max-width: 280px;
  margin: 0 auto;
  font-family: monospace;
  font-size: 12px;
  line-height: 1.4;
}

.border-dashed {
  border-style: dashed;
}

@media print {
  @page {
    size: 80mm auto;
    margin: 0 !important;
  }

  body {
    margin: 0 !important;
    padding: 0 !important;
  }

  .no-print {
    display: none !important;
  }

  body * {
    visibility: hidden;
  }

  .facture, .facture * {
    visibility: visible;
  }

  .facture {
    position: absolute;
    left: 0;
    top: 0;
    width: 80mm; /* Largeur ticket */
    margin: 0;
  }
}

</style>
