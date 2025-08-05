import { createRouter, createWebHistory } from 'vue-router'

import Login from '../views/Login.vue'
import AdminDashboard from '../components/AdminDashboard.vue'
import AccueilPharmacien from '../components/AccueilPharmacien.vue'
import Medicaments from '../components/medicaments.vue'
import GestionVentes from '../components/GestionVentes.vue'
import Unauthorized from '../views/autorisation.vue'
import Facture from '../components/Facture.vue'
import SingUp from '../components/SingUp.vue'
import Utilisation from '../components/Utilisation.vue'
import Notifications from '../views/Notifications.vue'
import ArchivesVentes from '../views/ArchivesVentes.vue'
import Parametres from '../views/Parametres.vue'
import ModalMessage from '../views/ModalMessage.vue'
import MotDePasseOublie from '../views/MotDePasseOublie.vue'
import NouveauMotDePasse from '../views/NouveauMotDePasse.vue'
import VerificationCode from '../views/VerificationCode.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/SingUp',
    name: 'SingUp',
    component: SingUp,
  },
  {
    path: '/Utilisation',
    name: 'Utilisation',
    component: Utilisation,
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, roles: ['Administrateur'] }
  },
  {
    path: '/AccueilPharmacien',
    name: 'AccueilPharmacien',
    component: AccueilPharmacien,
    meta: { requiresAuth: true }
  },
  
  {
    path: '/medicaments',
    name: 'Medicaments',
    component: Medicaments,
    meta: { requiresAuth: true }
  },
  {
    path: '/ventes',
    name: 'GestionVentes',
    component: GestionVentes,
    meta: { requiresAuth: true, roles: ['Pharmacien'] }
  },
  {
    path: '/notifications',
    name: 'Notifications',
    component: Notifications,
    meta: { requiresAuth: true }
  },
  {
    path: '/archives-ventes',
    name: 'ArchivesVentes',
    component: ArchivesVentes,
    meta: { requiresAuth: true }
  },
  {
    path: '/parametres',
    name: 'Parametres',
    component: Parametres,
    meta: { requiresAuth: true }
  },
  {
    path: '/facture/:id',
    name: 'Facture',
    component: Facture
  },
  {
    path: '/autorisation',
    name: 'Autorisation',
    component: Unauthorized
  },
  {
    path: '/medicaments/faible-frequence',
    name: 'FaibleFrequence',
    component: () => import('../views/FaibleFrequence.vue')
  },
  {
    path: '/archives/ventes/:nomComplet',
    name: 'ArchivesVentesDetails',
    component: () => import('@/views/ArchivesVentesDetails.vue'),
  },
  {
    path: '/MotDePasseOublie',
    name: 'MotDePasseOublie',
    component: () => import('@/views/MotDePasseOublie.vue'),
  },
  {
    path: '/NouveauMotDePasse',
    name: 'NouveauMotDePasse',
    component: () => import('@/views/NouveauMotDePasse.vue'),
  },
  {
    path: '/VerificationCode',
    name: 'VerificationCode',
    component: () => import('@/views/VerificationCode.vue'),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const userRole = localStorage.getItem('role');

  if (!token && to.meta.requiresAuth) {
    return next('/login');
  }

  if (to.meta.requiresAuth && to.meta.roles && !to.meta.roles.includes(userRole)) {
    return next('/autorisation');
  }

  next();
});

export default router;
