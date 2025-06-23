import 'vuetify/dist/vuetify.min.css'; // Importar los estilos de Vuetify
import 'sweetalert2/dist/sweetalert2.min.css';

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { createI18n } from 'vue-i18n'

import en from '../js/locales/en.json'
import es from '../js/locales/es.json'

import Auth from '../js/layouts/Auth.vue'
import Management from '../js/layouts/Management.vue'

import Vuetify from '../js/plugins/vuetify'


createInertiaApp({
    // title: (title) => `${title} - ${appName}`,
    resolve: (name) =>{
      const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
      let page = pages[`./Pages/${name}.vue`]
      if(name.startsWith('Auth')){
        page.default.layout = page.default.layout || Auth
      }
      if(name.startsWith('Ui')){
        page.default.layout = page.default.layout || Management
      }
      return page
    },
    setup({ el, App, props, plugin }) {
      const i18n = createI18n({
        legacy: false,
        globalInjection: true,
        locale: 'es',
        messages: {
          es: es,
          en: en
        }
      })
        return createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(Vuetify)
        .use(i18n)
        .mount(el);
    },
});
