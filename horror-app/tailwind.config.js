/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx}",
    "./components/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        horrorRed: "#7f1d1d",
        horrorDark: "#050505",
      },
    },
  },
  plugins: {},
};
