<template>
  <div class="bg-gradient-to-r from-green-600 to-green-800 w-48 h-48 rounded-full mx-auto flex flex-col items-center justify-center text-white text-center p-4">
    <img 
      :src="logo1Img" 
      alt="Logo Pharmacie" 
      class="h-16 w-16 rounded-full border-4 border-white shadow-lg object-cover mb-2"
      @error="handleImageError"
    >
    <div class="flex flex-col">
      <h1 class="text-xl font-bold leading-tight">PHARMACIE CHU</h1>
      <h2 class="text-lg font-bold">ANDRAINJATO</h2>
    </div>
  </div>

  <div class="page-container -mt-20">
    <div class="form-container">  
      <form @submit.prevent="handleLogin" autocomplete="off" class="form-card">
        <h2 class="connexion-title">Connexion</h2>

        <div class="fields-group">
          <div class="field">
            <label for="nom">Nom *</label>
            <input
              id="nom"
              v-model.trim="Nom"
              type="text"
              @input="clearError('nom')"
              placeholder="Entrez votre nom"
              :class="{ 'input-error': errors.nom }"
            />
            <small v-if="errors.nom" class="error-text">{{ errors.nom }}</small>
          </div>

          <div class="field">
            <label for="telephone">Téléphone *</label>
            <input
              id="telephone"
              v-model.trim="Phone"
              type="text"
              @input="clearError('telephone')"
              placeholder="034XXXXXXX"
              autocomplete="off"
              :class="{ 'input-error': errors.telephone }"
            />
            <small v-if="errors.telephone" class="error-text">{{ errors.telephone }}</small>
          </div>

          <div class="field">
            <div class="flex justify-between items-center">
              <label for="password">Mot de passe *</label>
            </div>
            <div class="relative">
              <input
                id="password"
                :type="showPassword ? 'text' : 'password'"
                v-model.trim="mot_de_passe"
                @input="clearError('motdepasse')"
                placeholder="Entrez votre mot de passe"
                class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent transition-all"
                :class="{ 'input-error': errors.motdepasse }"
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <input
                  type="checkbox"
                  id="togglePassword"
                  v-model="showPassword"
                  class="hidden"
                />
                <label for="togglePassword" class="cursor-pointer" :title="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'">
                  <svg
                    v-if="showPassword"
                    class="w-5 h-5 text-green-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5
                         c4.478 0 8.268 2.943 9.542 7
                         -1.274 4.057-5.064 7-9.542 7
                         -4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg
                    v-else
                    class="w-5 h-5 text-gray-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.875 18.825A10.05 10.05 0 0112 19
                        c-4.478 0-8.268-2.943-9.543-7
                        a9.97 9.97 0 011.563-3.029m5.858.908
                        a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242
                        M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                        m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943
                        9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </label>
              </div>
            </div>
            <small v-if="errors.motdepasse" class="error-text">{{ errors.motdepasse }}</small>
          </div>
        </div>

        <div class="pt-2">
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all duration-300 flex justify-center items-center"
            :class="{ 'opacity-75 cursor-not-allowed': loading }"
          >
            <svg
              v-if="loading"
              class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Connexion en cours...' : 'Se connecter' }}</span>
          </button>
        </div>

        <!-- Lien mot de passe oublié
        <div class="text-center pt-4">
          <router-link
            to="/MotDePasseOublie"
            class="text-xs text-green-600 hover:text-green-800 hover:underline font-medium"
          >
            Mot de passe oublié ?
          </router-link>
        </div>-->
      </form>

      <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center">
        <p class="text-xs text-gray-500">© 2025 CHU Andrainjato - Tous droits réservés</p>
      </div>

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
  </div>
</template>

<script>
import logo1Img from '@/assets/images/logo1.jpg'
import logo from '@/assets/images/logo.jpg'
import { useModalStore } from '@/stores/ModalStore'
import MessageModal from '@/views/ModalMessage.vue'

