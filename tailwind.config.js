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
    }
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

