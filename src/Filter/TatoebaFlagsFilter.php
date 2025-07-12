<?php
namespace App\Filter;

use MiniAsset\Filter\AssetFilter;

class TatoebaFlagsFilter extends AssetFilter
{

    public function input($path, $content)
    {
        if ($this->isTmpTarget()) {
            $filename = basename($path);
            $identifier = explode('.', $filename)[0];
            return $this->svg2symbol($content, $identifier);
        } else {
            return $this->svgWrap($content);
        }
    }

    private function isTmpTarget() {
        $settings = $this->settings();
        return in_array($settings['target'], $settings['tmptargets'] ?? []);
    }

    private function svg2symbol($content, $identifier) {
        $content = $this->forceViewBox($content);
        $content = $this->removeXmlDeclaration($content);
        $content = $this->removeXmlNs($content);
        $content = $this->changeSvgIntoSymbol($identifier, $content);
        return trim($content);
    }

    private function svgWrap($content) {
        return <<<SVG
               <?xml version="1.0"?>
               <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
               $content
               </svg>
               SVG;
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
