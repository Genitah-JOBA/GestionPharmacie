<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div
      :class="[
        'bg-white w-[90%] max-w-md p-6 rounded-xl shadow-lg text-center animate-fade-in-up border-t-4',
        typeClass.border
      ]"
    >
      <div class="text-3xl mb-2" :class="typeClass.text">{{ icon }}</div>
      <h3 class="text-xl font-semibold mb-2" :class="typeClass.text">{{ title }}</h3>
      <p class="mb-4 text-gray-700">{{ message }}</p>

      <div class="flex justify-center gap-4">
        <button
          @click="$emit('cancel')"
          class="px-5 py-2 rounded-full bg-gray-300 hover:bg-gray-400 text-black"
        >
          {{ cancelText || 'Annuler' }}
        </button>
        <button
          @click="$emit('confirm')"
          :class="[
            'px-5 py-2 rounded-full shadow transition text-white',
            typeClass.button
          ]"
        >
          {{ confirmText || 'Confirmer' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MessageModal',
  props: {
    show: Boolean,
    title: {
      type: String,
      default: 'Message'
    },
    message: {
      type: String,
      default: ''
    },
    type: {
      type: String,
      default: 'info'
    },
    confirmText: {
      type: String,
      default: 'OK'
    },
    cancelText: {
      type: String,
      default: 'Annuler'
    }
  },
  emits: ['confirm', 'cancel'],
  computed: {
    icon() {
      switch (this.type) {
        case 'success': return '✔️'
        case 'error': return '❌'
        default: return 'ℹ️'
      }
    },
    typeClass() {
      switch (this.type) {
        case 'success':
          return {
            border: 'border-green-500',
            button: 'bg-green-600 hover:bg-green-700',
            text: 'text-green-600'
          }
        case 'error':
          return {
            border: 'border-red-500',
            button: 'bg-red-600 hover:bg-red-700',
            text: 'text-red-600'
          }
        default:
          return {
            border: 'border-blue-500',
            button: 'bg-blue-600 hover:bg-blue-700',
            text: 'text-blue-600'
          }
      }
    }
  }
}
</script>

<style scoped>
@keyframes fade-in-up {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.3s ease-out;
}
</style>
