// tailwind.config.js — merge into the existing config
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        canvas:  { DEFAULT: '#f7f7fb', 2: '#f2f2f8' },
        ink:     { DEFAULT: '#26263a', 2: '#35354d', 3: '#4a4a63' },
        body:    '#6b6b85',
        muted:   '#82829b',
        hairline: '#e7e7ee',
        accent:  { DEFAULT: '#4f39e0', hover: '#4229c9', light: '#eeecfd', soft: '#dedaf9' },
        ok:      { DEFAULT: '#1fa36b', bg: '#e8f6ee' },
        warn:    { DEFAULT: '#97621b', bg: '#fdf6e7', border: '#eddcb4' },
        danger:  { DEFAULT: '#c0392b', bg: '#fceceb' },
      },
      fontFamily: {
        display: ['"Space Grotesk"', 'sans-serif'],
        sans: ['Manrope', 'system-ui', 'sans-serif'],
        mono: ['"JetBrains Mono"', 'monospace'],
      },
      borderRadius: { card: '16px', panel: '22px', field: '12px', ctl: '10px' },
      boxShadow: {
        mock: '0 30px 70px -30px rgba(48,40,90,.35)',
        cta:  '0 10px 26px -8px rgba(79,57,224,.6)',
      },
      letterSpacing: { display: '-0.035em', num: '-0.03em', micro: '0.12em' },
    },
  },
};
