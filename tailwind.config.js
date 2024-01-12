/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  safelist: [
    {
      pattern: /grid-.+/
    },
    {
      pattern: /row-span-.+/
    },
    {
      pattern: /bg-.+/
    }
  ],
  theme: {
    extend: {
      gridTemplateColumns: {
        '19': 'repeat(19, minmax(0, 1fr))',
      },
      fontSize: {
        'xxs': ['0.65rem', '0.75rem'],
      }
    },
  },
  plugins: [],
}

