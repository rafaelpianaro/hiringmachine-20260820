/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'off-white': '#FDFDF9',
        'mint': '#EAF1E4',
        'graphite': '#1F2420',
        'sage': '#5C6B5A',
        'olive': '#8DB33F',
        'olive-dark': '#719432',
        'terracotta': '#E07A3E',
        'gold': '#F2B705',
        'star-empty': '#D8DED2',
        'border-light': '#E3E9DD',
        'error': '#D9534F',
      },
      fontFamily: {
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
        sans: ['"Inter"', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        '3xl': '1.5rem',
        '4xl': '2rem',
      },
      animation: {
        'float': 'float 6s ease-in-out infinite',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-12px)' },
        }
      }
    },
  },
  plugins: [],
}
