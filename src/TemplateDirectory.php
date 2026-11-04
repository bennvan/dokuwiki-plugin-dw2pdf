<?php

namespace dokuwiki\plugin\dw2pdf\src;

/** Shared discovery and paths for bundled and persistent user templates. */
class TemplateDirectory
{
    /** @return array<string, string> Template names mapped to their directories. */
    public static function all(): array
    {
        $templates = [];
        // Later entries override bundled templates with the same name.
        foreach ([DOKU_PLUGIN . 'dw2pdf/tpl', DOKU_CONF . 'dw2pdf/tpl'] as $root) {
            if (!is_dir($root)) continue;
            foreach (scandir($root) as $name) {
                if (self::validName($name) && is_dir($root . '/' . $name)) {
                    $templates[$name] = $root . '/' . $name;
                }
            }
        }
        ksort($templates, SORT_NATURAL | SORT_FLAG_CASE);
        return $templates;
    }

    private static function validName(string $name): bool
    {
        // Accept directory names, never paths or traversal from the tpl URL parameter.
        return preg_match('/\A[\p{L}\p{N}_][\p{L}\p{N}_. -]*\z/u', $name) === 1;
    }

    public static function resolve(string $name): string
    {
        $templates = self::all();
        if (!isset($templates[$name])) {
            throw new \InvalidArgumentException('Unknown PDF template: ' . $name);
        }
        return $templates[$name];
    }

    /** Images under conf must be read from disk, never requested over HTTP. */
    public static function assetBase(string $directory): string
    {
        if (str_starts_with($directory, DOKU_PLUGIN . 'dw2pdf/tpl/')) {
            return DOKU_BASE . 'lib/plugins/dw2pdf/tpl/' . basename($directory) . '/';
        }
        return 'dw2pdf://' . $directory . '/';
    }

    /** Include directories so adding or deleting template files invalidates the PDF. */
    public static function dependencies(string $directory): array
    {
        $files = [$directory];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $file) {
            $files[] = $file->getPathname();
        }
        return $files;
    }
}
