<template>
  <v-container fluid class="payment-container">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="payment-card" elevation="8">
          <v-card-title class="payment-card-header">
            <v-icon :icon="mdiCreditCardMultiple" size="32" class="mr-3"></v-icon>
            <span class="text-h5 font-weight-bold">Plataforma de Pagos</span>
          </v-card-title>
          <v-card-subtitle class="px-5 py-2 text-body-2">
            Selecciona el monto, moneda y método de pago para realizar una transacción de prueba.
          </v-card-subtitle>

          <v-divider></v-divider>

          <v-card-text class="pa-5">
            <v-form @submit.prevent="submitPayment">
              <v-text-field
                v-model="form.amount"
                label="Monto a pagar"
                placeholder="100.00"
                type="number"
                step="0.01"
                variant="outlined"
                :prepend-inner-icon="mdiCash"
                color="primary"
                class="mb-3"
                density="comfortable"
                required
              />

              <v-select
                v-model="form.currency"
                :items="props.currencies"
                item-value="iso"
                item-title="name"
                label="Moneda"
                variant="outlined"
                :prepend-inner-icon="mdiCurrencyUsd"
                color="primary"
                class="mb-3"
                density="comfortable"
              />

              <v-divider class="my-4"></v-divider>

              <div class="mb-3">
                <h3 class="text-subtitle-1 font-weight-medium mb-2">
                  <v-icon :icon="mdiWallet" size="20" class="mr-1"></v-icon>
                  Método de pago
                </h3>
              </div>

              <div class="payment-methods-grid">
                <v-card
                  v-for="method in props.paymentMethods"
                  :key="method.id"
                  :class="{ 'payment-method-selected': form.payment_platform === method.id }"
                  class="payment-method-card"
                  elevation="2"
                  @click="selectPaymentMethod(method)"
                >
                  <v-img
                    :src="method.image"
                    height="70"
                    class="payment-method-img"
                    cover
                  />
                  <div v-if="form.payment_platform === method.id" class="selected-indicator">
                    <v-icon :icon="mdiCheckCircle" color="white" size="20"></v-icon>
                  </div>
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
                class="mt-4"
                disabled
                color="primary"
                size="large"
                block
                elevation="2"
              >
                <v-icon :icon="mdiLock" class="mr-2"></v-icon>
                Simular Pago
              </v-btn>

              <v-btn
                v-else-if="form.selectedPayment"
                type="submit"
                class="mt-4"
                color="primary"
                size="large"
                block
                elevation="2"
              >
                <v-icon :icon="mdiCreditCardCheck" class="mr-2"></v-icon>
                Procesar Pago
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
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
import {
  mdiCreditCardMultiple,
  mdiCash,
  mdiCurrencyUsd,
  mdiWallet,
  mdiCheckCircle,
  mdiCreditCardCheck,
  mdiLock
} from '@mdi/js'

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

<style scoped lang="scss">
.payment-container {
  padding: 24px 16px !important;
  min-height: 100%;
  display: flex;
  align-items: flex-start;
  padding-top: 32px !important;
}

.payment-card {
  border-radius: 16px !important;
  overflow: hidden;
  width: 100%;
}

.payment-card-header {
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
  color: white !important;
  padding: 20px !important;
  display: flex;
  align-items: center;
}

.payment-methods-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 16px;
  margin-bottom: 16px;
}

.payment-method-card {
  position: relative;
  cursor: pointer;
  border-radius: 12px !important;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  overflow: hidden;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
  }
}

.payment-method-selected {
  border: 2px solid #1976d2 !important;
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3) !important;
}

.payment-method-img {
  padding: 12px;
}

.selected-indicator {
  position: absolute;
  top: 6px;
  right: 6px;
  background: #1976d2;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

@media (max-width: 600px) {
  .payment-container {
    padding: 16px 12px !important;
    padding-top: 20px !important;
  }

  .payment-card-header {
    padding: 16px !important;
    flex-direction: column;
    text-align: center;

    .v-icon {
      margin-bottom: 8px;
    }
  }

  .payment-methods-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}

@media (min-width: 960px) {
  .payment-container {
    padding-top: 48px !important;
  }
}
</style>