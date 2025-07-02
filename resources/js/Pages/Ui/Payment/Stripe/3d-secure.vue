<template>
  <v-container class="d-flex flex-column align-center justify-center" style="height: 100vh">
    <v-card max-width="500" class="pa-6 elevation-12 rounded-xl">
      <v-card-title class="text-h6 mb-4">Autenticación de Pago (3D Secure)</v-card-title>

      <v-alert v-if="errorMessage" type="error" variant="tonal" class="mb-4">
        {{ errorMessage }}
      </v-alert>

      <v-btn block color="primary" @click="confirmPayment" :loading="loading">
        Confirmar Pago
      </v-btn>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { loadStripe } from '@stripe/stripe-js'
import Swal from 'sweetalert2'

const { props } = usePage()
const errorMessage = ref('')
const loading = ref(false)

let stripe = null

onMounted(async () => {
  stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY)
})

async function confirmPayment() {
  loading.value = true

  const { error, paymentIntent } = await stripe.confirmCardPayment(props.clientSecret)

  if (error) {
    Swal.fire({
        title: 'Pago cancelado, vuelve a intentarlo.',
        html: `<p>${error.message}</p>`,
        icon: 'error',
        timer: 3000,
        showConfirmButton: false
      }).then(() => {
        router.visit('/ui/payment')
      })
  } else if(paymentIntent.status === 'succeeded'){
      Swal.fire({
        title: '✅ ¡Pago exitoso!',
        html: `Gracias por tu colaboración.`,
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
      }).then(() => {
        router.visit('/ui/payment')
      })
  } else {
    Swal.fire({
        title: 'Pago denegado, vuelve a intentarlo si asi lo deseas.',
        icon: 'warning',
        timer: 3000,
        showConfirmButton: false
      }).then(() => {
        router.visit('/ui/payment')
      })
  }
  loading.value = false
}
</script>
