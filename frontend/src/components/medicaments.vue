<template>
  <div class="p-4 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-200 min-h-screen">
    <h2 class="text-xl font-bold mb-4">Gestion des médicaments</h2>

    <div class="flex flex-wrap gap-2 mb-4">
      <input
        v-model="filters.search"
        placeholder="Rechercher par nom, dosage ou référence"
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded flex-grow"
      />
      <select
        v-model="filters.frequence"
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-48"
      >
        <option value="">Toutes fréquences</option>
        <option value="Forte">Forte</option>
        <option value="Faible">Faible</option>
      </select>
    </div>

    <h3>***************************************</h3>

    <form
      ref="medicamentForm"
      @submit.prevent="handleSubmit"
      class="mb-6 flex flex-col gap-4 bg-white dark:bg-gray-900 p-6 rounded shadow-md max-w-xl"
    >
      <input
        v-model="form.reference"
        placeholder="Référence"
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full"
        :disabled="isEditing"
        required
      />
      <input
        v-model="form.nom"
        placeholder="Nom"
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full"
        required
      />

      <div>
        <label class="text-sm font-medium">Quantité</label>
        <input
          v-model.number="form.quantite"
          type="number"
          min="0"
          placeholder="Quantité"
          class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full"
          required
        />
      </div>

      <div>
        <label class="text-sm font-medium">Date d'expiration</label>
        <input
          v-model="form.date_expiration"
          type="date"
          class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full"
          required
        />
      </div>

      <input
        v-model="form.dosage"
        placeholder="Dosage"
        class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full"
        required
      />

      <div>
        <label class="text-sm font-medium">Prix Unitaire</label>
        <input
          v-model.number="form.prix_unitaire"
          type="number"
          min="0"
          step="0.01"
          placeholder="Prix"
          class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 p-2 rounded w-full"
          required
        />
      </div>

      <div class="flex gap-4 mt-4">
        <button
          type="submit"
          class="bg-green-600 text-white px-4 py-2 rounded w-full hover:bg-green-700 transition"
        >
          {{ isEditing ? 'Modifier' : 'Ajouter' }}
        </button>
        <button
          type="button"
          v-if="isEditing"
          @click="resetForm"
          class="bg-gray-400 text-white px-4 py-2 rounded w-full hover:bg-gray-500 transition"
        >
          Annuler
        </button>
      </div>
    </form>

    <table class="table-auto w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
      <thead>
        <tr class="bg-gray-200 dark:bg-gray-800">
          <th class="px-4 py-2">Référence</th>
          <th class="px-4 py-2">Nom</th>
          <th class="px-4 py-2">Quantité</th>
          <th class="px-4 py-2">Dosage</th>
          <th class="px-4 py-2">Prix</th>
          <th class="px-4 py-2">Fréquence</th>
          <th class="px-4 py-2">Date expiration</th>
          <th class="px-4 py-2">Date d'ajout</th>
          <th class="px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="med in filteredMedicaments"
          :key="med.reference"
          class="border-t border-gray-300 dark:border-gray-700"
        >
          <td class="px-4 py-2">{{ med.reference }}</td>
          <td class="px-4 py-2">{{ med.nom }}</td>
          <td class="px-4 py-2">{{ med.quantite }}</td>
          <td class="px-4 py-2">{{ med.dosage }}</td>
          <td class="px-4 py-2">{{ med.prix_unitaire }} Ar</td>
          <td class="px-4 py-2">{{ med.frequence_utilisation }}</td>
          <td class="px-4 py-2">{{ med.date_expiration }}</td>
          <td class="px-4 py-2">{{ formatDate(med.created_at) }}</td>
          <td class="px-4 py-2 space-x-1">
            <button
              @click="editMedicament(med)"
              class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 transition"
            >
              ✏️
            </button>
            <button
              @click="deleteMedicament(med.reference)"
              class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition"
            >
              🗑️
            </button>
          </td>
        </tr>
        <tr v-if="filteredMedicaments.length === 0">
          <td colspan="9" class="text-center py-4 text-gray-500 dark:text-gray-400">
            Aucun médicament trouvé
          </td>
        </tr>
      </tbody>
    </table>

    <MessageModal
      :show="modal.show"
      :title="modal.title"
      :message="modal.message"
      :type="modal.type"
      :confirmText="modal.confirmText"
      :cancelText="modal.cancelText"
      @confirm="modal.confirm"
      @cancel="modal.cancel"
    />
  </div>
</template>

<script>
import MessageModal from '@/views/ModalMessage.vue'
import { useModalStore } from '@/stores/ModalStore'

