<?php
namespace App\Filter;

use MiniAsset\Filter\AssetFilter;
use RuntimeException;

/**
 * This filter is meant to be used in combination
 * with nginx module ngx_http_gzip_static_module.
 * It writes an extra gzip-compressed version of
 * the asset to a file suffixed with .gz
 *
 * For this to work, you also need to add:
 *
 *   gzip_static on;
 *
 * to your nginx configuration.
 */
class GzipFilter extends AssetFilter
{
    public function output($filename, $content)
    {
        if (!function_exists('gzopen')) {
            throw new RuntimeException('Cannot compress asset without gzopen()');
        }
        $outfile = "$filename.gz";
        $fp = gzopen($outfile, 'w');
        if ($fp === FALSE) {
            throw new RuntimeException("Cannot write $outfile.");
        }
        gzwrite($fp, $content);
        gzclose($fp);
        printf("(also compressed version %s)\n", basename($outfile));
        return $content;
    }
}
