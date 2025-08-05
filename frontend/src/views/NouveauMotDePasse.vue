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
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
      <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-blue-800 mb-6 text-center">Nouveau mot de passe</h2>
  
        <form @submit.prevent="submitPassword" class="space-y-5">
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Nouveau mot de passe</label>
            <input
              v-model="password"
              type="password"
              class="w-full px-4 py-3 border rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': error }"
              placeholder="••••••••"
            />
          </div>
  
          <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg"
          >
            Réinitialiser le mot de passe
          </button>
  
          <p v-if="error" class="text-sm text-red-600 mt-2">{{ error }}</p>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        password: '',
        error: ''
      };
    },
    methods: {
      async submitPassword() {
        this.error = '';
        if (this.password.length < 6) {
          this.error = 'Mot de passe trop court';
          return;
        }
  
        const token = localStorage.getItem('reset_token');
        const phone = localStorage.getItem('reset_phone');
  
        if (!token || !phone) {
          this.error = 'Session expirée. Recommencez.';
          this.$router.push('/mot-de-passe-oublie');
          return;
        }
  
        try {
          const response = await fetch("http://localhost:8000/api/reset-password", {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              phone,
              token,
              new_password: this.password
            }),
          });
  
          const result = await response.json();
          if (!response.ok) throw new Error(result.message || 'Erreur');
  
          alert('Mot de passe réinitialisé avec succès.');
          localStorage.removeItem('reset_token');
          localStorage.removeItem('reset_phone');
          this.$router.push('/');
  
        } catch (err) {
          this.error = err.message;
        }
      }
    }
  };
  </script>
  