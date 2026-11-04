<?php

namespace dokuwiki\plugin\dw2pdf\src;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

/** Register template-local TrueType fonts without executing template PHP. */
class TemplateFonts
{
    public static function configure(string $directory, array $config): array
    {
        $manifest = $directory . '/fonts.json';
        if (!is_file($manifest)) return $config;

        $json = file_get_contents($manifest);
        try {
            $fonts = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Invalid PDF font manifest: ' . $manifest, 0, $e);
        }
        if (!is_array($fonts)) {
            throw new \RuntimeException('PDF fonts.json must map font families to font files');
        }
        if (!$fonts) return $config;

        $root = realpath($directory) . DIRECTORY_SEPARATOR;
        $signature = [$root, $json];
        foreach ($fonts as $family => $variants) {
            if (!preg_match('/\A[a-z][a-z0-9_-]*\z/', (string) $family)
                || !is_array($variants) || !isset($variants['R'])) {
                throw new \RuntimeException('Each PDF font needs a lowercase family name and an R file');
            }
            foreach ($variants as $style => $file) {
                if (!in_array($style, ['R', 'B', 'I', 'BI'], true) || !is_string($file)) {
                    throw new \RuntimeException('PDF font variants must be R, B, I or BI file paths');
                }
                $path = realpath($directory . '/' . $file);
                if (!$path || !str_starts_with($path, $root) || !is_file($path) || !is_readable($path)
                    || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'ttf') {
                    throw new \RuntimeException('PDF font must be a readable .ttf file inside its template: ' . $file);
                }
                $signature[] = $path . ':' . filemtime($path) . ':' . filesize($path);
            }
        }

        $defaults = (new ConfigVariables())->getDefaults();
        $fontDefaults = (new FontVariables())->getDefaults();
        $config['fontDir'] = array_merge([$directory], (array) ($config['fontDir'] ?? $defaults['fontDir']));
        $config['fontdata'] = array_replace($fontDefaults['fontdata'], $config['fontdata'] ?? [], $fonts);
        // mPDF caches metrics by font family, not source path. Isolate templates
        // and font revisions so two templates can safely use the same family name.
        $config['tempDir'] .= '/fonts-' . substr(hash('sha256', implode("\n", $signature)), 0, 16);
        return $config;
    }
}
