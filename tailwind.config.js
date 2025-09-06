/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./resources/views/**/*.blade.php", "./resources/js/**/*.js"],
    theme: {
        extend: {},
    },
    plugins: [],
    safelist: [
        "z-50", // Stellt sicher, dass diese Klasse immer generiert wird
    ],
};
