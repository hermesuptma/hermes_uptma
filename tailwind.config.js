import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                hermes: {
                    azul: {
                        50: '#eef4f9',
                        100: '#d3e3ee',
                        600: '#1e3a5f',
                        700: '#17304d',
                        900: '#0d1b2e',
                    },
                    dorado: {
                        100: '#f7ecd6',
                        400: '#d4a24c',
                        500: '#c08f3a',
                        600: '#a67a2e',
                    },
                },
            },
        },
    },

    plugins: [forms],
};