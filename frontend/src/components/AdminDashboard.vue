<template>
  <MessageModal
    :show="showDeleteModal"
    title="Confirmer la suppression"
    message="Êtes-vous sûr de vouloir supprimer ce pharmacien ?"
    confirmText="Oui, Supprimer"
    cancelText="Annuler"
    @confirm="deleteUser(confirmedId)"
    @cancel="showDeleteModal = false"
  />
  <div class="p-6 bg-gray-50 dark:bg-gray-800 min-h-screen font-sans text-gray-900 dark:text-gray-200">
    <h2 class="text-3xl font-extrabold mb-10 text-center text-green-700 dark:text-green-400 tracking-wide">
      Gestion des Pharmaciens
    </h2>

    <div class="flex flex-wrap justify-center items-center mb-10 gap-4">
      <input
        type="text"
        v-model="recherche"
        placeholder="🔍 Rechercher ..."
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 rounded p-2 w-full max-w-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-green-400"
      />
      <select
        v-model="filters.role"
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded text-sm"
      >
        <option value="">Tous les rôles</option>
        <option value="Pharmacien">Pharmacien</option>
        <option value="Administrateur">Administrateur</option>
      </select>
      <select
        v-model="filters.actif"
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded text-sm"
      >
        <option value="">Tous les statuts</option>
        <option value="1">Actif</option>
        <option value="0">Inactif</option>
      </select>
    </div>

    <!-- FORMULAIRE VERTICAL -->
    <div class="text-center mb-10">
      <h3 class="text-xl font-semibold uppercase bg-gradient-to-r from-green-700 to-green-400 dark:from-green-400 dark:to-green-600 bg-clip-text text-transparent tracking-wider">
        {{ isEditing ? 'Modifier un Pharmacien' : 'Ajouter un Nouveau Pharmacien' }}
      </h3>
      <div class="w-20 h-1 mx-auto mt-2 bg-gradient-to-r from-green-700 to-green-400 dark:from-green-400 dark:to-green-600 rounded-full animate-pulse"></div>
    </div>

    <form @submit.prevent="handleSubmit" class="flex flex-col gap-4 items-center mb-12 max-w-sm mx-auto">
      <div class="flex flex-col w-full">
        <input
          v-model="form.Nom"
          placeholder="Nom"
          class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full text-sm"
          :class="{ 'border-red-500': errors.Nom }"
        />
        <small v-if="errors.Nom" class="text-red-500 text-xs mt-1">{{ errors.Nom }}</small>
      </div>

      <div class="flex flex-col w-full">
        <input
          v-model="form.Prenom"
          placeholder="Prénom (facultatif)"
          class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full text-sm"
          :class="{ 'border-red-500': errors.Prenom }"
        />
        <small v-if="errors.Prenom" class="text-red-500 text-xs mt-1">{{ errors.Prenom }}</small>
      </div>

      <div class="flex flex-col w-full">
        <input
          v-model="form.Phone"
          placeholder="Téléphone"
          class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full text-sm"
          :class="{ 'border-red-500': errors.Phone }"
        />
        <small v-if="errors.Phone" class="text-red-500 text-xs mt-1">{{ errors.Phone }}</small>
      </div>

      <div class="flex flex-col w-full">
        <input
          v-model="form.mot_de_passe"
          :placeholder="isEditing ? 'Nouveau mot de passe' : 'Mot de passe'"
          class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full text-sm"
          :class="{ 'border-red-500': errors.mot_de_passe }"
          :required="!isEditing"
          type="password"
        />
        <small v-if="errors.mot_de_passe" class="text-red-500 text-xs mt-1">{{ errors.mot_de_passe }}</small>
      </div>

      <div class="flex flex-col w-full">
        <select
          v-model="form.role"
          class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full text-sm"
          :class="{ 'border-red-500': errors.role }"
        >
          <option disabled value="">-- Rôle --</option>
          <option value="Pharmacien">Pharmacien</option>
          <option value="Administrateur">Administrateur</option>
        </select>
        <small v-if="errors.role" class="text-red-500 text-xs mt-1">{{ errors.role }}</small>
      </div>

      <div class="flex gap-2 mt-4 w-full justify-center">
        <button
          type="submit"
          class="bg-green-600 text-white px-4 py-1.5 rounded hover:bg-green-700 text-sm font-semibold transition dark:bg-green-800 dark:hover:bg-green-600"
        >
          {{ isEditing ? 'Modifier' : 'Ajouter' }}
        </button>
        <button
          v-if="isEditing"
          @click="resetForm"
          type="button"
          class="bg-gray-500 text-white px-3 py-1.5 rounded hover:bg-gray-600 text-sm font-semibold transition"
        >
          Annuler
        </button>
      </div>
    </form>

    <!-- TABLEAU UTILISATEURS -->
    <div class="overflow-auto">
      <table class="min-w-full bg-white dark:bg-gray-900 shadow-md rounded-lg overflow-hidden text-sm text-gray-900 dark:text-gray-200">
        <thead class="bg-green-100 dark:bg-green-900">
          <tr>
            <th class="px-4 py-2">Nom</th>
            <th class="px-4 py-2">Prénom</th>
            <th class="px-4 py-2">Téléphone</th>
            <th class="px-4 py-2">Rôle</th>
            <th class="px-4 py-2">Dernière connexion</th>
            <th class="px-4 py-2">Statut</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in filteredUsers"
            :key="user.id"
            class="text-center border-t border-gray-300 dark:border-gray-700"
          >
            <td class="px-4 py-2">{{ user.Nom }}</td>
            <td class="px-4 py-2">{{ user.Prenom }}</td>
            <td class="px-4 py-2">{{ user.Phone }}</td>
            <td class="px-4 py-2">{{ user.role }}</td>
            <td class="px-4 py-2">
              {{ user.derniere_connexion ? new Date(user.derniere_connexion).toLocaleString('fr-FR') : 'Jamais' }}
            </td>
            <td class="px-4 py-2">
              <span :class="user.actif ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                {{ user.actif ? 'Actif' : 'Inactif' }}
              </span>
            </td>
            <td class="px-4 py-2 space-x-1">
              <button
                @click="editUser(user)"
                class="bg-yellow-400 px-2 py-1 rounded hover:bg-yellow-500 text-xs font-semibold"
              >
                ✏️
              </button>
              <button
                @click="confirmDelete(user.id)"
                class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 text-xs font-semibold"
              >
                🗑️
              </button>
              <button
                @click="toggleStatus(user.id)"
                class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 text-xs font-semibold"
              >
                {{ user.actif ? 'Désactiver' : 'Activer' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      recherche: '',
      form: {
        Nom: '',
        Prenom: '',
        Phone: '',
        mot_de_passe: '',
        role: '',
      },
      errors: {},
      isEditing: false,
      currentId: null,
      filters: {
        role: '',
        actif: ''
      },
      showDeleteModal: false,
      confirmedId: null,
    };
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
  validateForm() {
    this.errors = {};

    if (!this.form.Nom.trim() || /[^a-zA-ZÀ-ÿ\s]/.test(this.form.Nom)) {
      this.errors.Nom = 'Nom invalide ! (Lettres uniquement)';
    }

    if (this.form.Prenom && /[^a-zA-ZÀ-ÿ\s]/.test(this.form.Prenom)) {
      this.errors.Prenom = 'Prénom invalide ! (Lettres uniquement)';
    }

    if (!this.form.Phone.match(/^(032|033|034|037|038)\d{7}$/)) {
      this.errors.Phone = 'Numéro invalide (ex: 032XXXXXXX)';
    }

    if (!this.isEditing && this.form.mot_de_passe.length < 6) {
      this.errors.mot_de_passe = 'Mot de passe trop court, minimum 6 caractères';
    }

    if (!this.form.role) {
      this.errors.role = 'Le rôle est requis';
    }

    return Object.keys(this.errors).length === 0;
  },

  confirmDelete(id) {
    this.confirmedId = id;
    this.showDeleteModal = true;
  },

  async handleSubmit() {
    if (!this.validateForm()) return;

    try {
      const url = this.isEditing
        ? `http://127.0.0.1:8000/api/users/${this.currentId}`
        : 'http://127.0.0.1:8000/api/register';

      const method = this.isEditing ? 'PUT' : 'POST';
      const data = this.isEditing ? this.form : { ...this.form };

      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
        body: JSON.stringify(data),
      });

      if (!response.ok) throw new Error('Erreur lors de l\'envoi');

      this.resetForm();
      this.fetchUsers();

      alert(this.isEditing ? 'Utilisateur modifié avec succès !' : 'Utilisateur ajouté avec succès !');
    } catch (error) {
      alert('Erreur lors de la soumission.');
    }
  },

  async deleteUser(id) {
    try {
      const response = await fetch(`http://127.0.0.1:8000/api/users/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      });

      if (!response.ok) throw new Error('Suppression refusée');

      this.fetchUsers();
      alert('Utilisateur supprimé avec succès !');
    } catch (error) {
      alert('Erreur lors de la suppression.');
    }
  },

  async fetchUsers() {
    try {
      const token = localStorage.getItem('token');
      const res = await fetch('http://127.0.0.1:8000/api/users', {
        headers: { Authorization: `Bearer ${token}` },
      });

      if (!res.ok) throw new Error('Erreur de récupération');
      this.users = await res.json();
    } catch (error) {
      alert('Erreur de chargement des utilisateurs.');
    }
  },

  editUser(user) {
    this.form = {
      Nom: user.Nom,
      Prenom: user.Prenom,
      Phone: user.Phone,
      mot_de_passe: '',
      role: user.role,
    };
    this.currentId = user.id;
    this.isEditing = true;
  },

  resetForm() {
    this.form = {
      Nom: '',
      Prenom: '',
      Phone: '',
      mot_de_passe: '',
      role: '',
    };
    this.errors = {};
    this.currentId = null;
    this.isEditing = false;
  },

  confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce pharmacien ? Cette action est irréversible.')) {
      this.deleteUser(id);
    }
  },

  async toggleStatus(id) {
    try {
      const response = await fetch(`http://127.0.0.1:8000/api/users/${id}/toggle`, {
        method: 'PUT',
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });

      if (!response.ok) throw new Error('Erreur de bascule');

      this.fetchUsers();
      alert('Statut mis à jour avec succès !');
    } catch (err) {
      alert("Erreur lors de l'activation/désactivation.");
    }
  },
},

  computed: {
    filteredUsers() {
      return this.users.filter(user => {
        const rechercheLower = this.recherche.toLowerCase();
        const matchSearch = [user.Nom, user.Prenom, user.Phone].some(field =>
          field.toLowerCase().includes(rechercheLower)
        );
        const matchRole = this.filters.role ? user.role === this.filters.role : true;
        const matchActif = this.filters.actif !== '' ? user.actif == this.filters.actif : true;
        return matchSearch && matchRole && matchActif;
      });
    },
  },
};
</script>

<style scoped>
h2 {
  font-family: 'ENGRAVERS MT', serif;
}
</style>
