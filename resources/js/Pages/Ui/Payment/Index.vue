<template>
  <v-container class="py-12">
    <v-card class="mx-auto pa-6" max-width="600" elevation="10">
      <v-card-title class="text-h5 font-weight-bold">🚀 Plataforma de Pagos</v-card-title>
      <v-card-subtitle class="mb-6">
        Esta es una simulación de prueba. Los pagos aún no son reales, pero podrían ser validados en un entorno de producción.
      </v-card-subtitle>

      <v-form @submit.prevent="submitPayment" ref="formRef">
        <v-text-field
          v-model="form.card_number"
          label="Número de Tarjeta"
          placeholder="4242 4242 4242 4242"
          required
        />
        <v-text-field
          v-model="form.expiry"
          label="Expiración (MM/AA)"
          placeholder="12/30"
          required
        />
        <v-text-field
          v-model="form.cvc"
          label="CVC"
          placeholder="123"
          required
        />
          <v-text-field
          v-model="form.amount"
          label="¿Cuanto deseas aportar?"
          placeholder="5.00"
          required
        />
         <v-select
          :items="currencies"
          item-name="name"
          item-value="iso"
          label="Moneda"
        ></v-select>

        <v-divider class="my-4" />

        <div class="mb-2 text-subtitle-1 font-weight-medium">Selecciona el método de pago:</div>
        <div class="d-flex flex-row gap-4">
          <v-card
            v-for="method in paymentMethods"
            :key="method.id"
            :class="{ 'border-primary': form.method === method.name }"
            class="pa-2"
            max-width="120"
            elevation="2"
            @click="form.method = method.name"
          >
            <v-img
              :src="method.image"
              height="60"
              class="mx-auto"
              cover
            />
            <v-card-subtitle class="text-center text-caption mt-1">
              {{ method.name }}
            </v-card-subtitle>
          </v-card>
        </div>

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
import { ref } from 'vue'
import Swal from 'sweetalert2'
import { useForm, usePage } from '@inertiajs/vue3'

const form = useForm({
  card_number: '',
  expiry: '',
  cvc: '',
  method: '',
 
})
const { props } = usePage();

const currencies = props.currencies
const paymentMethods = props.paymentMethods
// const currencies = pageProps.currency;
// console.log(currency)

const submitPayment = () => {
  if (
    !form.value.card_number ||
    !form.value.expiry ||
    !form.value.cvc ||
    !form.value.method
  ) {
    Swal.fire({
      icon: 'warning',
      title: 'Campos incompletos',
      text: 'Por favor, completa todos los campos.',
    })
    return
  }
  router.post(`/ui/payment/create`, form, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Proceso agregado',
        html: `✅ ¡Pago exitoso con <b>${form.value.method.toUpperCase()}</b>!<br><small>(Simulación completada)</small>`,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
      })
    },
    onError: () => {
      Swal.fire({
        title: 'Error',
        text: 'No se pudo agregar el proceso.',
        icon: 'error'
      })
    }
  })

  // Limpiar el formulario
  form.value = {
    card_number: '',
    expiry: '',
    cvc: '',
    method: '',
    currency: '',
    mount: ''
  }

}
</script>

<style>
.border-primary {
  border: 2px solid #1976d2 !important;
  border-radius: 8px;
}
</style>
