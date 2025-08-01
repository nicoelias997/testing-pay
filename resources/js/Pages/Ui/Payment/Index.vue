<template>
  <v-container class="py-12">
    <v-card class="mx-auto pa-6" max-width="600" elevation="10">
      <v-card-title class="text-h5 font-weight-bold">🚀 Plataforma de Pagos</v-card-title>
      <v-card-subtitle class="mb-6">
        Esta es una simulación de prueba. Los pagos aún no son reales, pero podrían ser validados en un entorno de producción.
      </v-card-subtitle>

      <v-form @submit.prevent="submitPayment">
        <v-text-field
          v-model="form.amount"
          label="¿How much you want to pay?"
          placeholder="5,00"
          required
        />
         <v-select
          v-model="form.currency"
          :items="props.currencies"
          item-value="iso"
          item-title="name"
          label="Currency"
        ></v-select>
        <v-divider class="my-4" />

        <div class="mb-2 text-subtitle-1 font-weight-medium">Selecciona el método de pago:</div>
        
          <div class="d-flex flex-row gap-4">
            <v-card
              v-for="method in props.paymentMethods"
              :key="method.id"
              :class="{ 'border-primary': form.payment_platform === method.id }"
              class="pa-2"
              max-width="120"
              elevation="2"
              @click="form.payment_platform = method.id, form.selectedPayment = method.name"
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
        
        <component
          :is="selectedComponent"
          :form="form"
          :onSubmit="handleMercadoPagoSubmit"
          class="mt-4"
        />
        <v-btn
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
import MercadoPagoForm from '../../../Components/Service/MercadoPagoInfo.vue'

import Swal from 'sweetalert2'
import { useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue';

const selectedComponent = computed(() => {
  switch (form.selectedPayment) {
    case 'Stripe':
      return StripeInfo
    case 'PayPal':
      return PayPalInfo
    case 'Mercado Pago':
      return MercadoPagoForm
    default:
      return null
  }
})

const form = useForm({
  amount: '',
  currency: '',
  payment_platform: '',
  selectedPayment: '',
})
const { props } = usePage();


const submitPayment = async () => {
  if (
    !form.amount ||
    !form.currency ||
    !form.payment_platform
  ) {
    Swal.fire({
      icon: 'warning',
      title: 'Campos incompletos',
      text: 'Por favor, completa todos los campos.',
    })
    return;
  }
  if (form.selectedPayment === 'Mercado Pago') {
    return;
  }

  form.post(`/ui/payment/create`, {
    preserveState: true,
    preserveScroll: true,
    onError: () => {
      Swal.fire({
        title: 'Error',
        text: 'No se pudo agregar el proceso.',
        icon: 'error'
      })
    }
  })
}
// MP maneja su propio envío
const handleMercadoPagoSubmit = (extraData) => {
  form.transform((FormData) => ({
    ...FormData,
    ...extraData,
  })).post('/ui/payment/create'), {
    onError: () => {
      Swal.fire('Error', 'No se pudo procesar el pago.', 'error')
    },
    onSuccess: () => {
      Swal.fire('Éxito', 'Pago simulado con Mercado Pago enviado.', 'success')
    }
  }
}
</script>

<style>
.border-primary {
  border: 2px solid #1976d2 !important;
  border-radius: 8px;
}
</style>
