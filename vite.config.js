import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { visualizer } from 'rollup-plugin-visualizer';

export default defineConfig(({ mode }) => ({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // Bundle analyzer for production builds
        mode === 'analyze' && visualizer({
            filename: 'dist/stats.html',
            open: true,
            gzipSize: true,
            brotliSize: true,
        }),
    ].filter(Boolean),
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        // Enable minification
        minify: 'esbuild',
        // Improve chunk splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    // Separate vendor libraries
                    'vendor': ['vue', 'axios'],
                    'ui': ['bootstrap', '@popperjs/core'],
                    'realtime': ['laravel-echo', 'pusher-js'],
                    'utils': ['alpinejs']
                },
                // Optimize chunk file names
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]'
            }
        },
        // Optimize source maps for production
        sourcemap: false,
        // Enable compression
        cssCodeSplit: true,
        // Set chunk size warning limit
        chunkSizeWarningLimit: 500,
        // Enable minification
        cssMinify: true,
        // Optimize dependencies
        target: 'es2015'
    },
    // Optimize dev server
    server: {
        hmr: {
            overlay: false
        }
    },
    // Enable CSS preprocessing optimizations
    css: {
        devSourcemap: false,
        preprocessorOptions: {
            scss: {
                // Enable compression
                outputStyle: 'compressed',
                // Reduce precision for smaller file sizes
                precision: 6
            }
        }
    }
}));
