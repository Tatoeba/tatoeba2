<?php
namespace App\Filter;

use MiniAsset\Filter\AssetFilter;

class TatoebaFlagsFilter extends AssetFilter
{
    private $knownIds = [];
    private $lastIdAsInt = 0;

    public function input($path, $content)
    {
        $filename = basename($path);

        if ($this->isTmpTarget()) {
            $content = $this->removeSymbols($content);
            $content = $this->fixDuplicateIds($content);
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

    private function intToStr($i) {
        $str = "";
        do {
            $str .= base_convert($i % 36, 10, 36);
            $i = intdiv($i, 36);
        } while ($i > 0);
        return strrev($str);
    }

    private function getNewId($toAvoid) {
        $toAvoid = array_merge($this->knownIds, $toAvoid);
        do {
            $id = $this->intToStr($this->lastIdAsInt++);
        } while (in_array($id, $toAvoid));
        return $id;
    }

    private function renameIdRefAttr($oldId, $newId, $ns, $tagName, $attrName, $attrValue) {
        $hrefElem = ['linearGradient', 'radialGradient', 'pattern', 'textPath', 'use'];
        if ($attrName == 'href' && in_array($tagName, $hrefElem)) {
            // href and xlink:href attributes only uses "#id" format
            if ($attrValue == "#$oldId") {
                return "#$newId";
            }
        } elseif (is_null($ns)) {
            // some other XML attributes use url(#id) format
            $_oldId = preg_quote($oldId, '/');
            $newAttrValue = preg_replace(
                "/url\((\")?#$_oldId(\")?\)/",
                "url(\1#$newId\2)",
                $attrValue,
                -1,
                $count
            );
            if ($count) {
                return $newAttrValue;
            }
        }
        return null;
    }

    private function renameId($svg, $oldId, $newId) {
        $this->walkSVG($svg, function($node) use ($oldId, $newId) {
            // Rename ID itself
            $id = (string)$node->attributes()->id;
            if ($id == $oldId) {
                $node->attributes()->id = $newId;
            }

            // Rename ID references
            $tagName = $node->getName();
            foreach ([null, "http://www.w3.org/1999/xlink"] as $ns) {
                foreach ($node->attributes($ns) as $attrName => $attrValue) {
                    $newAttrValue = $this->renameIdRefAttr($oldId, $newId, $ns, $tagName, $attrName, $attrValue);
                    if ($newAttrValue) {
                        $node->attributes($ns)->$attrName = $newAttrValue;
                    }
                }
            }
        });
    }

    private function removeSymbols($content) {
        $svg = new \SimpleXMLIterator($content);
        $this->walkSVG($svg, function($node) {
            unset($node->symbol);
        });
        return $svg->asXML();
    }

    private function fixDuplicateIds($content) {
        $svg = new \SimpleXMLIterator($content);

        $currentFileIds = $this->getAllIds($svg);
        foreach ($currentFileIds as $id) {
            if (in_array($id, $this->knownIds)) {
                $newId = $this->getNewId($currentFileIds);
                $this->renameId($svg, $id, $newId);
                $this->knownIds[] = $newId;
            } else {
                $this->knownIds[] = $id;
            }
        }

        return $svg->asXML();
    }
}
