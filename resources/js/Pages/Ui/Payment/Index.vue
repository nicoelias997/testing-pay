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
              :class="{ 'border-primary': form.payment_platform === method.name }"
              class="pa-2"
              max-width="120"
              elevation="2"
              @click="form.payment_platform = method.name"
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
          <v-alert
          v-if="selectedMethod"
          type="info"
          class="mt-4"
          border="start"
          border-color="primary"
        >
          Serás redirigido a <strong>{{ selectedMethod }}</strong> para completar tu donación de forma segura.
        </v-alert>
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
import Swal from 'sweetalert2'
import { useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue';

const form = useForm({
  amount: '',
  currency: '',
  payment_platform: '',
 
})
const { props } = usePage();

const selectedMethod = computed(() => form.payment_platform)

const submitPayment = () => {
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
  form.post(`/ui/payment/create`, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Proceso agregado',
        html: `✅ ¡Pago exitoso con <b>${form.payment_platform.toUpperCase()}</b>!`,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
      })
      form.reset()
    },
    onError: (e) => {
      console.log(e)
      Swal.fire({
        title: 'Error',
        text: 'No se pudo agregar el proceso.',
        icon: 'error'
      })
    }
  })
}
</script>

<style>
.border-primary {
  border: 2px solid #1976d2 !important;
  border-radius: 8px;
}
</style>
