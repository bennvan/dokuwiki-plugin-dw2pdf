<?php

namespace dokuwiki\plugin\dw2pdf\src;

use Mpdf\AssetFetcher;

/**
 * Wrapper for AssetFetcher which resolves DokuWiki media paths
 */
class DokuAssetFetcher extends AssetFetcher
{
    public function fetchDataFromPath($path, $originalSrc = null)
    {
        $resolved = (new MediaLinkResolver())->resolve($path);
        // Use the local file directly: mPDF can ignore originalSrc when the
        // request host includes a port and its basepathIsLocal check fails.
        if ($resolved) $path = $originalSrc = $resolved['path'];
        return parent::fetchDataFromPath($path, $originalSrc);
    }
}
