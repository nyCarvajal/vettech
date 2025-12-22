import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/css/**/*.css',
        './storage/framework/views/*.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                mint: {
                    50: 'var(--mint-50)',
                    100: 'var(--mint-100)',
                    200: 'var(--mint-200)',
                    500: 'var(--mint-500)',
                    600: 'var(--mint-600)',
                    700: 'var(--mint-700)',
                },
                gray: {
                    50: 'var(--gray-50)',
                    100: 'var(--gray-100)',
                    200: 'var(--gray-200)',
                    500: 'var(--gray-500)',
                    700: 'var(--gray-700)',
                },
                danger: {
                    500: 'var(--danger-500)',
                },
                warning: {
                    500: 'var(--warning-500)',
                },
            },
            fontFamily: {
                sans: ['var(--font-sans)', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 10px 30px rgba(15, 23, 42, 0.06)',
            },
        },
    },
    plugins: [],
};
