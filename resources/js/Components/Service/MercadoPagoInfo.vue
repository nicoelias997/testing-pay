<template>
  <div class="mt-4">
    <v-alert
      type="warning"
      border="start"
      border-color="deep-orange"
      class="mb-4"
    >
      Estás usando <strong>Mercado Pago</strong>. Por favor, completa la información para simular un pago.
    </v-alert>

    <v-alert
      v-if="sdkLoaded && !isFormReady && (!props.form.amount || parseFloat(props.form.amount) <= 0)"
      type="info"
      border="start"
      border-color="blue"
      class="mb-4"
    >
      <v-icon icon="mdi-information" class="mr-2"></v-icon>
      Por favor, ingresa un <strong>monto válido</strong> arriba para cargar el formulario de pago.
    </v-alert>

    <div v-if="!sdkLoaded" class="text-center py-8">
      <v-progress-circular indeterminate color="primary" />
      <p class="mt-4">Cargando Mercado Pago...</p>
    </div>

    <div v-else-if="sdkLoaded && !isFormReady && (!props.form.amount || parseFloat(props.form.amount) <= 0)" class="text-center py-8">
      <v-icon icon="mdi-cash-multiple" size="64" color="grey-lighten-1"></v-icon>
      <p class="mt-4 text-grey">Esperando monto...</p>
    </div>

    <form v-else-if="sdkLoaded" :id="formId" @submit.prevent="handleSubmit">
      <div class="mb-3">
        <label :for="`${formId}__cardNumber`">Número de tarjeta</label>
        <div :id="`${formId}__cardNumber`" class="mp-field" />
      </div>

      <div class="mb-3">
        <label :for="`${formId}__expirationDate`">Fecha de vencimiento</label>
        <div :id="`${formId}__expirationDate`" class="mp-field" />
      </div>

      <div class="mb-3">
        <label :for="`${formId}__securityCode`">Código de seguridad</label>
        <div :id="`${formId}__securityCode`" class="mp-field" />
      </div>

      <div class="mb-3">
        <label :for="`${formId}__cardholderName`">Nombre del titular</label>
        <input
          type="text"
          :id="`${formId}__cardholderName`"
          class="v-input"
          value="APRO"
        />
      </div>

      <div class="mb-3">
        <label :for="`${formId}__cardholderEmail`">Correo electrónico</label>
        <input
          type="email"
          :id="`${formId}__cardholderEmail`"
          class="v-input"
          value="test@testuser.com"
        />
      </div>

      <div class="mb-3">
        <label :for="`${formId}__identificationType`">Tipo de documento</label>
        <select :id="`${formId}__identificationType`" class="v-input" />
      </div>

      <div class="mb-3">
        <label :for="`${formId}__identificationNumber`">Número de documento</label>
        <input
          type="text"
          :id="`${formId}__identificationNumber`"
          class="v-input"
          value="12345678"
        />
      </div>

      <div class="mb-3">
        <label :for="`${formId}__issuer`">Banco emisor</label>
        <select :id="`${formId}__issuer`" class="v-input" />
      </div>

      <div class="mb-3">
        <label :for="`${formId}__installments`">Cuotas</label>
        <select :id="`${formId}__installments`" class="v-input" />
      </div>

      <v-btn
        color="primary"
        type="submit"
        :loading="loading"
        :disabled="!isFormReady"
        block
      >
        Pagar con Mercado Pago
      </v-btn>
    </form>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, computed, watch } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  onSubmit: {
    type: Function,
    required: true
  }
})

const loading = ref(false)
const isFormReady = ref(false)
const sdkLoaded = ref(false)
const instanceId = ref(Date.now())
const lastInitializedAmount = ref(null)

const formId = computed(() => `form-checkout-${instanceId.value}`)

let cardFormInstance = null
let scriptElement = null

