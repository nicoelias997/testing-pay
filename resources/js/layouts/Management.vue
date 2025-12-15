<template>
  <v-app>
    <v-app-bar elevation="2" class="payment-app-bar">
      <template v-slot:prepend>
        <v-icon :icon="mdiCreditCardOutline" size="32" class="ml-4"></v-icon>
      </template>

      <v-toolbar-title class="font-weight-bold">
        {{ dynamicTitle }}
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn
        icon
        @click="logout"
        class="mr-2"
        variant="text"
      >
        <v-icon :icon="mdiLogout" />
      </v-btn>
    </v-app-bar>

    <v-main class="payment-main">
      <slot></slot>

      <div class="payment-footer">
        <span class="footer-text">
          Made with by
          <a
            href="https://chipper-puffpuff-ffcd43.netlify.app/"
            target="_blank"
            rel="noopener noreferrer"
            class="footer-link"
          >
            Nico Elias
          </a>
        </span>
      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { mdiLogout, mdiCreditCardOutline } from '@mdi/js'

const { props } = usePage()

const dynamicTitle = computed(() => props.pageTitle || 'Payment Platform')

function logout() {
  router.post('/logout')
}
</script>

<style scoped lang="scss">
.payment-app-bar {
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%) !important;
  color: white !important;

  :deep(.v-toolbar-title) {
    color: white !important;
  }

  :deep(.v-btn) {
    color: white !important;
  }
}

.payment-main {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  position: relative;
  padding-bottom: 60px;
  display: flex;
  flex-direction: column;
}

.payment-footer {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 20px;
  text-align: center;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.footer-text {
  font-size: 14px;
  font-weight: 400;
  color: #666;
}

.footer-link {
  color: #1976d2;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;

  &:hover {
    color: #1565c0;
    text-decoration: underline;
  }
}
</style>
