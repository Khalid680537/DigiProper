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
                // Peacock workspace palette: purple brand + warm peach accent.
                primary: {
                    50:  '#f3f0fb',
                    100: '#e6dcf5',
                    200: '#cab9ec',
                    300: '#ad8fe1',
                    400: '#8e62d3',
                    500: '#684ed3',
                    600: '#4b2fbf',
                    700: '#3d1da5',
                    800: '#2f1485',
                    900: '#251066',
                    950: '#160641',
                },
                accent: {
                    50:  '#fdf3ef',
                    100: '#fbe1d5',
                    200: '#f6c3b0',
                    300: '#f0a18a',
                    400: '#df927f',
                    500: '#d77762',
                    600: '#c25c47',
                    700: '#a44535',
                    800: '#87382c',
                    900: '#6f2f26',
                    950: '#3d160f',
                },
            },
        },
    },

    plugins: [forms],
};
