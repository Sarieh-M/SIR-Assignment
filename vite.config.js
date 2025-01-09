import path from 'path';
import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';


const host = 'sir-assignment.test';
const certPath = path.resolve(__dirname, './certs/sir-assignment.test.pem');
const keyPath = path.resolve(__dirname, './certs/sir-assignment.test-key.pem');

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
                'app/Forms/Components/**',
                'app/Livewire/**',
                'app/Infolists/Components/**',
                'app/Providers/Filament/**',
                'app/Tables/Columns/**',
            ],
        }),
    ],
    server: {
        host,
        hmr: { host, port: 5173, protocol: 'wss' },
        https: {
            cert: certPath,
            key: keyPath,
        },
        port: 5173,
    },
});