// Watch for amount changes and re-initialize the form
watch(() => props.form.amount, (newAmount, oldAmount) => {
  // Only reinitialize if:
  // 1. SDK is loaded
  // 2. New amount is valid
  // 3. Amount changed significantly (to avoid unnecessary reloads)
  if (sdkLoaded.value && newAmount && parseFloat(newAmount) > 0) {
    const amountDifference = Math.abs(parseFloat(newAmount) - (lastInitializedAmount.value || 0))

    // Reinitialize if amount changed by more than 0.01 or if never initialized
    if (!lastInitializedAmount.value || amountDifference > 0.01) {
      console.log('💰 Amount changed, reinitializing form:', newAmount)
      reinitializeCardForm()
    }
  }
})

// CRÍTICO: Limpiar cuando el componente se desmonta (al cambiar de método de pago)
onBeforeUnmount(() => {
  console.log('🔄 Componente MP desmontándose...')
  cleanupCompletely()
})

onMounted(() => {
  console.log('🚀 Componente MP montado')
  loadMercadoPagoSDK()
})

// Limpieza COMPLETA del SDK y todas las instancias
function cleanupCompletely() {
  console.log('🧹 Limpieza COMPLETA iniciada...')
  
  // 1. Limpiar instancia del cardForm
  if (cardFormInstance) {
    cardFormInstance = null
    console.log('  ✓ CardForm instance limpiada')
  }
  
  // 2. Limpiar iframes del formulario actual
  const form = document.getElementById(formId.value)
  if (form) {
    const iframes = form.querySelectorAll('iframe')
    iframes.forEach(iframe => {
      iframe.remove()
      console.log('  ✓ iframe removido:', iframe.id)
    })
  }
  
  // 3. Limpiar TODOS los iframes de MP en el documento
  const allMPIframes = document.querySelectorAll('iframe[src*="mercadopago"]')
  allMPIframes.forEach(iframe => {
    iframe.remove()
    console.log('  ✓ iframe MP global removido')
  })
  
  // 4. Remover el script del SDK del DOM
  if (scriptElement && scriptElement.parentNode) {
    scriptElement.parentNode.removeChild(scriptElement)
    scriptElement = null
    console.log('  ✓ Script del SDK removido')
  }
  
  // 5. Remover TODOS los scripts de MP del documento
  const allMPScripts = document.querySelectorAll('script[src*="mercadopago"]')
  allMPScripts.forEach(script => {
    script.remove()
    console.log('  ✓ Script MP global removido')
  })
  
  // 6. Limpiar variable global de MercadoPago
  if (window.MercadoPago) {
    delete window.MercadoPago
    console.log('  ✓ window.MercadoPago eliminado')
  }
  
  // 7. Resetear estados
  sdkLoaded.value = false
  isFormReady.value = false
  lastInitializedAmount.value = null

  console.log('✅ Limpieza COMPLETA finalizada')
}

function loadMercadoPagoSDK() {
  console.log('📦 Cargando SDK de Mercado Pago...')

  // Asegurar limpieza previa
  cleanupCompletely()

  // Pequeña pausa para asegurar que el DOM se limpió
  setTimeout(() => {
    // Crear nuevo script con timestamp único para evitar cache
    scriptElement = document.createElement('script')
    scriptElement.src = `https://sdk.mercadopago.com/js/v2?nocache=${Date.now()}`
    scriptElement.async = true
    scriptElement.defer = true

    scriptElement.onload = () => {
      console.log('✅ SDK de MP cargado exitosamente')
      sdkLoaded.value = true

      // Only initialize if amount is already present
      if (props.form.amount && parseFloat(props.form.amount) > 0) {
        setTimeout(() => {
          initCardForm()
        }, 100)
      } else {
        console.log('⏳ SDK cargado. Esperando que el usuario ingrese un monto...')
      }
    }

    scriptElement.onerror = () => {
      console.error('❌ Error cargando SDK de MP')
      Swal.fire('Error', 'No se pudo cargar Mercado Pago', 'error')
      sdkLoaded.value = false
    }

    document.head.appendChild(scriptElement)
  }, 50)
}

// Reinitialize the card form with new amount
function reinitializeCardForm() {
  // Clean up existing form instance
  if (cardFormInstance) {
    try {
      cardFormInstance = null
      isFormReady.value = false
      console.log('🔄 Limpiando instancia anterior del formulario...')
    } catch (error) {
      console.warn('⚠️ Error limpiando instancia:', error)
    }
  }

  // Wait a bit for cleanup, then initialize
  setTimeout(() => {
    initCardForm()
  }, 200)
}

