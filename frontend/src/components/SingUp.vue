<template>
  <div class="page-container">
    <div class="form-container">
      <form @submit.prevent="handleRegister" autocomplete="off" class="form-card">
        <h2 class="connexion-title text-green-700 text-2xl font-bold mb-6 text-center">Inscription</h2>

        <div class="fields-group space-y-4">
          <div class="field">
            <label for="nom" class="block font-medium text-sm text-gray-700">Nom *</label>
            <input
              id="nom"
              v-model="Nom"
              type="text"
              placeholder="Entrez votre nom"
              :class="{ 'input-error': errors.nom }"
              class="input-field"
            />
            <small v-if="errors.nom" class="error-text">{{ errors.nom }}</small>
          </div>

          <div class="field">
            <label for="prenom" class="block font-medium text-sm text-gray-700">Prénom</label>
            <input
              id="prenom"
              v-model="Prenom"
              type="text"
              placeholder="Entrez votre prénom"
              :class="{ 'input-error': errors.prenom }"
              class="input-field"
            />
            <small v-if="errors.prenom" class="error-text">{{ errors.prenom }}</small>
          </div>

          <div class="field">
            <label for="telephone" class="block font-medium text-sm text-gray-700">Téléphone *</label>
            <input
              id="telephone"
              v-model="Phone"
              type="text"
              placeholder="Ex: 0341234567"
              :class="{ 'input-error': errors.telephone }"
              class="input-field"
            />
            <small v-if="errors.telephone" class="error-text">{{ errors.telephone }}</small>
          </div>

          <div class="field">
            <label for="motdepasse" class="block font-medium text-sm text-gray-700">Mot de passe *</label>
            <input
              id="motdepasse"
              v-model="mot_de_passe"
              type="password"
              placeholder="••••••"
              :class="{ 'input-error': errors.motdepasse }"
              class="input-field"
            />
            <small v-if="errors.motdepasse" class="error-text">{{ errors.motdepasse }}</small>
          </div>

          <div class="field">
            <label for="role" class="block font-medium text-sm text-gray-700">Rôle *</label>
            <select v-model="role" id="role" class="input-field">
              <option disabled value="">Sélectionnez un rôle</option>
              <option value="admin">Administrateur</option>
              <option value="pharmacien">Pharmacien</option>
            </select>
            <small v-if="errors.role" class="error-text">{{ errors.role }}</small>
          </div>
        </div>

        <button type="submit" :disabled="loading" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded mt-6">
          <span v-if="loading">
            <span class="loader mr-2"></span> Inscription en cours...
          </span>
          <span v-else>S'inscrire</span>
        </button>

        <div class="mt-6 text-center text-sm text-gray-500">
          Déjà inscrit ?
          <router-link to="/login" class="text-green-600 font-medium hover:underline">Se connecter</router-link>
        </div>
      </form>
    </div>

    <div class="image-container" aria-hidden="true"></div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      Nom: "",
      Prenom: "",
      Phone: "",
      mot_de_passe: "",
      role: "",
      loading: false,
      errors: {},
    };
  },
  methods: {
    validateInputs() {
      const namePattern = /^[A-Za-zÀ-ÿ\s-]+$/;
      const phonePattern = /^(032|033|034|037|038)[0-9]{7}$/;
      this.errors = {};

      if (!this.Nom.trim()) this.errors.nom = "Le nom est obligatoire.";
      else if (!namePattern.test(this.Nom)) this.errors.nom = "Nom invalide.";

      if (this.Prenom && !namePattern.test(this.Prenom)) this.errors.prenom = "Prénom invalide.";

      if (!this.Phone.trim()) this.errors.telephone = "Le téléphone est obligatoire.";
      else if (!phonePattern.test(this.Phone)) this.errors.telephone = "Format téléphone invalide.";

      if (!this.mot_de_passe) this.errors.motdepasse = "Mot de passe requis.";
      else if (this.mot_de_passe.length < 6) this.errors.motdepasse = "6 caractères minimum.";

      if (!this.role) this.errors.role = "Veuillez choisir un rôle.";

      return Object.keys(this.errors).length === 0;
    },

    async handleRegister() {
      if (!this.validateInputs()) return;
      this.loading = true;

      try {
        const response = await fetch("http://localhost:8000/api/register", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            Nom: this.Nom,
            Prenom: this.Prenom,
            Phone: this.Phone,
            mot_de_passe: this.mot_de_passe,
            role: this.role,
          }),
        });

        const data = await response.json();
        this.loading = false;

        if (!response.ok) {
          if (data.errors) this.errors = data.errors;
          else alert(data.message || "Erreur lors de l'inscription.");
          return;
        }

        alert("Inscription réussie ! En attente d'activation.");
        this.$router.push("/login");
      } catch (error) {
        this.loading = false;
        alert("Erreur serveur.");
        console.error(error);
      }
    },
  },
};
</script>

<style scoped>
.page-container {
  display: flex;
  height: 100vh;
  align-items: center;
  justify-content: center;
  background: linear-gradient(to right, #e0f7ec, #cce5dc);
}

.form-container,
.image-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 300px;
}

.form-card {
  max-width: 400px;
  width: 100%;
  background: white;
  padding: 2.5rem 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0, 128, 0, 0.15);
}

.input-field {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #ccc;
  outline: none;
  transition: border-color 0.3s ease;
}

.input-field:focus {
  border-color: #38a169;
}

.input-error {
  border-color: red !important;
}

.error-text {
  color: red;
  font-size: 0.75rem;
}

.image-container {
  background: url("@/assets/images/logo.jpg") no-repeat center center;
  background-size: cover;
  height: 100%;
}

.loader {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #38a169;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  animation: spin 1s linear infinite;
  display: inline-block;
  vertical-align: middle;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 768px) {
  .page-container {
    flex-direction: column;
  }

  .image-container {
    width: 100%;
    height: 200px;
  }
}
</style>