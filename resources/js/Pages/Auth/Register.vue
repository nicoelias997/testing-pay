<template>
  <div class="register-container">
    <v-card class="register-card" elevation="12">
          <v-card-title class="payment-header">
            <div class="text-center w-100">
              <v-icon :icon="mdiAccountPlus" size="40" class="mb-1" color="white"></v-icon>
              <h1 class="text-h5 font-weight-bold text-white">Crear Cuenta</h1>
            </div>
          </v-card-title>

          <v-card-text class="pa-6">
            <v-form @submit.prevent="register">
              <v-text-field
                v-model="form.name"
                :label="$t('app.users.name')"
                :prepend-inner-icon="mdiAccount"
                :error-messages="form.errors.name"
                variant="outlined"
                color="primary"
                class="mb-2"
                clearable
                required
              ></v-text-field>

              <v-text-field
                v-model="form.email"
                type="email"
                :label="$t('app.users.email')"
                :prepend-inner-icon="mdiEmailOutline"
                :error-messages="form.errors.email"
                variant="outlined"
                color="primary"
                class="mb-2"
                clearable
                required
              ></v-text-field>

              <v-text-field
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                :label="$t('app.users.password')"
                :prepend-inner-icon="mdiLockOutline"
                :append-inner-icon="showPassword ? mdiEyeOff : mdiEye"
                @click:append-inner="showPassword = !showPassword"
                :error-messages="form.errors.password"
                variant="outlined"
                color="primary"
                class="mb-2"
                clearable
                required
              ></v-text-field>

              <v-text-field
                v-model="form.password_confirmation"
                :type="showPasswordConfirm ? 'text' : 'password'"
                :label="$t('app.users.password_confirmation')"
                :prepend-inner-icon="mdiLockCheck"
                :append-inner-icon="showPasswordConfirm ? mdiEyeOff : mdiEye"
                @click:append-inner="showPasswordConfirm = !showPasswordConfirm"
                :error-messages="form.errors.password_confirmation"
                variant="outlined"
                color="primary"
                class="mb-2"
                clearable
                required
              ></v-text-field>

              <v-btn
                :loading="form.processing"
                class="payment-btn mt-2"
                block
                type="submit"
                size="large"
                color="primary"
              >
                <v-icon :icon="mdiAccountPlus" class="mr-2"></v-icon>
                {{ $t('app.register') }}
              </v-btn>
            </v-form>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-3 justify-center flex-column">
            <p class="text-body-2 text-medium-emphasis mb-1">
              ¿Ya tienes cuenta?
              <v-btn
                color="success"
                variant="text"
                size="small"
                @click="go('/')"
                class="px-2"
              >
                Inicia sesión
              </v-btn>
            </p>
            <p class="text-caption text-medium-emphasis mt-1">
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
  mdiAccountPlus,
  mdiAccount,
  mdiEmailOutline,
  mdiLockOutline,
  mdiLockCheck,
  mdiEye,
  mdiEyeOff
} from '@mdi/js'

const form = useForm({
  name: null,
  email: null,
  password: null,
  password_confirmation: null
})

const showPassword = ref(false)
const showPasswordConfirm = ref(false)

function register() {
  form.post('/register')
}

const go = uri => router.visit(uri)
</script>

<style lang="scss" scoped>
.register-container {
  height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
  overflow: hidden;
}

.register-card {
  border-radius: 16px !important;
  overflow: visible;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2) !important;
  width: 100%;
  max-width: 460px;
  max-height: 95vh;
  overflow-y: auto;
}

.payment-header {
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
  padding: 24px 20px !important;
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
  .register-container {
    padding: 8px;
  }

  .register-card {
    max-height: 98vh;
  }

  .payment-header {
    padding: 20px 16px !important;
  }

  .payment-header h1 {
    font-size: 1.3rem !important;
  }
}

</style>
