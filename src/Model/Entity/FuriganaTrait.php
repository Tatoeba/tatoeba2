<?php
namespace App\Model\Entity;

trait FuriganaTrait
{
    /**
     * Transforms "[kanji|reading]" to HTML <ruby> tags
     */
    public function rubify($formatted) {
        $ruby = '';
        $parts = preg_split(
            '/\[([^|]*)\|([^\]]*)\]/',
            $formatted, -1, PREG_SPLIT_DELIM_CAPTURE);
        // PREG_SPLIT_DELIM_CAPTURE inserts the two capture groups between the
        // non-matching parts, so we walk the result three steps at a time and
        // distinguish between match vs non-match based on the index.
        for($part = 0; $part < count($parts); $part += 3) {
            $ruby .= htmlentities($parts[$part]);
            if ($part+2 < count($parts)) {
               $kanjis = preg_split('//u', $parts[$part+1], null, PREG_SPLIT_NO_EMPTY);
               $readings = explode('|', $parts[$part+2]);
               for ($i = 0; $i < count($readings); $i++) {
                   if ($i > 0 && empty($readings[$i])) {
                       if (array_key_exists($i, $kanjis)) {
                           array_splice($kanjis, $i-1, 2, $kanjis[$i-1].$kanjis[$i]);
                       }
                       array_splice($readings, $i, 1);
                       $i--;
                   }
               }
               while (count($kanjis) > count($readings)) {
                   $last = array_pop($kanjis);
                   array_push($kanjis, array_pop($kanjis).$last);
               }
               for ($i = 0; $i < count($kanjis); $i++) {
                   $kanji = htmlentities($kanjis[$i]);
                   $reading = htmlentities($readings[$i]);
                   $ruby .= "<ruby>$kanji<rp>（</rp><rt>$reading</rt><rp>）</rp></ruby>";
               }
            }
        }
        return $ruby;
    }

    /**
     * Transforms "[kanji|reading]" into kanji｛reading｝
     * and "[kanjikanji|reading|reading]" into kanjikanji｛reading｜reading｝
     */
    public function bracketify($formatted) {
        $formatted = preg_replace(
            '/\[([^|]*)\|([^\]]*)\]/',
            '$1｛$2｝',
            $formatted);
        return str_replace('|', '｜', $formatted);
    }
}
?>
