import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        vuetify({
            styles: { configFile: 'resources/css/vuetify-settings.scss' },
            includePaths: ['node_modules'],
            autoImport: true,
          }),
    ],
});
