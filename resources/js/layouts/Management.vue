<template>
  <v-card class="mx-auto" max-width="100%">
    <v-layout column>
      <!-- App Bar con degradado (sin imagen) -->
      <v-app-bar flat class="app-bar-gradient" dark>
        <!-- Icono para abrir el navigation drawer -->
        <v-app-bar-nav-icon @click="drawer = !drawer" />

        <!-- Título dinámico -->
        <v-toolbar-title>{{ dynamicTitle }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <!-- Campo de búsqueda visible sólo en la página de recetas -->
        <template v-if="isRecipesIndex">
          <v-text-field 
            v-model="searchQuery" 
            append-icon="mdi-magnify" 
            single-line 
            hide-details 
            placeholder="Buscar..."
            class="mx-2"
          />
        </template>

        <v-btn icon @click="search">
          <v-icon :icon="mdiMagnify" />
        </v-btn>
        <v-btn icon @click="create">
          <v-icon :icon="mdiPlusCircle" />
        </v-btn>
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

// Detectamos si la página actual es el índice de recetas. Puedes ajustar la condición según tu lógica.
const isRecipesIndex = computed(() => {
  return props.pageTitle && props.pageTitle.toLowerCase().includes('recetas')
})

// Campo de búsqueda
const searchQuery = ref('')

// Control del navigation drawer
const drawer = ref(false)

// Ítems del menú de navegación
const navItems = ref([
  { title: 'Recetas', value: 'recipes' },
  { title: 'Ingredientes', value: 'ingredients' },
  { title: 'Condimentos', value: 'condiments' },
  { title: 'Postres', value: 'desserts' },
  { title: 'Tragos', value: 'drinks' }
])

// Función para navegar a las distintas secciones
function navigateTo(section) {
  switch(section) {
    case 'recipes':
      router.visit('/ui/recipes')
      break
    case 'condiments':
      router.visit('/ui/condiments')
      break
    case 'ingredients':
      router.visit('/ui/ingredients')
      break
    case 'desserts':
      router.visit('/ui/desserts')
      break
    case 'drinks':
      router.visit('/ui/drinks')
      break
    default:
      break
  }
  drawer.value = false
}

// function goToProfile() {
//   router.visit('/ui/profile')
// }
function search(){
  console.log("Hello")
}
function create(){
  router.visit('/ui/create')
}
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
