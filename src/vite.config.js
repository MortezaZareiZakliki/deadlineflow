import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/main.tsx"],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
    optimizeDeps: {
        include: [
            'react-multi-date-picker',
            'react-date-object',
        ],
    },
    server: {
        host: "0.0.0.0",
        port: 5173,
        strictPort: true,
        hmr: {
            host: "10.12.143.31",
            port: 5173,
        },
        watch: {
            usePolling: true,
        },
    },
});
