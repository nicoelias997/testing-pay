<template>
  <v-app>
    <v-layout class="rounded rounded-md">
      <v-main class="d-flex align-center justify-center">
        <v-app-bar>
          <v-spacer></v-spacer>
          <!-- <v-btn 
            class="me-4"
            :prepend-icon="mdiEarth" 
            style="font-size: 22px"
          >
            <v-icon 
              :icon="mdiMenuDown"
            ></v-icon>
          </v-btn> -->
        </v-app-bar>   
        <v-container class="responsive-container">
          <slot></slot>
        </v-container>
        <v-footer>
          <span class="footer">Copyright &copy; {{ new Date().getFullYear() }}. Nico Elias. Derechos reservados.</span>
        </v-footer>
      </v-main>
    </v-layout>
  </v-app>
</template>
<script>
import {
  mdiEarth,
  mdiCodeBrackets,
  mdiMenuDown,
} from '@mdi/js'

export default {
  setup () {
    return {
      mdiEarth,
      mdiCodeBrackets,
      mdiMenuDown,
    }
  }
}
</script>
<style scoped lang="scss">

.v-app-bar{
  background-color: #f5f5f5
}

.footer{
  position: fixed;
  bottom: 0;
  right: 0;
  left: 0;
  padding: 22px 15px;
  font-size: 12px;
  font-weight: 300;
  color: #0e0e0e;
  background-color: #f5f5f5
 }
 .responsive-container {
  max-height: 100vh; 
  height: 100%; 
  overflow-y: auto; 
  overflow-x: hidden;
  margin-bottom: 10%;
 }
 </style>
