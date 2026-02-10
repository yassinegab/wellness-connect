/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.html.twig",
    "./assets/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        border: "hsl(240 5.9% 90%)",

        background: "#fafafa",
        foreground: "#09090b",

        card: "#ffffff",
        popover: "#ffffff",
        popoverForeground: "#09090b",

        input: "#e4e4e7",
        ring: "#ef4444",

        sidebar: {
          DEFAULT: "#ffffff",
          foreground: "#3f3f46",
          primary: "#000000",
          accent: "#f4f4f5",
        },

        primary: {
          DEFAULT: "#ef4444",
          foreground: "#ffffff",
        },

        muted: {
          DEFAULT: "#f4f4f5",
          foreground: "#71717a",
        },

        accent: "#f4f4f5",
        destructive: "#ef4444",
      },
    },
  },
  plugins: [],
};
