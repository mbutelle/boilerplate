/** @type {import('tailwindcss').Config} */
import PrimeUI from 'tailwindcss-primeui'

export default {
  darkMode: ['selector', '[class*="app-dark"]'],
  content: ['./components/**/*.{vue,js,jsx,mjs,ts,tsx}', './layouts/**/*.{vue,js,jsx,mjs,ts,tsx}', './pages/**/*.{vue,js,jsx,mjs,ts,tsx}', './plugins/**/*.{js,ts,mjs}', './composables/**/*.{js,ts,mjs}', './utils/**/*.{js,ts,mjs}'],
  plugins: [PrimeUI],
  theme: {
    screens: {
      'sm': '576px',
      'md': '768px',
      'lg': '992px',
      'xl': '1200px',
      '2xl': '1920px',
    },
  },
}
