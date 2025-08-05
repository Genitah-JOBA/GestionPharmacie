import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useModalStore = defineStore('modal', () => {
  const show = ref(false)
  const title = ref('Message')
  const message = ref('')
  const type = ref('info')
  const confirmCallback = ref(null)
  const cancelCallback = ref(null)
  const confirmText = ref('OK')       
  const cancelText = ref('Annuler')   

  function open({ title: t, message: m, type: tp = 'info', onConfirm, onCancel, confirmTxt, cancelTxt }) {
    title.value = t
    message.value = m
    type.value = tp
    confirmCallback.value = onConfirm || null
    cancelCallback.value = onCancel || null
    confirmText.value = confirmTxt || 'OK'     
    cancelText.value = cancelTxt || 'Annuler'  
    show.value = true
  }

  function close() {
    show.value = false
  }

  function confirm() {
    confirmCallback.value?.()
    close()
  }

  function cancel() {
    cancelCallback.value?.()
    close()
  }

  return {
    show,
    title,
    message,
    type,
    confirmText,    
    cancelText,     
    open,
    close,
    confirm,
    cancel
  }
})
