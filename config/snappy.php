<?php

/*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    |
    | This option contains settings for PDF generation.
    |
    | Enabled:
    |
    |    Whether to load PDF / Image generation.
    |
    | Binary:
    |
    |    The file path of the wkhtmltopdf / wkhtmltoimage executable.
    |
    | Timout:
    |
    |    The amount of time to wait (in seconds) before PDF / Image generation is stopped.
    |    Setting this to false disables the timeout (unlimited processing time).
    |
    | Options:
    |
    |    The wkhtmltopdf command options. These are passed directly to wkhtmltopdf.
    |    See https://wkhtmltopdf.org/usage/wkhtmltopdf.txt for all options.
    |
    | Env:
    |
    |    The environment variables to set while running the wkhtmltopdf process.
    |
    */

/*
| Resolve the wkhtmltopdf / wkhtmltoimage binary.
|
| knplabs/knp-snappy (>= 1.5) validates the configured binary with
| is_executable() and throws a RuntimeException ("The binary '...' is not
| executable.") when it does not resolve to an existing executable file.
| That check needs a full path — a bare "wkhtmltopdf" from the PATH fails it.
|
| To avoid hardcoding a single machine-specific path (and the resulting
| "not executable" error when the binary lives elsewhere), we probe a list
| of common install locations per platform and pick the first one that is
| actually executable. An explicit env override always wins so deployments
| can point at a known binary.
*/

$resolveBinary = static function (string $envKey, array $candidates): string {
    $override = env($envKey);

    if (! empty($override)) {
        return $override;
    }

    foreach ($candidates as $candidate) {
        if ($candidate !== '' && @is_executable($candidate)) {
            return $candidate;
        }
    }

    // Fall back to the first candidate so error messages stay meaningful.
    return $candidates[0] ?? '';
};

// Look the binary up on the PATH (cross-platform) and return a full path.
$fromPath = static function (string $name): ?string {
    $locator = PHP_OS_FAMILY === 'Windows' ? 'where' : 'command -v';
    $output = @shell_exec($locator.' '.escapeshellarg($name).' 2>&1');

    if (! is_string($output)) {
        return null;
    }

    $path = trim(strtok($output, "\n") ?: '');

    return ($path !== '' && @is_executable($path)) ? $path : null;
};

$pdfCandidates = PHP_OS_FAMILY === 'Windows'
    ? array_filter([
        'C:\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
        'C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
        'C:\\Program Files (x86)\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
        $fromPath('wkhtmltopdf'),
    ])
    : array_filter([
        base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
        '/usr/local/bin/wkhtmltopdf',
        '/usr/bin/wkhtmltopdf',
        $fromPath('wkhtmltopdf'),
    ]);

$imageCandidates = PHP_OS_FAMILY === 'Windows'
    ? array_filter([
        'C:\\wkhtmltopdf\\bin\\wkhtmltoimage.exe',
        'C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltoimage.exe',
        'C:\\Program Files (x86)\\wkhtmltopdf\\bin\\wkhtmltoimage.exe',
        $fromPath('wkhtmltoimage'),
    ])
    : array_filter([
        base_path('vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64'),
        '/usr/local/bin/wkhtmltoimage',
        '/usr/bin/wkhtmltoimage',
        $fromPath('wkhtmltoimage'),
    ]);

return [

    'pdf' => [
        'enabled' => true,
        'binary' => $resolveBinary('WKHTML_PDF_BINARY', array_values($pdfCandidates)),
        'timeout' => false,
        'options' => [
            'enable-local-file-access' => true,
            'print-media-type' => true,
        ],
        'env' => [],
    ],

    'image' => [
        'enabled' => true,
        'binary' => $resolveBinary('WKHTML_IMG_BINARY', array_values($imageCandidates)),
        'timeout' => false,
        'options' => [
            'enable-local-file-access' => true,
        ],
        'env' => [],
    ],

];
