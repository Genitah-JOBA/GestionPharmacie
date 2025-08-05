import './assets/main.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import { createPinia } from 'pinia'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher
console.log('Pusher key:', import.meta.env.VITE_PUSHER_APP_KEY)
console.log(import.meta.env)

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
})

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

const savedTheme = localStorage.getItem('theme') || 'light'
if (savedTheme === 'dark') {
  document.documentElement.classList.add('dark')
} else {
  document.documentElement.classList.remove('dark')
}

axios.defaults.baseURL = 'http://127.0.0.1:8000/api'

app.mount('#app')
