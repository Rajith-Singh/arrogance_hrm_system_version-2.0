import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    host: '0.0.0.0', // Bind to all network interfaces
    port: 5000, // Vite server port
    hmr: {
      host: '127.0.0.1', // Ensure this matches your actual IP
      port: 3001, // Port for HMR notifications
    }
  },
});
