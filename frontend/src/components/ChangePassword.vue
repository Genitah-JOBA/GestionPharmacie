<template>
  <div class="password-container">
    <form @submit.prevent="handleChangePassword" class="password-form">
      <h2 class="form-title">
         Changer le mot de passe
        <span v-if="userNom" class="user-tag">| {{ userNom }}</span>
      </h2>

      <div class="form-group">
        <label>Ancien mot de passe</label>
        <input type="password" v-model="oldPassword" class="form-input" required />
      </div>

      <div class="form-group">
        <label>Nouveau mot de passe</label>
        <input type="password" v-model="newPassword" class="form-input" required />
      </div>

      <div class="form-group">
        <label>Confirmer le nouveau mot de passe</label>
        <input type="password" v-model="confirmPassword" class="form-input" required />
      </div>

      <button :disabled="loading" type="submit" class="submit-btn">
        {{ loading ? 'Modification...' : 'Modifier' }}
      </button>

      <transition name="fade">
        <p v-if="message" class="message success">
          ✅ {{ message }}
        </p>
      </transition>

      <p v-if="error" class="message error">{{ error }}</p>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      oldPassword: '',
      newPassword: '',
      confirmPassword: '',
      message: '',
      error: '',
      loading: false,
      userNom: '',
    };
  },
  methods: {
    async handleChangePassword() {
      this.message = '';
      this.error = '';
      this.loading = true;

      if (this.newPassword.length < 6) {
        this.error = 'Le mot de passe doit contenir au moins 6 caractères.';
        this.loading = false;
        return;
      }

      if (this.newPassword !== this.confirmPassword) {
        this.error = 'Les nouveaux mots de passe ne correspondent pas.';
        this.loading = false;
        return;
      }

      try {
        const token = localStorage.getItem('token');
        const res = await fetch('http://127.0.0.1:8000/api/doit_changer_mdp', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          body: JSON.stringify({
            old_password: this.oldPassword,
            new_password: this.newPassword,
          }),
        });

        const data = await res.json();
        this.loading = false;

        if (!res.ok) {
          this.error = data.message || 'Une erreur est survenue.';
        } else {
          this.message = data.message || 'Mot de passe modifié avec succès.';
          setTimeout(() => {
            this.$router.push('/AccueilPharmacien');
          }, 2000);
        }
      } catch (e) {
        this.loading = false;
        this.error = 'Erreur réseau.';
      }
    },
  },
  mounted() {
    const user = JSON.parse(localStorage.getItem('user'));
    this.userNom = user?.nom || 'Utilisateur';
  },
};
</script>

<style scoped>
.password-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 90vh;
  background: linear-gradient(to bottom right, #f0fdf4, #e0f2fe);
  padding: 20px;
}

.password-form {
  background: white;
  padding: 30px;
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  max-width: 420px;
  width: 100%;
  animation: fadeIn 0.6s ease;
}

.form-title {
  font-size: 22px;
  font-weight: bold;
  color: #1dd89a;
  text-align: center;
  margin-bottom: 20px;
}

.user-tag {
  display: block;
  font-size: 14px;
  color: #4b5563;
  margin-top: 4px;
  font-weight: normal;
}

.form-group {
  margin-bottom: 15px;
}

label {
  font-weight: 600;
  margin-bottom: 6px;
  display: block;
  color: #374151;
}

.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 16px;
  outline: none;
  transition: border 0.2s;
}

.form-input:focus {
  border-color: #3bf6a8;
}

.submit-btn {
  background-color: #25eba9;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  width: 100%;
  cursor: pointer;
  transition: background 0.3s;
}

.submit-btn:hover {
  background-color: #1eaf4e;
}

.message {
  margin-top: 15px;
  text-align: center;
  font-weight: 500;
}

.success {
  color: #059669;
  font-size: 16px;
}

.error {
  color: #dc2626;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* transition animée */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
