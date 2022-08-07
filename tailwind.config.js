const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    mode: 'jit',
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                proximanova: ['ProximaNova', 'monospace'],
            },
        },
        minHeight: {
            '90vh': '90vh'
        },
        maxHeight: {
            '90vh': '90vh'
        },
        zIndex: {
            '999': '999'
        }
    },

    variants: {
        extend: {
            opacity: ['disabled'],
        },
    },
    important: true,
    plugins: [require('@tailwindcss/forms')],
};
