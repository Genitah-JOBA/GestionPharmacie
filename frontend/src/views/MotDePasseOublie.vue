<template>
  <ModalMessage
    :show="modal.show"
    :title="modal.title"
    :message="modal.message"
    :type="modal.type"
    :confirmText="modal.confirmText"
    :cancelText="modal.cancelText"
    @confirm="modal.confirm"
    @cancel="modal.cancel"
  />

  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 -mt-20">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
      <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Réinitialiser le mot de passe</h2>

      <form @submit.prevent="handleReset" class="space-y-5">
        <div>
          <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
          <div class="relative">
            <input
              v-model="phone"
              type="text"
              id="telephone"
              placeholder="034 XX XXX XX"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent"
              :class="{ 'border-red-500': error }"
            />
          </div>
          <p v-if="error" class="text-sm text-red-600 mt-1">{{ error }}</p>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg shadow transition duration-200"
        >
          <span v-if="loading">Envoi en cours...</span>
          <span v-else>Envoyer un code de réinitialisation</span>
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-500">
        <router-link to="/" class="text-green-600 hover:underline">Retour à la connexion</router-link>
      </p>
    </div>
  </div>
</template>

<script>
import ModalMessage from '@/views/ModalMessage.vue'

export default {
  components: {
    ModalMessage
  },
  data() {
    return {
      phone: '',
      error: '',
      showPassword: false,
      showModal: false,
      errorMessage: "",
      errors: {},
      loading: false,
      modal: {
        show: false,
        title: '',
        message: '',
        type: '',
        confirmText: '',
        cancelText: '',
        confirm: null,
        cancel: null,
      },
    };
  },
  methods: {
    formatPhone(phone) {
      return phone.replace(/\s+/g, '');
    },
    validatePhone(phone) {
      const cleaned = this.formatPhone(phone);
      const regex = /^(032|033|034|037|038)\d{7}$/;
      return regex.test(cleaned);
    },
    async handleReset() {
      this.error = '';

      if (!this.phone.trim()) {
        this.error = "Le téléphone est requis";
        return;
      }

      const formattedPhone = this.formatPhone(this.phone);

      if (!this.validatePhone(formattedPhone)) {
        this.error = "Numéro invalide.";
        return;
      }

      this.loading = true;

      try {
        const response = await fetch('http://localhost:8000/api/password/forgot-password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ phone: formattedPhone }),
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw new Error(result.message || "Erreur lors de l'envoi");
        }

        this.modal.show = true;
        this.modal.title = "Succès";
        this.modal.message = "Un code de réinitialisation a été envoyé par SMS.";
        this.modal.type = "success";
        this.modal.confirmText = "OK";
        this.modal.cancelText = "";
        this.modal.confirm = () => {
          this.modal.show = false;
          this.$router.push('/');
        };

      } catch (err) {
        this.error = err.message || "Erreur inconnue";
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.border-red-500 {
  border-color: #ef4444;
}
</style>
