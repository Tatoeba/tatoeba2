<?php
namespace App\Filter;

use MiniAsset\Filter\AssetFilter;

class TatoebaFlagsFilter extends AssetFilter
{

    public function input($path, $content)
    {
        $filename = basename($path);

        if ($this->isTmpTarget()) {
            $identifier = explode('.', $filename)[0];
            return $this->svg2symbol($content, $identifier);
        } else {
            $content = $this->svgWrap($content);
            $this->checkIdDuplicates($filename, $content);
            return $content;
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

    private function walkSVG(\SimpleXMLIterator $node, callable $func)
    {
        $stack = [$node];
        while (!empty($stack)) {
            $elem = array_pop($stack);
            $func($elem);
            for ($elem->rewind(); $elem->valid(); $elem->next()) {
                array_unshift($stack, $elem->current());
            }
        }
    }

    private function getAllIds($svg) {
        $ids = [];
        $this->walkSVG($svg, function($node) use (&$ids) {
            $id = $node->attributes()->id;
            if (!is_null($id)) {
                $ids[] = (string)$id;
            }
        });
        return $ids;
    }

    private function checkIdDuplicates($filename, $content) {
        $svg = new \SimpleXMLIterator($content);
        $allIds = $this->getAllIds($svg);
        $dups = array_filter(
            array_count_values($allIds),
            fn($count) => $count > 1
        );
        if (!empty($dups)) {
            $dupsStr = implode(',', array_keys($dups));
            throw new \RuntimeException("id(s) $dupsStr used more than once in $filename");
        }
    }
}