export default {
  components: { MessageModal },
  setup() {
    const modal = useModalStore()
    return { modal }
  },

  data() {
    return {
      Nom: "",
      Phone: "",
      mot_de_passe: "",
      loading: false,
      logo1Img,
      showPassword: false,
      errors: {},
      defaultLogo: logo
    };
  },

  methods: {
    clearError(field) {
      if (this.errors[field]) {
        delete this.errors[field];
      }
    },

    isRequired(value) {
      return value && value.trim().length > 0;
    },

    handleImageError(e) {
      e.target.src = this.defaultLogo;
    },

    minLength(value, length) {
      return value.length >= length;
    },

    validateInputs() {
      const namePattern = /^[A-Za-zÀ-ÿ\s\-]+$/;
      const phonePattern = /^(032|033|034|037|038)\d{7,8}$/;

      this.errors = {};

      if (!this.isRequired(this.Nom)) {
        this.errors.nom = "Le nom est obligatoire.";
      } else if (!namePattern.test(this.Nom)) {
        this.errors.nom = "Le nom ne doit contenir que des lettres.";
      }

      if (!this.isRequired(this.Phone)) {
        this.errors.telephone = "Le téléphone est obligatoire.";
      } else if (!phonePattern.test(this.Phone)) {
        this.errors.telephone = "Format téléphone invalide ";
      }

      if (!this.isRequired(this.mot_de_passe)) {
        this.errors.motdepasse = "Le mot de passe est requis.";
      } else if (!this.minLength(this.mot_de_passe, 6)) {
        this.errors.motdepasse = "6 caractères minimum.";
      }

      return Object.keys(this.errors).length === 0;
    },

    async handleLogin() {
      const modal = useModalStore();

      if (!this.validateInputs()) return;

      this.loading = true;
      try {
        const response = await fetch("http://localhost:8000/api/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          mode: "cors",
          body: JSON.stringify({
            Nom: this.Nom,
            Phone: this.Phone,
            mot_de_passe: this.mot_de_passe,
          }),
        });

        const data = await response.json();
        this.loading = false;

        if (!response.ok) {
          modal.open({
            title: 'Erreur',
            message: data.message || "Erreur de connexion",
            type: 'error'
          });
          return;
        }

        localStorage.setItem("token", data.token);
        localStorage.setItem("user", JSON.stringify(data.user));
        localStorage.setItem("role", data.user.role);

        if (data.user.role === "Administrateur" || data.user.role === "Pharmacien") {
          modal.open({
            title: 'Succès',
            message: 'Connexion réussie. Bienvenue !',
            type: 'success',
            confirmButtonText: 'OK',
            onConfirm: () => {
              this.$router.push("/AccueilPharmacien");
            }
          });
        } else {
          modal.open({
            title: 'Accès refusé',
            message: 'Rôle non autorisé !',
            type: 'warning'
          });
        }

      } catch (error) {
        this.loading = false;
        modal.open({
          title: 'Erreur réseau',
          message: 'Erreur de connexion au serveur. Veuillez réessayer.',
          type: 'error'
        });
        console.error("Erreur de connexion:", error);
      }
    }
  }
};
</script>

<style scoped>
.page-container {
  @apply max-w-md mx-auto p-4;
}

.form-container {
  @apply bg-white rounded-xl shadow-md overflow-hidden;
}

.form-card {
  @apply p-6;
}

.connexion-title {
  @apply text-2xl font-bold text-center text-gray-800 mb-6;
}

.fields-group {
  @apply space-y-4;
}

.field {
  @apply space-y-1;
}

.field label {
  @apply block text-sm font-medium text-gray-700;
}

.field input {
  @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent transition-all;
}

svg {
  transition: all 0.2s ease-in-out;
}

input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
}

.animate-spin {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.input-error {
  border-color: #ef4444;
  border-width: 2px;
}
.input-error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
}
.error-text {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.25rem;
  display: block;
}
</style>
