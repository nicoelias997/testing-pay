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

    <form id="form-checkout" @submit.prevent="handleSubmit">
      <div class="mb-3">
        <label for="form-checkout__cardNumber">Número de tarjeta</label>
        <div id="form-checkout__cardNumber" class="mp-field" />
      </div>

      <div class="mb-3">
        <label for="form-checkout__expirationDate">Fecha de vencimiento</label>
        <div id="form-checkout__expirationDate" class="mp-field" />
      </div>

      <div class="mb-3">
        <label for="form-checkout__securityCode">Código de seguridad</label>
        <div id="form-checkout__securityCode" class="mp-field" />
      </div>

      <div class="mb-3">
        <label for="form-checkout__cardholderName">Nombre del titular</label>
        <input
          type="text"
          id="form-checkout__cardholderName"
          class="v-input"
          value="APRO"
        />
      </div>

      <div class="mb-3">
        <label for="form-checkout__cardholderEmail">Correo electrónico</label>
        <input
          type="email"
          id="form-checkout__cardholderEmail"
          class="v-input"
          value="test@testuser.com"
        />
      </div>

      <div class="mb-3">
        <label for="form-checkout__identificationType">Tipo de documento</label>
        <select id="form-checkout__identificationType" class="v-input" value="DNI" />
      </div>

      <div class="mb-3">
        <label for="form-checkout__identificationNumber">Número de documento</label>
        <input
          type="text"
          id="form-checkout__identificationNumber"
          class="v-input"
          value="12345678"
        />
      </div>

      <div class="mb-3">
        <label for="form-checkout__issuer">Banco emisor</label>
        <select id="form-checkout__issuer" class="v-input" />
      </div>

      <div class="mb-3">
        <label for="form-checkout__installments">Cuotas</label>
        <select id="form-checkout__installments" class="v-input" />
      </div>

      <v-btn
        color="primary"
        type="submit"
        :loading="loading"
        block
      >
        Generar Token y Simular Pago
      </v-btn>
    </form>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
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
let cardFormInstance = null

onMounted(() => {
  if (!window.MercadoPago) {
    const script = document.createElement('script')
    script.src = 'https://sdk.mercadopago.com/js/v2'
    script.onload = () => initCardForm()
    document.head.appendChild(script)
  } else {
    initCardForm()
  }
})

function initCardForm() {
  const mp = new MercadoPago(import.meta.env.VITE_MERCADO_PAGO_PUBLIC_KEY, {
    locale: 'es-AR'
  });

  cardFormInstance = mp.cardForm({
    amount: props.form.amount.toString(),
    iframe: true,
    form: {
      id: 'form-checkout',
      cardNumber: { id: 'form-checkout__cardNumber' },
      expirationDate: { id: 'form-checkout__expirationDate' },
      securityCode: { id: 'form-checkout__securityCode' },
      cardholderName: { id: 'form-checkout__cardholderName' },
      cardholderEmail: { id: 'form-checkout__cardholderEmail' },
      identificationType: { id: 'form-checkout__identificationType' },
      identificationNumber: { id: 'form-checkout__identificationNumber' },
      issuer: { id: 'form-checkout__issuer' },
      installments: { id: 'form-checkout__installments' }
    },
    callbacks: {
      onFormMounted: error => {
        if (error) console.error('Error montando el formulario:', error)
      }
    }
  })
}

async function handleSubmit() {
  try {
    loading.value = true
    const {
      token,
      issuerId,
      paymentMethodId,
      installments,
      identificationType,
      identificationNumber,
      cardholderEmail
    } = cardFormInstance.getCardFormData()

    props.onSubmit({
      token,
      issuer_id: issuerId,
      payment_method_id: paymentMethodId,
      transaction_amount: parseFloat(props.form.amount),
      installments: parseInt(installments),
      description: 'Simulación de producto',
      payer: {
        email: cardholderEmail,
        identification: {
          type: identificationType,
          number: identificationNumber
        }
      }
    })
  } catch (error) {
    console.error(error)
    Swal.fire('Error', 'No se pudo generar el token.', 'error')
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
}
.mp-field {
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 10px;
  height: 44px;
}
.mb-3 {
  margin-bottom: 16px;
}
</style>
