<?php
namespace App\Model\Entity;

trait FuriganaTrait
{
    /**
     * Transforms "[kanji|reading]" to HTML <ruby> tags
     */
    public function rubify($formatted) {
        return preg_replace_callback(
            '/\[([^|]*)\|([^\]]*)\]/',
            function ($matches) {
               $kanjis = preg_split('//u', $matches[1], null, PREG_SPLIT_NO_EMPTY);
               $readings = explode('|',$matches[2]);
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
               $ruby = '';
               for ($i = 0; $i < count($kanjis); $i++) {
                   $kanji = htmlentities($kanjis[$i]);
                   $reading = htmlentities($readings[$i]);
                   $ruby .= "<ruby>$kanji<rp>（</rp><rt>$reading</rt><rp>）</rp></ruby>";
               }
               return $ruby;
            },
            $formatted);
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