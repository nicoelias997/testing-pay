<template>
  <v-container class="py-12">
    <v-card class="mx-auto pa-6" max-width="600" elevation="10">
      <v-card-title class="text-h5 font-weight-bold">🚀 Plataforma de Pagos</v-card-title>
      <v-card-subtitle class="mb-6">
        Esta es una simulación de prueba.
      </v-card-subtitle>

      <v-form @submit.prevent="submitPayment">
        <v-text-field
          v-model="form.amount"
          label="¿Cuánto quieres pagar?"
          placeholder="5.00"
          type="number"
          step="0.01"
          required
        />
        <v-select
          v-model="form.currency"
          :items="props.currencies"
          item-value="iso"
          item-title="name"
          label="Moneda"
        />
        <v-divider class="my-4" />

        <div class="mb-2 text-subtitle-1 font-weight-medium">
          Selecciona el método de pago:
        </div>
        
        <div class="d-flex flex-row gap-4">
          <v-card
            v-for="method in props.paymentMethods"
            :key="method.id"
            :class="{ 'border-primary': form.payment_platform === method.id }"
            class="pa-2"
            max-width="120"
            elevation="2"
            @click="selectPaymentMethod(method)"
          >
            <v-img
              :src="method.image"
              width="120"
              height="60"
              class="mx-auto"
              cover
            />
          </v-card>
        </div>
        
        <!-- Key dinámica para destruir completamente el componente -->
        <component
          :is="selectedComponent"
          v-if="selectedComponent"
          :key="componentKey"
          :form="form"
          :onSubmit="handleMercadoPagoSubmit"
          class="mt-4"
        />

        <v-btn
          v-if="form.selectedPayment === 'Mercado Pago' || form.selectedPayment === 'PayU'"
          type="submit"
          class="mt-6"
          disabled
          color="primary"
          size="large"
          block
        >
          Simular Pago
        </v-btn>

        <v-btn
          v-else-if="form.selectedPayment"
          type="submit"
          class="mt-6"
          color="primary"
          size="large"
          block
        >
          Simular Pago
        </v-btn>
      </v-form>
    </v-card>
  </v-container>
</template>

<script setup>
import StripeInfo from '../../../Components/Service/StripeInfo.vue'
import PayPalInfo from '../../../Components/Service/PayPalInfo.vue'
import PayUInfo from '../../../Components/Service/PayUInfo.vue'
import MercadoPagoForm from '../../../Components/Service/MercadoPagoInfo.vue'

import Swal from 'sweetalert2'
import { useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const { props } = usePage()

const form = useForm({
  amount: '',
  currency: '',
  payment_platform: '',
  selectedPayment: '',
})

const componentKey = ref(0) // Key para forzar recreación

const selectedComponent = computed(() => {
  switch (form.selectedPayment) {
    case 'Stripe':
      return StripeInfo
    case 'PayPal':
      return PayPalInfo
    case 'Mercado Pago':
      return MercadoPagoForm
    case 'PayU':
      return PayUInfo
    default:
      return null
  }
})

function selectPaymentMethod(method) {
  form.payment_platform = method.id
  form.selectedPayment = method.name
  
  // Incrementar key para destruir y recrear el componente
  componentKey.value++
}

const submitPayment = async () => {
  if (!form.amount || !form.currency || !form.payment_platform) {
    Swal.fire({
      icon: 'warning',
      title: 'Campos incompletos',
      text: 'Por favor, completa todos los campos.',
    })
    return
  }

  if (form.selectedPayment === 'Mercado Pago') {
    return
  }

  form.post('/ui/payment/create', {
    preserveState: true,
    preserveScroll: true,
    onError: () => {
      Swal.fire('Error', 'No se pudo procesar el pago.', 'error')
    },
    onSuccess: () => {
      Swal.fire('Éxito', 'Pago procesado correctamente.', 'success')
      form.reset()
    }
  })
}

const handleMercadoPagoSubmit = async (mpData) => {
  console.log('📤 Enviando MP:', mpData)

  form.post('/ui/payment/create', {
    data: {
      amount: form.amount,
      currency: form.currency,
      payment_platform: form.payment_platform,
      ...mpData
    },
    preserveState: true,
    preserveScroll: true,
    onError: (errors) => {
      console.error('❌ Errores:', errors)
      Swal.fire('Error', 'No se pudo procesar con Mercado Pago', 'error')
    },
    onSuccess: () => {
      Swal.fire('Éxito', 'Pago con Mercado Pago exitoso', 'success')
      form.reset()
      componentKey.value++ // Resetear componente
    }
  })
}
</script>

<style>
.border-primary {
  border: 2px solid #1976d2 !important;
  border-radius: 8px;
}
.gap-4 {
  gap: 16px;
}
</style>