function initCardForm() {
  if (!props.form.amount || parseFloat(props.form.amount) <= 0) {
    console.warn('⚠️ Monto inválido:', props.form.amount)
    isFormReady.value = false
    return
  }

  if (!window.MercadoPago) {
    console.error('❌ window.MercadoPago no disponible')
    return
  }

  try {
    isFormReady.value = false

    const amountValue = parseFloat(props.form.amount)
    lastInitializedAmount.value = amountValue

    console.log('🔧 Inicializando CardForm con ID:', formId.value)
    console.log('   Monto:', amountValue)

    const mp = new window.MercadoPago(
      import.meta.env.VITE_MERCADO_PAGO_PUBLIC_KEY,
      { locale: 'es-AR' }
    )

    cardFormInstance = mp.cardForm({
      amount: amountValue.toString(),
      iframe: true,
      form: {
        id: formId.value,
        cardNumber: { 
          id: `${formId.value}__cardNumber`, 
          placeholder: '4509 9535 6623 3704' 
        },
        expirationDate: { 
          id: `${formId.value}__expirationDate`, 
          placeholder: 'MM/YY' 
        },
        securityCode: { 
          id: `${formId.value}__securityCode`, 
          placeholder: '123' 
        },
        cardholderName: { id: `${formId.value}__cardholderName` },
        cardholderEmail: { id: `${formId.value}__cardholderEmail` },
        identificationType: { id: `${formId.value}__identificationType` },
        identificationNumber: { id: `${formId.value}__identificationNumber` },
        issuer: { id: `${formId.value}__issuer` },
        installments: { id: `${formId.value}__installments` }
      },
      callbacks: {
        onFormMounted: error => {
          if (error) {
            console.error('❌ Error montando formulario MP:', error)
            Swal.fire('Error', 'No se pudo inicializar el formulario de pago', 'error')
            isFormReady.value = false
          } else {
            console.log('✅ Formulario MP inicializado y listo')
            isFormReady.value = true
          }
        },
        onFormUnmounted: error => {
          if (error) {
            console.error('❌ Error desmontando formulario:', error)
          } else {
            console.log('🔄 Formulario MP desmontado')
          }
        }
      }
    })
  } catch (error) {
    console.error('❌ Error en initCardForm:', error)
    Swal.fire('Error', error.message || 'Error al inicializar el formulario', 'error')
  }
}

async function handleSubmit() {
  if (!cardFormInstance || !isFormReady.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Formulario no listo',
      text: 'Por favor espera a que el formulario cargue completamente'
    })
    return
  }

  try {
    loading.value = true
    console.log('💳 Generando token de pago...')
    
    const cardFormData = await cardFormInstance.getCardFormData()

    if (!cardFormData?.token) {
      throw new Error('No se pudo generar el token de pago')
    }

    console.log('✅ Token generado exitosamente:', cardFormData.token)

    const paymentData = {
      token: cardFormData.token,
      issuer_id: cardFormData.issuerId,
      payment_method_id: cardFormData.paymentMethodId,
      transaction_amount: parseFloat(props.form.amount),
      installments: parseInt(cardFormData.installments),
      description: 'Simulación de pago',
      payer: {
        email: cardFormData.cardholderEmail,
        identification: {
          type: cardFormData.identificationType,
          number: cardFormData.identificationNumber
        }
      }
    }

    console.log('📤 Enviando datos de pago:', paymentData)
    
    await props.onSubmit(paymentData)

  } catch (error) {
    console.error('❌ Error procesando pago:', error)
    Swal.fire({
      icon: 'error',
      title: 'Error al procesar',
      text: error.message || 'No se pudo procesar el pago. Intenta nuevamente.'
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.v-input {
  display: block;
  width: 100%;
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background: white;
}

.mp-field {
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 10px;
  height: 44px;
  background: white;
}

.mb-3 {
  margin-bottom: 16px;
}
</style>