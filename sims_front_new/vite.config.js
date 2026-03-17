// import { defineConfig } from 'vite'
// import vue from '@vitejs/plugin-vue'

// // https://vite.dev/config/
// export default defineConfig({
//   plugins: [vue()],
// })

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    host: 'sims.clcst.edu.local',
    port: 5173,
    strictPort: true,
    hmr: {
      host: 'sims.clcst.edu.local',
      protocol: 'ws',
      port: 5173
    }
  }
})