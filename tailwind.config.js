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
                ink: {
                    50:  '#f7f7fb',
                    100: '#eeedf4',
                    200: '#dcdae6',
                    300: '#b9b6cc',
                    400: '#8e8aaa',
                    500: '#67638a',
                    600: '#4d496e',
                    700: '#3a3756',
                    800: '#26243b',
                    900: '#161525',
                    950: '#0c0b18',
                },
                surface: {
                    50:  '#fbfaff',
                    100: '#f4f1fb',
                    200: '#ebe6f5',
                },
            },
            backgroundImage: {
                'hero-purple': 'linear-gradient(135deg, #4b2fbf 0%, #684ed3 50%, #3d1da5 100%)',
                'hero-warm':   'linear-gradient(135deg, #4b2fbf 0%, #684ed3 45%, #d77762 100%)',
                'hero-night':  'linear-gradient(135deg, #3d1da5 0%, #160641 100%)',
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            boxShadow: {
                'soft':       '0 1px 2px rgba(22,6,65,0.04), 0 4px 16px rgba(22,6,65,0.06)',
                'soft-lg':    '0 4px 12px rgba(22,6,65,0.06), 0 16px 48px rgba(22,6,65,0.10)',
                'glow':       '0 8px 32px -8px rgba(104,78,211,0.45)',
                'glow-sm':    '0 4px 16px -4px rgba(104,78,211,0.35)',
                'inset-ring': 'inset 0 0 0 1px rgba(22,6,65,0.06)',
            },
            keyframes: {
                'fade-up': {
                    '0%':   { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'shimmer': {
                    '100%': { transform: 'translateX(100%)' },
                },
                'pulse-dot': {
                    '0%, 100%': { opacity: '1' },
                    '50%':      { opacity: '.4' },
                },
            },
            animation: {
                'fade-up':   'fade-up .4s ease-out both',
                'shimmer':   'shimmer 1.4s linear infinite',
                'pulse-dot': 'pulse-dot 1.6s ease-in-out infinite',
            },
            transitionTimingFunction: {
                'spring': 'cubic-bezier(.22,1.2,.36,1)',
            },
        },
    },

    plugins: [forms],
};
