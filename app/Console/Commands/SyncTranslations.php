<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class SyncTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync
                            {--locale=* : One or more target locales to sync (defaults to the app locale plus any existing JSON locale files)}
                            {--prune : Remove keys from the locale files that are no longer present in the source code}
                            {--sort : Sort the keys alphabetically when writing the locale files}
                            {--dry-run : Show what would change without writing any files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the codebase for translation strings and sync them into the JSON language files.';

    /**
     * Directories that are scanned for translation strings.
     *
     * @var string[]
     */
    protected array $scanPaths = [
        'app',
        'resources/views',
        'routes',
        'database',
        'Modules',
    ];

    /**
     * File extensions that are scanned for translation strings.
     *
     * @var string[]
     */
    protected array $scanExtensions = ['php'];

    /**
     * Translation helpers whose first string argument is a translation key.
     *
     * @var string[]
     */
    protected array $functions = ['__', 'trans', 'trans_choice', '@lang', '@choice'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning the codebase for translation strings...');

        $keys = $this->collectKeys();

        if (empty($keys)) {
            $this->warn('No translation strings were found.');

            return self::SUCCESS;
        }

        $this->line(sprintf('Found <info>%d</info> unique translation string(s).', count($keys)));

        $locales = $this->resolveLocales();

        if ($this->option('sort')) {
            sort($keys, SORT_NATURAL | SORT_FLAG_CASE);
        }

        $rows = [];

        foreach ($locales as $locale) {
            $rows[] = $this->syncLocale($locale, $keys);
        }

        $this->newLine();
        $this->table(['Locale', 'File', 'Added', 'Pruned', 'Total'], $rows);

        if ($this->option('dry-run')) {
            $this->warn('Dry run: no files were written.');
        }

        return self::SUCCESS;
    }

    /**
     * Collect every translation key used across the scanned paths.
     *
     * @return string[]
     */
    protected function collectKeys(): array
    {
        $paths = array_values(array_filter(
            array_map(fn (string $path) => base_path($path), $this->scanPaths),
            fn (string $path) => is_dir($path)
        ));

        if (empty($paths)) {
            return [];
        }

        $finder = Finder::create()->files()->in($paths);

        foreach ($this->scanExtensions as $extension) {
            $finder->name('*.'.$extension);
        }

        $pattern = $this->buildPattern();
        $keys = [];

        foreach ($finder as $file) {
            $contents = $file->getContents();

            if (! preg_match_all($pattern, $contents, $matches)) {
                continue;
            }

            foreach (array_merge($matches['single'], $matches['double']) as $match) {
                if ($match === '') {
                    continue;
                }

                $key = $this->normalizeKey($match);

                // Skip dotted keys that reference PHP array translation files
                // (e.g. validation.required) rather than JSON string keys.
                if ($this->looksLikeArrayKey($key)) {
                    continue;
                }

                $keys[$key] = true;
            }
        }

        return array_keys($keys);
    }

    /**
     * Build the regular expression used to match translation helper calls.
     */
    protected function buildPattern(): string
    {
        $functions = implode('|', array_map(
            fn (string $function) => preg_quote($function, '/'),
            $this->functions
        ));

        // Matches a helper call followed by its first single or double quoted argument.
        return '/(?:'.$functions.')\s*\(\s*(?:'
            ."'(?<single>(?:\\\\.|[^'\\\\])*)'"
            .'|"(?<double>(?:\\\\.|[^"\\\\])*)"'
            .')/s';
    }

    /**
     * Un-escape a raw matched string into its literal translation key.
     */
    protected function normalizeKey(string $raw): string
    {
        // Handle the escape sequences PHP recognises inside single/double quotes.
        return str_replace(
            ['\\\\', "\\'", '\\"'],
            ['\\', "'", '"'],
            $raw
        );
    }

    /**
     * Determine whether a key targets a PHP array file rather than a JSON string.
     */
    protected function looksLikeArrayKey(string $key): bool
    {
        // Keys without spaces that contain a dot and no sentence punctuation are
        // treated as references to array based translation files.
        return ! Str::contains($key, ' ')
            && Str::contains($key, '.')
            && ! Str::endsWith($key, '.');
    }

    /**
     * Resolve the list of locales to sync.
     *
     * @return string[]
     */
    protected function resolveLocales(): array
    {
        $locales = array_filter((array) $this->option('locale'));

        if (empty($locales)) {
            $locales = [config('app.locale', 'en')];

            foreach (glob($this->langPath('*.json')) as $file) {
                $locales[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        return array_values(array_unique($locales));
    }

    /**
     * Sync a single locale file and return a summary row for the output table.
     *
     * @param  string[]  $keys
     * @return array<int, string|int>
     */
    protected function syncLocale(string $locale, array $keys): array
    {
        $path = $this->langPath($locale.'.json');
        $existing = $this->readLocale($path);

        $isBaseLocale = $locale === config('app.locale', 'en');

        $added = 0;
        $translations = $existing;

        foreach ($keys as $key) {
            if (array_key_exists($key, $translations)) {
                continue;
            }

            // The base locale is keyed by the source string, so it can safely
            // default to the key itself. Other locales start untranslated.
            $translations[$key] = $isBaseLocale ? $key : '';
            $added++;
        }

        $pruned = 0;

        if ($this->option('prune')) {
            $lookup = array_flip($keys);

            foreach (array_keys($translations) as $existingKey) {
                if (! isset($lookup[$existingKey])) {
                    unset($translations[$existingKey]);
                    $pruned++;
                }
            }
        }

        if ($this->option('sort')) {
            uksort($translations, fn ($a, $b) => strcasecmp($a, $b));
        }

        if (($added > 0 || $pruned > 0) && ! $this->option('dry-run')) {
            $this->writeLocale($path, $translations);
        }

        return [
            $locale,
            $this->relativePath($path),
            $added,
            $pruned,
            count($translations),
        ];
    }

    /**
     * Read an existing JSON locale file.
     *
     * @return array<string, string>
     */
    protected function readLocale(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode(file_get_contents($path), true);

        if (! is_array($decoded)) {
            $this->warn(sprintf('Could not parse %s, treating it as empty.', $this->relativePath($path)));

            return [];
        }

        return $decoded;
    }

    /**
     * Write a JSON locale file, preserving unicode characters and readability.
     *
     * @param  array<string, string>  $translations
     */
    protected function writeLocale(string $path, array $translations): void
    {
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $json = json_encode(
            $translations,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        file_put_contents($path, $json.PHP_EOL);
    }

    /**
     * Resolve a path inside the application's language directory.
     */
    protected function langPath(string $path = ''): string
    {
        return $this->laravel->langPath($path);
    }

    /**
     * Present a path relative to the project root for cleaner output.
     */
    protected function relativePath(string $path): string
    {
        return Str::after($path, base_path().DIRECTORY_SEPARATOR);
    }
}
