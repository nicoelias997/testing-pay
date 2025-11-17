<template>
  <v-card class="mx-auto" max-width="100%">
    <v-layout column>
      <!-- App Bar con degradado (sin imagen) -->
      <v-app-bar flat class="app-bar-gradient" dark>

        <!-- Título dinámico -->
        <v-toolbar-title>{{ dynamicTitle }}</v-toolbar-title>

        <v-spacer></v-spacer>
        <v-btn icon @click="logout">
          <v-icon :icon="mdiLogout" />
        </v-btn>
      </v-app-bar>

      <!-- Navigation Drawer -->
      <v-navigation-drawer 
        v-model="drawer" 
        temporary 
        :location="$vuetify.display.mobile ? 'bottom' : 'left'"
      >
        <v-list>
          <v-list-item 
            v-for="item in navItems" 
            :key="item.value" 
            @click="navigateTo(item.value)"
          >
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>

      <!-- Contenido principal -->
      <v-main>
        <v-container fluid>
          <slot></slot>
        </v-container>
      </v-main>

    </v-layout>
  </v-card>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { mdiMagnify, mdiHome, mdiLogout, mdiPlusCircle } from '@mdi/js'

const { props } = usePage()

// Se espera que cada página envíe un prop "pageTitle". Si no, usamos un valor por defecto.
const dynamicTitle = computed(() => props.pageTitle || 'Mi plataforma de pago')

function logout() {
  router.post('/logout')
}
</script>

<style scoped>
.app-bar-gradient {
  background: linear-gradient(45deg, #ff9a9e, #fad0c4);
  /* Ajusta estos colores según tu preferencia para una web de comida */
}
</style>
