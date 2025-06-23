<template>
    <v-col 
      class="form-login"
      style="max-height:100vh; overflow-y: auto;" 
    >
      <v-sheet 
        class="mx-auto form-container"
        width="65%"
      >
        <v-row 
          no-gutters
          class="titles" 
        >
          <h1 class="mt-4">
            {{ $t('app.register') }}
          </h1>
          <v-spacer></v-spacer>
          <v-btn
            class="btn"
            color="success"
            variant="text"
            type="button"
            size="small"
            height="60"
            @click="go('/')"
          >
            Ya tengo cuenta
          </v-btn>
        </v-row>
        <v-form @submit.prevent="register">
          <v-text-field 
            v-model="form.name" 
            class="mt-3"
            :label="$t('app.users.name')"
            :error-messages="form.errors.name"
            variant="outlined"
            clearable
          ></v-text-field>
          <v-text-field 
            v-model="form.password" 
            class="mt-3"
            type="password"
            :label="$t('app.users.password')"
            :error-messages="form.errors.password"
            variant="outlined"
            clearable
          ></v-text-field>
          <v-text-field 
            v-model="form.password_confirmation" 
            class="mt-3"
            type="password"
            :label="$t('app.users.password_confirmation')"
            :error-messages="form.errors.password_confirmation"
            variant="outlined"
            clearable
          ></v-text-field>
          <v-text-field 
            v-model="form.email" 
            class="mt-3"
            type="email"
            :label="$t('app.users.email')"
            :error-messages="form.errors.email"
            variant="outlined"
            clearable
          ></v-text-field>
          <v-btn
            :loading="loading"
            class="btn-login"
            block
            type="submit"
            size="large"
            height="60"
          >
            {{ $t('app.register') }}
          </v-btn>
        </v-form>
      </v-sheet>
    </v-col>
  </template>
  <script setup>
  import { router, useForm } from '@inertiajs/vue3'
  import { ref } from 'vue'


  const form = useForm({
      name: null,
      email: null,
      password: null,
      password_confirmation: null,

    })
function register(){
    form.post('/register')
}
  const loading = ref(false)

  const go = uri => router.visit(uri)

  </script>
  
  <style lang="scss" scoped>
  .fade-grow-enter-active {
    transition: all 1s cubic-bezier(.28, .06, .33, .79);
  }
  
  .fade-grow-enter-from {
    opacity: 0;
    transform: scale(0.98);
  }
  
  .error-login{
    color: red;
    font-size : 500;
    margin-bottom: 1rem;
  }
  .form-login{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  
  .title-login {
    margin-bottom: 5%;
    display: flex;
    flex-direction: column;
    justify-content: center; /* Centra verticalmente */
    align-items: center; /* Centra horizontalmente */
    text-align: center;
    height: 100%; /* Ocupa todo el espacio vertical disponible en form-login */
  }
  .btn-login{
    background-color: rgba(129, 199, 141);
    color: white;
  }
  .titles {
    display: flex;
    flex-direction: row;
    align-items: center;
    text-align: center;
  }
  .btn {
      margin-top: 15px;
    }
  @media (max-width: 600px) {
    .form-container {
      width: 100% !important; /* Reducir el ancho del formulario en pantallas pequeñas */
    }
    h1 {
      font-size: 150%;
    }
    .btn {
      margin-top: 3px;
    }
  }
  </style>
  