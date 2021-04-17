<?php
namespace App\Filter;

use MiniAsset\Filter\AssetFilter;

class TatoebaFlagsFilter extends AssetFilter
{
    private $passThrough = [ "sprite_header", "sprite_footer" ];

    public function input($filename, $content)
    {
        $filename = explode('/', $filename);
        $filename = end($filename);
        if (in_array($filename, $this->passThrough)) {
            return trim($content);
        }
        $identifier = explode('.', $filename)[0];

        $content = $this->forceViewBox($content);
        $content = $this->removeXmlDeclaration($content);
        $content = $this->removeXmlNs($content);
        $content = $this->changeSvgIntoSymbol($identifier, $content);

        return trim($content);
    }

    private function removeXmlNs($content) {
        return preg_replace("/[[:space:]]*xmlns(:[\-a-zA-Z]+)?=[^>[:space:]]+/", '', $content);
    }

    private function removeXmlDeclaration($content) {
        return preg_replace('/^<\?[^>]+\?>[[:space:]]*/', '', $content);
    }

    private function changeSvgIntoSymbol($identifier, $content) {
        $content = preg_replace('/<svg/', "<symbol id=\"$identifier\"", $content);
        return preg_replace('/<\/svg>/', '</symbol>', $content);
    }

    private function forceViewBox($content) {
        $xml = new \DOMDocument();
        $xml->loadXML($content);
        if ($xml->firstChild) {
            $h = $xml->firstChild->getAttribute('height');
            $w = $xml->firstChild->getAttribute('width');
            if ($w && $h) {
                $xml->firstChild->removeAttribute('width');
                $xml->firstChild->removeAttribute('height');
                if (!$xml->firstChild->hasAttribute('viewBox')) {
                    $xml->firstChild->setAttribute('viewBox', "0 0 $w $h");
                }
            }
        }
        return $xml->saveXML();
    }
}
