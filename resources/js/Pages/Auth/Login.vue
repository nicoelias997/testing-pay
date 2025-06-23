<template>
    <v-col class="form-login">
    <v-sheet class="mx-auto form-container" width="50%">
        <div class="title-login">
        <h1>Laravel Payment</h1>
        <h2 class="d-flex">
          Currency<v-icon :icon="mdiSwapHorizontal"></v-icon>Platform
        </h2>
      </div>
      <!-- fast-fail -->
      <v-form  @submit.prevent="login">
        <v-text-field
          v-model="form.email"
          label="Ingrese su correo electronico"
          type="email"
          :error-messages="form.errors.email"
        ></v-text-field>
        <v-text-field
            v-model="form.password"
            label="Ingrese su contraseña"
            type="password"
            :error-messages="form.errors.password"
        ></v-text-field>
        <div v-if="form.errors.login" class="error-login">
          {{ form.errors.login }}
        </div>
        <v-btn
          :loading="loading"
          class="btn-login"
          block
          type="submit"
          size="large"
          height="60"
        >
          Ingresar
        </v-btn>
      </v-form>
      <v-btn
        :loading="loading"
        color="success"
        variant="text"
        block
        type="button"
        size="x-small"
        height="60"
        @click="go('/register')"
      >
        {{ $t('app.registered.account')}}
      </v-btn>
    </v-sheet>
    </v-col>
  </template>
  <script setup>
  import { router, useForm } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import {
    mdiSwapHorizontal
  } from '@mdi/js';

    const form = useForm({
        email: null,
        password: null
    })
    function login(){
        form.post('/', {
          preserveScroll: true
        })
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
    color: #EB3E39;
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
  @media (max-width: 600px) {
    .form-container {
      width: 100% !important; /* Reducir el ancho del formulario en pantallas pequeñas */
    }
  }
  </style>
  