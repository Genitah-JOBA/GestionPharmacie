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
        <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">Vérifier le code</h2>
        
        <p class="text-sm text-gray-600 mb-4 text-center">
          Un code a été envoyé à votre numéro <span class="font-medium">+261 {{ phone }}</span>
        </p>
  
        <form @submit.prevent="submitCode" class="space-y-5">
          <input
            v-model="code"
            type="text"
            maxlength="6"
            placeholder="Code à 6 chiffres"
            class="w-full px-4 py-3 border rounded-lg border-gray-300 focus:ring-2 focus:ring-green-500"
            :class="{ 'border-red-500': error }"
          />
          <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
  
          <button
            type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg"
          >
            Vérifier
          </button>
        </form>
  
        <p class="mt-6 text-center text-sm text-gray-500">
          <router-link to="/MotDePasseOublie" class="text-green-600 ">Retour</router-link>
        </p>
      </div>
    </div>
  </template>
  
  <script>
import ModalMessage from '@/views/ModalMessage.vue'

  export default {
    data() {
      return {
        code: '',
        error: '',
        showPassword: false,
        showModal: false,
        errorMessage: "",
        errors: {},
        phone: ''
      };
    },
    created() {
      const phone = localStorage.getItem('reset_phone');
      if (!phone) {
        this.$router.push('/mot-de-passe-oublie');
      }
      this.phone = phone?.replace('+261', '');
    },
    methods: {
      async submitCode() {
        this.error = '';
        if (!/^\d{6}$/.test(this.code)) {
          this.error = 'Code invalide (6 chiffres attendus)';
          return;
        }
  
        try {
          const response = await fetch("http://localhost:8000/api/verify-code", {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              phone: '+261' + this.phone,
              code: this.code
            }),
          });
  
          const result = await response.json();
          if (!response.ok) throw new Error(result.message || 'Code incorrect');
          localStorage.setItem('reset_token', result.token);
          this.$router.push('/nouveau-mot-de-passe');
  
        } catch (err) {
          this.error = err.message;
        }
      }
    }
  };
  </script>
  