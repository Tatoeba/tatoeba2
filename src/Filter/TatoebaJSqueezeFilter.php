<?php
namespace App\Filter;

use Cake\Core\Configure;
use MiniAsset\Filter\JSqueezeFilter;

class TatoebaJSqueezeFilter extends JSqueezeFilter
{
    /**
     * We move minification from output() to input() so that we
     * can avoid minifying files which name ends with .min.js
     */
    public function input($file, $content)
    {
        if (!preg_match('/\.min\.js$/', $file)
            && (!Configure::read('debug') || php_sapi_name() === 'cli')) {
            $content = parent::output('', $content);
        }
        return $content;
    }

    /**
     * Files already minifed in input(). Purposely do nothing.
     */
    public function output($filename, $content)
    {
        return $content;
    }
}