export default {
  components: { MessageModal },
  data() {
    return {
      medicaments: [],
      form: {
        reference: '',
        nom: '',
        quantite: 0,
        date_expiration: '',
        dosage: '',
        prix_unitaire: 0,
        frequence_utilisation: '',
      },
      isEditing: false,
      filters: {
        search: '',
        frequence: '',
      },
    };
  },
  setup() {
    const modal = useModalStore();
    return { modal };
  },
  mounted() {
    this.fetchMedicaments();
  },
  methods: {
    showMessage(message, type = 'error') {
      this.modal.open({
        title: type === 'success' ? 'Succès' : 'Erreur',
        message: message,
        type: type,
      });
    },
    getAuthHeader() {
      const token = localStorage.getItem('token');
      return {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      };
    },
    async fetchMedicaments() {
      try {
        const res = await fetch('http://127.0.0.1:8000/api/medicaments', {
          headers: this.getAuthHeader()
        });

        if (!res.ok) throw new Error('Non autorisé');

        this.medicaments = await res.json();
      } catch (error) {
        this.showMessage('Erreur chargement médicaments ou accès non autorisé.');
        this.$router.push('/autorisation');
      }
    },
    async handleSubmit() {
      const quantite = this.form.quantite;
      this.form.frequence_utilisation = quantite >= 50 ? 'Forte' : 'Faible';

      const today = new Date();
      const expirationDate = new Date(this.form.date_expiration);

      const minExpirationDate = new Date();
      minExpirationDate.setFullYear(today.getFullYear() + 1);

      if (expirationDate < minExpirationDate) {
        this.showMessage('❌ La date d\'expiration doit être au moins 1 an après aujourd\'hui.');
        return;
      }

      const url = this.isEditing
        ? `http://127.0.0.1:8000/api/medicaments/${this.form.reference}`
        : 'http://127.0.0.1:8000/api/medicaments';

      const method = this.isEditing ? 'PUT' : 'POST';

      try {
        const res = await fetch(url, {
          method,
          headers: this.getAuthHeader(),
          body: JSON.stringify(this.form)
        });

        if (!res.ok) {
          const data = await res.json();
          this.showMessage(data.message || '❌ Erreur lors de l\'enregistrement.');
          return;
        }

        this.showMessage(
          this.isEditing ? '✅ Médicament modifié avec succès' : '✅ Médicament ajouté avec succès',
          'success'
        );

        this.resetForm();
        this.fetchMedicaments();
      } catch (error) {
        this.showMessage('❌ Une erreur est survenue lors de l\'enregistrement.');
      }
    },
    async deleteMedicament(reference) {
      this.modal.open({
        title: 'Confirmation',
        message: 'Supprimer ce médicament ?',
        type: 'warning',
        confirmText: 'Oui',
        cancelText: 'Annuler',
        confirm: async () => {
          try {
            const res = await fetch(`http://127.0.0.1:8000/api/medicaments/${reference}`, {
              method: 'DELETE',
              headers: this.getAuthHeader()
            });

            if (!res.ok) {
              const data = await res.json();
              this.showMessage(data.message || '❌ Suppression échouée.');
              return;
            }

            this.showMessage('✅ Médicament supprimé avec succès.', 'success');
            this.fetchMedicaments();
          } catch (error) {
            this.showMessage('❌ Erreur lors de la suppression.');
          }
        }
      });
    },
    editMedicament(med) {
      this.form = { ...med };
      this.isEditing = true;
      this.$nextTick(() => {
        this.$refs.medicamentForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    },
    resetForm() {
      this.form = {
        reference: '',
        nom: '',
        quantite: 0,
        date_expiration: '',
        dosage: '',
        prix_unitaire: 0,
        frequence_utilisation: '',
      };
      this.isEditing = false;
    },
    formatDate(dateStr) {
      if (!dateStr) return 'N/A';
      const d = new Date(dateStr);
      return d.toLocaleDateString() + ' ' + d.toLocaleTimeString();
    }
  },
  computed: {
    filteredMedicaments() {
      return this.medicaments.filter(med => {
        const search = this.filters.search.toLowerCase();
        const matchSearch =
          med.nom.toLowerCase().includes(search) ||
          med.dosage.toLowerCase().includes(search) ||
          med.reference.toLowerCase().includes(search);

        const matchFrequence = this.filters.frequence
          ? med.frequence_utilisation === this.filters.frequence
          : true;

        return matchSearch && matchFrequence;
      });
    }
  }
};
</script>

<style scoped>
table {
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ddd;
}

h2 {
  font-family: 'Engravers MT', sans-serif;
  font-weight: 500;
}

h3 {
  text-align: center;
  padding: 20px;
}

form {
  margin-left: 30%;
  margin-top: -3%;
}
</style>
