/**
 * Triangle POS — Tailwind config
 *
 * The design is authored in OKLCH. These tokens are transcribed 1:1 from the
 * HTML reference (handoff/design/Triangle POS.dc.html). Do not hand-tune the
 * values — they are the source of truth for the whole UI.
 */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        // Neutrals (near-black ink -> paper)
        ink:      'oklch(0.21 0.02 277)',   // headings, sidebar, dark CTA
        'ink-2':  'oklch(0.28 0.02 277)',   // hover on dark surfaces
        'ink-3':  'oklch(0.30 0.02 277)',   // dark chips / logo tile
        body:     'oklch(0.42 0.02 277)',   // body copy
        muted:    'oklch(0.52 0.02 277)',   // secondary copy
        faint:    'oklch(0.55 0.02 277)',   // labels
        line:     'oklch(0.91 0.008 280)',  // hairline borders
        'line-2': 'oklch(0.93 0.008 280)',  // lighter hairline
        paper:    'oklch(0.985 0.004 280)', // app background
        'paper-2':'oklch(0.975 0.004 280)', // dashboard background
        // Brand — violet
        brand:      'oklch(0.52 0.19 277)',
        'brand-600':'oklch(0.45 0.19 277)', // hover
        'brand-400':'oklch(0.62 0.19 277)', // accent / triangle
        'brand-300':'oklch(0.68 0.19 277)',
        'brand-tint':'oklch(0.96 0.02 277)',// pill / soft bg
        // Status
        'success':'oklch(0.60 0.15 155)',
        'success-bg':'oklch(0.95 0.05 155)',
        'success-fg':'oklch(0.42 0.13 155)',
        'warn':   'oklch(0.45 0.12 55)',
        'warn-bg':'oklch(0.97 0.04 75)',
        'danger': 'oklch(0.50 0.16 20)',
        'danger-bg':'oklch(0.96 0.03 20)',
        'danger-fg':'oklch(0.48 0.16 20)',
      },
      fontFamily: {
        display: ['"Space Grotesk"', 'system-ui', 'sans-serif'],
        sans:    ['Manrope', 'system-ui', 'sans-serif'],
        mono:    ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
      },
      borderRadius: {
        xl2: '16px',
        xl3: '20px',
      },
      boxShadow: {
        card: '0 30px 70px -30px oklch(0.3 0.04 277 / 0.35)',
        brand: '0 10px 26px -8px oklch(0.52 0.19 277 / 0.6)',
      },
      keyframes: {
        tpRise: { from: { opacity: '0', transform: 'translateY(8px)' }, to: { opacity: '1', transform: 'none' } },
      },
      animation: { rise: 'tpRise .4s ease both' },
    },
  },
  plugins: [],
};
