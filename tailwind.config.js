/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./*.php", "./**/*.php", "./*.html", "./**/*.html"],
  theme: {
    extend: {},
  },
  plugins: [require("daisyui")],
};
