<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-800 p-6">
    <div class="relative p-10 max-w-sm w-11/12 rounded-[50px] text-center overflow-hidden bg-green-200 dark:bg-green-800/50 shadow-lg">
      <h1 class="text-3xl font-extrabold font-engravers mb-4 text-black dark:text-white">Accès refusé</h1>
      <p class="mb-6 text-gray-800 dark:text-gray-300 text-lg leading-snug font-serif">
        Vous n'avez pas la permission d'accéder à cette page.
      </p>
      <router-link to="/AccueilPharmacien" class="underline text-black dark:text-white font-serif hover:text-green-700 dark:hover:text-green-300">
        Retour à l'accueil
      </router-link>
    </div>
  </div>
</template>

<style scoped>
.cadre{
  position: relative;
  padding: 40px;
  max-width: 400px;
  width: 90%;
  background: rgba(0, 247, 16, 0.527);
  text-align: center;
  overflow: hidden;
  border-radius: 50px;
}
p{
  padding: 10%;
}
.lien{
  color: #0c0b0a;
  text-decoration: underline;
  font-family: serif;
}
.text {
  margin-bottom: 1.5rem;
  color: #34495e;
  font-size: 1.25rem;
  line-height: 1.25;
  min-height: 90px;
  color: #0c0b0a;
  font-weight: lighter;
  margin-top: -0.5rem;
  font-family: serif;
}
.texte {
  font-size: 2rem;
  font-family: ENGRAVERS TM;
}
*{
  margin: 0;
}
</style>
  