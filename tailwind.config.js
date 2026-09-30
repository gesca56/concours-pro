import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './config/ipnetp.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                marine: {
                    DEFAULT: '#0F172A',
                    light: '#1E3A8A',
                },
                institutionnel: {
                    DEFAULT: '#2563EB',
                    hover: '#1D4ED8',
                },
                fond: '#F8FAFC',
            },
        },
    },

    plugins: [forms],
};
