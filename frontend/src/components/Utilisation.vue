<template>
  <div class="max-w-5xl mx-auto p-8 text-white dark:text-white relative bg-white dark:bg-gray-800">
    <DarkToggle class="absolute top-4 right-4" />

    <h1 class="text-3xl text-gray-600 font-bold mb-8 text-center dark:text-white">🧪 Guide d'utilisation de l'application </h1>

    <TransitionGroup name="fade" tag="div">
      <div
        v-for="(section, index) in sections"
        :key="section.title"
        class="mb-6 bg-white dark:bg-black rounded-lg shadow-md p-5 border border-gray-200 dark:border-gray-700"
      >
        <h2 class="text-xl font-semibold mb-2 flex items-center gap-2">
          <span>{{ section.icon }}</span>
          {{ index + 1 }}. {{ section.title }}
        </h2>
        <p class="mb-2" v-if="!section.expanded">
          {{ section.preview }}...
        </p>
        <div v-else>
          <p class="mb-2" v-for="(p, i) in section.paragraphs" :key="i">
            {{ p }}
          </p>
          <ul v-if="section.points" class="list-disc list-inside ml-4 mb-2">
            <li v-for="(point, i) in section.points" :key="i">{{ point }}</li>
          </ul>
          <ol v-if="section.steps" class="list-decimal list-inside ml-4 mb-2">
            <li v-for="(step, i) in section.steps" :key="i">{{ step }}</li>
          </ol>
        </div>

        <button
          @click="toggleSection(index)"
          class="text-sm text-blue-600 hover:underline mt-2 dark:bg-green-900 dark:text-white"
        >
          {{ section.expanded ? 'Réduire' : 'Voir plus' }}
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const sections = ref([
  {
    title: 'Introduction',
    icon: '📘',
    preview: 'Bienvenue dans le système de gestion de ventes pharmaceutiques.',
    paragraphs: [
      'Bienvenue dans votre système de gestion de pharmacie. Cette application est conçue pour répondre aux besoins spécifiques des pharmacies en matière de gestion de stocks, ventes, clients et sécurité.',
      'Ce guide vous accompagnera pas à pas pour tirer le meilleur parti de l’application, quelle que soit votre fonction (administrateur, pharmacien ou autre personnel autorisé).'
    ],
    expanded: false
  },
  {
    title: 'Tableau de bord',
    icon: '📊',
    preview: 'Le tableau de bord offre une vue globale sur l\'activité.',
    paragraphs: [
      'Le tableau de bord est la première interface que vous voyez après vous être connecté. Il vous donne un aperçu rapide des éléments essentiels : nombre de médicaments en stock et périmés, utilisateurs, stocks faibles, total des ventes du jour et total cumulé.',
      'C’est également ici que vous retrouvez les statistiques de ventes (achats par jour).'
    ],
    points: ['Produits en stock critique', 'Dernières ventes', 'Notifications système'],
    expanded: false
  },
  {
    title: 'Gestion des Pharmaciens',
    icon: '🧑‍⚕️',
    preview: 'Ajoutez, modifiez ou supprimez les informations des pharmaciens et gérez leurs comptes.',
    paragraphs: [
      'La section de gestion des pharmaciens vous permet de tenir à jour la liste des professionnels travaillant dans l\'établissement.',
      'Notez que seuls les administrateurs ont accès à cette gestion.',
      'Vous pouvez attribuer des rôles spécifiques, contrôler leurs accès et vérifier leurs activités récentes.'
    ],
    points: [
      'Ajouter un nouveau pharmacien',
      'Modifier les informations existantes',
      'Supprimer un compte pharmacien',
      'Activer ou désactiver des comptes',
      'Attribuer ou retirer des rôles'
    ],
    expanded: false
  },
  {
    title: 'Gestion des médicaments',
    icon: '💊',
    preview: 'Ajoutez, modifiez ou supprimez les médicaments facilement.',
    paragraphs: [
      'Dans le menu Produits, vous pouvez gérer tous les médicaments et articles vendus. Cela inclut l’ajout de nouveaux produits, la mise à jour des informations (prix, quantité, catégorie, etc.) ou leur suppression.',
      'La recherche intelligente vous permet de filtrer rapidement par nom, catégorie ou disponibilité.'
    ],
    steps: [
      'Accéder au menu "Produits"',
      'Cliquer sur "Ajouter un produit"',
      'Remplir les champs : nom, catégorie, prix, quantité',
      'Valider pour enregistrer'
    ],
    expanded: false
  },
  {
    title: 'Gestion des Ventes',
    icon: '🛒',
    preview: 'Le module de ventes permet de traiter les transactions rapidement.',
    paragraphs: [
      'La vente de médicaments est fluide et rapide. Vous pouvez ajouter un ou plusieurs produits dans le panier, appliquer une remise selon le client ou la campagne en cours, et sélectionner le mode de paiement.',
      'Notez que seuls les pharmaciens ont accès à cette gestion.',
      'Une fois la vente validée, un ticket est généré automatiquement avec les détails de la transaction.'
    ],
    points: ['Ajout au panier', 'Choix du paiement', 'Impression du ticket'],
    expanded: false
  },
  {
    title: 'Archive des Ventes',
    icon: '🗄️',
    preview: 'Consultez l’historique complet des transactions et ventes réalisées.',
    paragraphs: [
      'Toutes les ventes sont archivées automatiquement pour garantir la traçabilité et la conformité réglementaire.',
      'Vous avez accès aux détails de chaque vente.',
      'Cette section vous offre un moteur de recherche avancé pour retrouver rapidement une transaction spécifique.'
    ],
    points: [
      'Rechercher une vente par date ou référence',
      'Télécharger un reçu ou un rapport',
      'Exporter l’historique des ventes',
      'Accéder au détail de la vente',
      'Analyser les tendances de vente'
    ],
    expanded: false
  },
  {
    title: 'Notifications',
    icon: '🔔',
    preview: 'Suivez toutes les alertes en temps réel, filtrez-les et gardez le contrôle.',
    paragraphs: [
      'La section Notifications centralise en un seul endroit toutes les alertes générées par le système : ruptures de stock, ventes importantes, anomalies ou simples informations.',
      'Vous pouvez filtrer les notifications par catégorie (Urgentes, À surveiller, Infos), les marquer comme lues ou toutes les gérer en un clic.',
      'Les alertes sont reçues en temps réel grâce au système d’écoute des événements pour vous permettre de réagir immédiatement.'
    ],
    points: [
      'Recevoir des notifications en direct',
      'Filtrer par niveau d’importance',
      'Marquer une ou toutes les notifications comme lues',
      'Consulter l’historique détaillé',
      'Réagir sans délai aux alertes critiques'
    ],
    expanded: false
  },
  {
    title: 'Assistance',
    icon: '📞',
    preview: 'Besoin d\'aide ? Nous sommes là pour vous.',
    paragraphs: [
      'Vous pouvez consulter la documentation détaillée ou demander une session de formation en ligne.',
      'En cas de difficultés, contactez notre équipe via genitahrazafindrasoa@gmail.com / lorraineanjarasoa@gmail.com ou appelez le 038 14 611 05 / 034 75 914 21.'
    ],
    expanded: false
  }
])

function toggleSection(index) {
  sections.value[index].expanded = !sections.value[index].expanded
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
