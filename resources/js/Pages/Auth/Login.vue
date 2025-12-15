<template>
  <div class="login-container">
    <v-card class="login-card" elevation="12">
          <v-card-title class="payment-header">
            <div class="text-center w-100">
              <v-icon :icon="mdiCreditCardOutline" size="48" class="mb-2" color="white"></v-icon>
              <h1 class="text-h4 font-weight-bold text-white">Payment Platform</h1>
              <p class="text-subtitle-1 text-white-70 mt-2">
                <v-icon :icon="mdiCurrencyUsd" size="20"></v-icon>
                Currency Exchange & Payment Processing
                <v-icon :icon="mdiSwapHorizontal" size="20"></v-icon>
              </p>
            </div>
          </v-card-title>

          <v-card-text class="pa-8">
            <h2 class="text-h5 mb-6 text-center font-weight-medium">Iniciar Sesión</h2>

            <v-form @submit.prevent="login">
              <v-text-field
                v-model="form.email"
                label="Correo electrónico"
                type="email"
                :prepend-inner-icon="mdiEmailOutline"
                :error-messages="form.errors.email"
                variant="outlined"
                color="primary"
                class="mb-3"
                required
              ></v-text-field>

              <v-text-field
                v-model="form.password"
                label="Contraseña"
                :type="showPassword ? 'text' : 'password'"
                :prepend-inner-icon="mdiLockOutline"
                :append-inner-icon="showPassword ? mdiEyeOff : mdiEye"
                @click:append-inner="showPassword = !showPassword"
                :error-messages="form.errors.password"
                variant="outlined"
                color="primary"
                class="mb-2"
                required
              ></v-text-field>

              <v-alert
                v-if="form.errors.login"
                type="error"
                variant="tonal"
                class="mb-4"
                closable
              >
                {{ form.errors.login }}
              </v-alert>

              <v-btn
                :loading="form.processing"
                class="payment-btn"
                block
                type="submit"
                size="large"
                color="primary"
              >
                <v-icon :icon="mdiLogin" class="mr-2"></v-icon>
                Ingresar
              </v-btn>
            </v-form>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4 justify-center flex-column">
            <p class="text-body-2 text-medium-emphasis mb-2">
              ¿No tienes cuenta?
              <v-btn
                color="success"
                variant="text"
                size="small"
                @click="go('/register')"
                class="px-2"
              >
                Regístrate aquí
              </v-btn>
            </p>
            <p class="text-caption text-medium-emphasis mt-3">
              Made by
              <a
                href="https://chipper-puffpuff-ffcd43.netlify.app/"
                target="_blank"
                rel="noopener noreferrer"
                class="text-primary text-decoration-none"
              >
                Nico Elias
              </a>
            </p>
          </v-card-actions>
        </v-card>
  </div>
</template>
<script setup>
import { router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
  mdiSwapHorizontal,
  mdiCreditCardOutline,
  mdiCurrencyUsd,
  mdiEmailOutline,
  mdiLockOutline,
  mdiEye,
  mdiEyeOff,
  mdiLogin
} from '@mdi/js'

const form = useForm({
  email: null,
  password: null
})

const showPassword = ref(false)

function login() {
  form.post('/', {
    preserveScroll: true
  })
}

const go = uri => router.visit(uri)
</script>

<style lang="scss" scoped>
.login-container {
  height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
  overflow: hidden;
}

.login-card {
  border-radius: 16px !important;
  overflow: visible;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2) !important;
  width: 100%;
  max-width: 480px;
}

.payment-header {
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
  padding: 40px 20px !important;
}

.text-white-70 {
  opacity: 0.9;
}

.w-100 {
  width: 100%;
}

.payment-btn {
  margin-top: 8px;
  text-transform: none;
  font-weight: 600;
  letter-spacing: 0.5px;
}

@media (max-width: 600px) {
  .login-container {
    padding: 8px;
  }

  .payment-header {
    padding: 32px 16px !important;
  }

  .payment-header h1 {
    font-size: 1.5rem !important;
  }
}
</style>
