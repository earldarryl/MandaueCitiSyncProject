import plugin from 'tailwindcss/plugin';

const customColSpanPlugin = plugin(function ({ addUtilities }) {
  const newUtilities = {};
  for (let i = 14; i <= 50; i++) {
    newUtilities[`.col-span-${i}`] = {
      'grid-column': `span ${i} / span ${i}`,
    };
  }
  addUtilities(newUtilities);
});

export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './vendor/livewire/flux/**/*.blade.php',
    './vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php',
  ],
  darkMode: 'class',
  plugins: [customColSpanPlugin],
};
