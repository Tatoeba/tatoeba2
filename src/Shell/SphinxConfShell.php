<?php
/**
 *  Tatoeba Project, free collaborative creation of languages corpuses project
 *  Copyright (C) 2014  Gilles Bedel
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Shell;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use App\Lib\LanguagesLib;
use Cake\Console\Shell;


class SphinxConfShell extends Shell {

    private $tatoeba_languages;

    public $morphology = array(
        'deu' => 'libstemmer_deu',
        'spa' => 'libstemmer_spa',
        'fra' => 'libstemmer_fra',
        'nld' => 'libstemmer_nld',
        'por' => 'libstemmer_por',
        'rus' => 'libstemmer_rus',
        'fin' => 'libstemmer_fin',
        'ita' => 'libstemmer_ita',
        'tur' => 'libstemmer_tur',
        'swe' => 'libstemmer_swe',
        'eng' => 'libstemmer_eng',
        'dan' => 'libstemmer_dan', # Danish
        'hun' => 'libstemmer_hun', # Hungarian
        'ron' => 'libstemmer_ron', # Romanian
        'nob' => 'libstemmer_nor', # Norwegian (Bokmål)
    );

    public $charsetTable = array(
        # Ascii
        '0..9', 'a..z', '_', 'A..Z->a..z',
        # Searchable symbols
        '$',
        # Latin-1 Supplement, with case folding (0080-00FF)
        'U+C0..U+D6->U+E0..U+F6', 'U+D8..U+DE->U+F8..U+FE', 'U+DF', 'U+E0..U+F6', 'U+F8..U+FF',
        # Latin extended-A, with case folding (0100-017F)
        'U+100..U+137/2', 'U+138', 'U+139..U+148/2', 'U+149', 'U+14A..U+177/2', 'U+178->U+FF', 'U+179..U+17E/2', 'U+017F',
        # Latin extended-B, with case folding (0180-024F)
        'U+0180', 'U+0181->U+0253', 'U+0182..U+0185/2', 'U+0186->U+0254', 'U+0187->U+0188', 'U+0188',
        'U+0189->U+0256', 'U+018A->U+0257', 'U+018B->U+018C', 'U+018C', 'U+018D', 'U+018E->U+01DD', 'U+018F->U+0259',
        'U+0190->U+025B', 'U+0191->U+0192', 'U+0192', 'U+0193->U+0260', 'U+0194->U+0263', 'U+0195', 'U+0196->U+0269', 'U+0197->U+0268', 'U+0198->U+0199',
        'U+0199..U+019B', 'U+019C->U+026F', 'U+019D->U+0272', 'U+019E', 'U+019F->U+0275',
        'U+01A0..U+01A5/2', 'U+01A6->U+0280', 'U+01A7->U+01A8', 'U+01A8',
        'U+01A9->U+0283', 'U+01AA', 'U+01AB', 'U+01AC->U+01AD', 'U+01AD', 'U+01AE->U+0288', 'U+01AF->U+01B0',
        'U+01B0', 'U+01B1->U+028A', 'U+01B2->U+028B', 'U+01B3..U+01B6/2', 'U+01B7->U+0292', 'U+01B8->U+01B9',
        'U+01BA', 'U+01BB', 'U+01BC->U+01BD', 'U+01BD..U+01BF',
        'U+01C0..U+01C3', 'U+01C4->U+01C6', 'U+01C5', 'U+01C6', 'U+01C7->U+01C9', 'U+01C8',
        'U+01C9..U+01CC', 'U+01CD..U+01DC/2', 'U+01DE..U+01EF/2',
        'U+01F0', 'U+01F1->U+01F3', 'U+01F2', 'U+01F3', 'U+01F4->U+01F5', 'U+01F5', 'U+01F6->U+0195', 'U+01F7->U+01BF', 'U+01F8..U+021F/2',
        'U+0220->U+019E', 'U+0221', 'U+0222..U+0233/2', 'U+0234..U+0238',
        'U+0239', 'U+023A->U+2C65', 'U+023B->U+023C', 'U+023C', 'U+023D->U+019A', 'U+023E->U+2C66', 'U+023F',
        'U+0240', 'U+0241->U+0242', 'U+0242', 'U+0243->U+0180', 'U+0244->U+0289', 'U+0245->U+028C', 'U+0246..U+024F/2',
        # Latin Extended Additional, with case folding (1E00-1EFF)
        'U+1E00..U+1E95/2', 'U+1E96..U+1E9F', 'U+1EA0..U+1EFF/2',
        # Combining Diacritical Marks
        'U+300..U+36F',
        # Arabic
        'U+621..U+63a', 'U+640..U+64a',
        'U+66e..U+66f', 'U+671..U+6d3', 'U+6d5', 'U+6e5..U+6e6', 'U+6ee..U+6ef', 'U+6fa..U+6fc', 'U+6ff',
        # Greek and Coptic
        'U+37a', 'U+386..U+389->U+3ac..U+3af', 'U+38c..U+38e->U+3cc..U+3ce', 'U+390', 'U+391..U+3a1->U+3b1..U+3c1',
        'U+3a3..U+3ab->U+3c3..U+3cb', 'U+3ac..U+3ce', 'U+3d0..U+3d7', 'U+3d8..U+3ef/2', 'U+3f0..U+3f3', 'U+3f4->U+3b8',
        'U+3f5', 'U+3f7..U+3f8/2', 'U+3f9->U+3f2', 'U+3fa..U+3fb/2', 'U+3fc..U+3ff',
        # Hebrew, Yiddish: alef through yod
        'U+5D0..U+5D9',
        # Hebrew, Yiddish: Fold final kaf into (non-final) kaf.
        'U+5DA->U+5DB', 'U+5DB',
        # Hebrew, Yiddish: lamed
        'U+5DC',
        # Hebrew, Yiddish: Fold final mem into (non-final) mem.
        'U+5DD->U+5DE', 'U+5DE',
        # Hebrew, Yiddish: Fold final nun into (non-final) nun.
        'U+5DF->U+5E0', 'U+5E0',
        # Hebrew, Yiddish: samekh through ayin
        'U+5E1..U+5E2',
        # Hebrew, Yiddish: Fold final pe into (non-final) pe.
        'U+5E3->U+5E4', 'U+5E4',
        # Hebrew, Yiddish: Fold final tsadi into (non-final) tsadi.
        'U+5E5->U+5E6', 'U+5E6',
        # Hebrew, Yiddish: qof through tav
        'U+5E7..U+5EA',
        # Yiddish digraphs
        'U+5F0..U+5F2',
        # Cyrillic: Russian range (also used by other languages)
        'U+410..U+42F->U+430..U+44F', 'U+430..U+44F',
        # Cyrillic: io and non-Russian letters
        'U+400..U+40F->U+450..U+45F', 'U+450..U+45F',
        # Cyrillic: first checkerboard range (alternate capital and small letters)
        'U+460..U+481/2',
        # Cyrillic: second checkerboard range
        'U+48A..U+4BF/2',
        # Cyrillic: palochka
        'U+4C0->U+4CF', 'U+4CF',
        # Cyrillic: third checkerboard range
        'U+4C1..U+4CE/2',
        # Cyrillic: fourth checkerboard range
        'U+4D0..U+4FF/2',
        # Georgian
        'U+10a0..U+10c5->U+2d00..U+2d25', 'U+10d0..U+10fa', 'U+10fc', 'U+2d00..U+2d25',
        # Bengali
        'U+980..U+9FC',
        # Devanagari + Devanagari Extended
        'U+900..U+963', 'U+966..U+97F', 'U+A8E0..U+A8FB',
        # Armenian + Alphabetic Presentation Forms (Armenian Small Ligatures)
        'U+531..U+58A', 'U+FB13..U+FB17',
        # Malayalam
        'U+D00..U+D77',
        # Ethiopic
        'U+1200..U+1248', 'U+124A..U+124D', 'U+1250..U+1256', 'U+1258', 'U+125A..U+125D', 'U+1260..U+1288',
        'U+128A..U+128D', 'U+1290..U+12B0', 'U+12B2..U+12B5', 'U+12B8..U+12BE', 'U+12C0', 'U+12C2..U+12C5',
        'U+12C8..U+12D6', 'U+12D8..U+1310', 'U+1312..U+1315', 'U+1318..U+135A', 'U+135D..U+135F', 'U+1369..U+137C',
        'U+1380..U+1399', 'U+2D80..U+2DA6', 'U+2DA8..U+2DAE', 'U+2DB0..U+2DB6', 'U+2DB8..U+2DBE', 'U+2DC0..U+2DC6',
        'U+2DC8..U+2DCE', 'U+2DD0..U+2DD6', 'U+2DD8..U+2DDE', 'U+AB01..U+AB06', 'U+AB09..U+AB0E', 'U+AB11..U+AB16',
        'U+AB20..U+AB26', 'U+AB28..U+AB2E',
        # Cherokee
        'U+13A0..U+13F4',
        # Mon (called Myanmar by Unicode)
        'U+1000..U+1049', 'U+104C..U+109F', 'U+AA60..U+AA7F', 'U+A9E0..U+A9FE',
        # Sinhala
        'U+0D82', 'U+0D83', 'U+0D85..U+0D96', 'U+0D9A..U+0DB1', 'U+0DB3..U+0DBB', 'U+0DBD', 'U+0DC0..U+0DC6',
        'U+0DCA', 'U+0DCF..U+0DD4', 'U+0DD6', 'U+0DD8..U+0DDF', 'U+0DE6..U+0DEF', 'U+0DF2', 'U+0DF3',
        'U+111E1..U+111E9', 'U+111EA..U+111F4',
        # Tamil
        'U+0B82', 'U+0B83', 'U+0B85..U+0B8A', 'U+0B8E..U+0B90', 'U+0B92..U+0B95', 'U+0B99', 'U+0B9A', 'U+0B9C',
        'U+0B9E', 'U+0B9F', 'U+0BA3', 'U+0BA4', 'U+0BA8..U+0BAA', 'U+0BAE..U+0BB9', 'U+0BBE..U+0BC2', 'U+0BC6..U+0BC8',
        'U+0BCA..U+0BCD', 'U+0BD0', 'U+0BD7', 'U+0BE6..U+0BFA',
        # Telugu
        'U+0C00..U+0C03', 'U+0C05..U+0C0C', 'U+0C0E..U+0C10', 'U+0C12..U+0C28', 'U+0C2A..U+0C39', 'U+0C3D..U+0C44',
        'U+0C46..U+0C48', 'U+0C4A..U+0C4D', 'U+0C55', 'U+0C56', 'U+0C58', 'U+0C59', 'U+0C60..U+0C63', 'U+0C66..U+0C6F',
        'U+0C78..U+0C7F',
        # Gurmukhi (one of the scripts for [Eastern] Punjabi)
        'U+0A01..U+0A03', 'U+0A05..U+0A0A', 'U+0A0F', 'U+0A10', 'U+0A13..U+0A19', 'U+0A1A..U+0A28', 'U+0A2A..U+0A30',
        'U+0A32', 'U+0A33', 'U+0A35', 'U+0A36', 'U+0A38', 'U+0A39', 'U+0A3C', 'U+0A3E..U+0A42', 'U+0A47', 'U+0A48',
        'U+0A4B..U+0A4D', 'U+0A51', 'U+0A59..U+0A5C', 'U+0A5E', 'U+0A66..U+0A75',
        # Gujarati
        'U+0A81..U+0A83', 'U+0A85..U+0A8D', 'U+0A8F..U+0A91', 'U+0A93..U+0AA8', 'U+0AAA..U+0AB0', 'U+0AB2', 'U+0AB3',
        'U+0AB5..U+0AB9', 'U+0ABC..U+0AC5', 'U+0AC7..U+0AC9', 'U+0ACB..U+0ACD', 'U+0AD0', 'U+0AE0..U+0AE3', 'U+0AE6..U+0AEF',
        # Latin fullwidth to halfwidth
        'U+FF10..U+FF19->U+30..U+39', 'U+FF21..U+FF3A->U+61..U+7A', 'U+FF41..U+FF5A->U+61..U+7A',
        # Hangul
        'U+1100..U+11FF', 'U+3130..U+318F', 'U+A960..U+A97F', 'U+AC00..U+D7FF',
        # Hangul halfwidth to fullwidth
        'U+FFA1..U+FFBE->U+3131..U+314E', 'U+FFC2..U+FFC7->U+314F..U+3154',
        'U+FFCA..U+FFCF->U+3155..U+315A', 'U+FFD2..U+FFD7->U+315B..U+3160', 'U+FFDA..U+FFDC->U+3161..U+3163',
        # Neo-Tifinagh (one of the Berber scripts)
        'U+2D30..U+2D67', 'U+2D6F',
        # Syriac (script of Assyrian)
        'U+0710..U+074A', 'U+074D..U+074F',
        # Odia/Oriya
        'U+0B01..U+0B03', 'U+0B05..U+0B0C', 'U+0B0F..U+0B10', 'U+0B13..U+0B28',
        'U+0B2A..U+0B30', 'U+0B32..U+0B33', 'U+0B35..U+0B39', 'U+0B3C..U+0B44',
        'U+0B47..U+0B48', 'U+0B4B..U+0B4D', 'U+0B56..U+0B57', 'U+0B5C..U+0B5D',
        'U+0B5F..U+0B63', 'U+0B66..U+0B77',
        # Kannada
        'U+0C80..U+0C8C', 'U+0C8E..U+0C90', 'U+0C92..U+0CA8', 'U+0CAA..U+0CB3',
        'U+0CB5..U+0CB9', 'U+0CBC..U+0CC4', 'U+0CC6..U+0CC8', 'U+0CCA..U+0CCD',
        'U+0CD5..U+0CD6', 'U+0CDE', 'U+0CE0..U+0CE3', 'U+0CE6..U+0CEF', 'U+0CF1..U+0CF2',
		# Dhivehi 
		'U+0780..U+07B1',
    );

    public $scriptsWithoutWordBoundaries = array(
        # CJK
        'U+3000..U+312F', 'U+3300..U+9FFF', 'U+F900..U+FAFF',
        'U+1B000..U+1B16F', 'U+20000..U+2EBEF', 'U+2F800..U+2FA1F',
        # Katakana halfwidth to fullwidth
        'U+FF66->U+30F2', 'U+FF67->U+30A1', 'U+FF68->U+30A3', 'U+FF69->U+30A5', 'U+FF6A->U+30A7',
        'U+FF6B->U+30A9', 'U+FF6C->U+30E3', 'U+FF6D->U+30E5', 'U+FF6E->U+30E7', 'U+FF6F->U+30C3',
        'U+FF70->U+30FC', 'U+FF71->U+30A2', 'U+FF72->U+30A4', 'U+FF73->U+30A6', 'U+FF74->U+30A8',
        'U+FF75->U+30AA', 'U+FF76->U+30AB', 'U+FF77->U+30AD', 'U+FF78->U+30AF', 'U+FF79->U+30B1',
        'U+FF7A->U+30B3', 'U+FF7B->U+30B5', 'U+FF7C->U+30B7', 'U+FF7D->U+30B9', 'U+FF7E->U+30BB',
        'U+FF7F->U+30BD', 'U+FF80->U+30BF', 'U+FF81->U+30C1', 'U+FF82->U+30C4', 'U+FF83->U+30C6',
        'U+FF84->U+30C8', 'U+FF85..U+FF8A->U+30CA..U+30CF', 'U+FF8B->U+30D2', 'U+FF8C->U+30D5',
        'U+FF8D->U+30D8', 'U+FF8E->U+30DB', 'U+FF8F..U+FF93->U+30DE..U+30E2', 'U+FF94->U+30E4',
        'U+FF95->U+30E6', 'U+FF96->U+30E8', 'U+FF97->U+30E9', 'U+FF98->U+30EA', 'U+FF99->U+30EB',
        'U+FF9A->U+30EC', 'U+FF9B->U+30ED', 'U+FF9C->U+30EF', 'U+FF9D->U+30F3',
        # Katakana Phonetic Extensions
        'U+31F0..U+31FF',
        # Lao (lao)
        'U+0E81', 'U+0E82', 'U+0E84', 'U+0E87', 'U+0E88', 'U+0E8A', 'U+0E8D', 'U+0E94..U+0E97', 'U+0E99..U+0E9F',
        'U+0EA1..U+0EA3', 'U+0EA5', 'U+0EA7', 'U+0EAA', 'U+0EAB', 'U+0EAD', 'U+0EAE', 'U+0EB0..U+0EB9', 'U+0EBB',
        'U+0EBC', 'U+0EBD', 'U+0EC0..U+0EC4', 'U+EC6', 'U+0EC8..U+0ECD', 'U+0ED0..U+0ED9', 'U+0EDC..U+0EDF',
        # Tibetan (bod) (not sure about marks and signs)
        'U+0F00', 'U+0F20..U+0F33', 'U+0F40..U+0F47', 'U+0F49..U+0F6C', 'U+0F71..U+0F87', 'U+0F90..U+0F97',
        'U+0F99..U+0FBC', 'U+0FD0..U+0FD2',
        # Khmer (khm)
        'U+1780..U+17D2', 'U+17E0..U+17E9', 'U+17F0..U+17F9', 'U+19E0..U+19FF',
        # Thai (tha)
        'U+0E01..U+0E2E', 'U+0E30..U+0E3A', 'U+0E40..U+0E4E', 'U+0E50..U+0E59',
        # Yi syllables, used by Nuosu (iii)
        'U+A000..U+A48C',
        # Javanese (jav)
        'U+A980..U+A9C0', 'U+A9CF..U+A9D9',
    );

    public $regexpFilter = array(
      '\$(\d+) => $ \1',
      '(\d+)\$ => \1 $'
    );

    public $indexExtraOptions = array();

    private $dbConfig;
    private $sphinxConfig;

    public function __construct() {
        parent::__construct();

        $this->indexExtraOptions['lat'] =
            "
        charset_table = ".implode(', ', array_merge(
            array(
                'A..H->a..h', 'a..h',
                'I->i', 'J->i', 'j->i', 'i',
                'K..T->k..t', 'k..t',
                'U->v', 'u->v', 'V->v', 'W->v', 'w->v', 'v',
                'X..Z->x..z', 'x..z',
                'U+100->a',       # cap a + macron -> small a
                'U+101->a',       # small a + macron -> small a
                'U+102->a',       # cap a + breve -> small a
                'U+103->a',       # small a + breve -> small a
                'U+C1->a',        # cap a + acute -> small a
                'U+E1->a',        # small a + acute -> small a
                'U+112->e',       # same pattern as for a
                'U+113->e',
                'U+114->e',
                'U+115->e',
                'U+C9->e',
                'U+E9->e',
                'U+12A->i',       # same pattern as for a
                'U+12B->i',
                'U+12C->i',
                'U+12D->i',
                'U+CD->i',
                'U+ED->i',
                'U+14C->o',       # same pattern as for a
                'U+14D->o',
                'U+14E->o',
                'U+14F->o',
                'U+D3->o',
                'U+F3->o',
                'U+16A->v',       # cap u + macron -> small v
                'U+16B->v',       # small u + macron -> small v
                'U+16C->v',       # cap u + breve -> small v
                'U+16D->v',       # small u + breve -> small v
                'U+DA->v',        # cap u + acute -> small v
                'U+FA->v',        # small u + acute -> small v
                'U+1E2->U+E6',    # cap ae + macron -> small ae
                'U+1E3->U+E6',    # small ae + macron -> small ae
                'U+C6->U+E6',     # cap ae -> small ae
                'U+E6'            # small ae
                ),
            array_filter(
                $this->charsetTable,
                function($v) { return
                    $v != 'A..Z->a..z' &&
                    $v != 'a..z' &&
                    $v != 'U+C0..U+D6->U+E0..U+F6' &&
                    $v != 'U+D8..U+DE->U+F8..U+FE' &&
                    $v != 'U+E0..U+F6' &&
                    $v != 'U+F8..U+FF' &&
                    $v != 'U+100..U+177/2' &&
                    $v != 'U+01DE..U+01EF/2' &&
                    $v != 'U+300..U+36F'
                ; }
            )
        ))."
        ignore_chars = U+AD, U+301\n";

        /* Lojban uses apostrophe as a regular character
         * and sometimes replaces it with h */
        $this->indexExtraOptions['jbo'] =
            "
        charset_table = ".implode(', ', array_merge(
                array(
                    'A..G->a..g', 'a..g',
                    "H->'", "h->'", "'",
                    'I..Z->i..z', 'i..z'
                ),
                array_filter(
                    $this->charsetTable,
                    function($v) { return $v != 'A..Z->a..z' && $v != 'a..z'; }
                )
            ))."\n";

        /* Russian uses diacritics only to stress words and it's easier
         * to search if they are ignored. Since all the Russian diacritics
         * are not single characters but combining characters (e.g. и + ´ = и́)
         * we simply ignore the *´* combining char (U+301) so that characters
         * are considered as not having a diacritic. We also ignore soft hyphen. */
        $this->indexExtraOptions['rus'] =
            "
        charset_table = ".implode(', ', array_merge(
            array('U+300', 'U+302..U+36F'),
            array_filter(
                $this->charsetTable,
                function($v) { return $v != 'U+300..U+36F'; }
            )
        ))."
        ignore_chars = U+AD, U+301\n";

        /* Turkish
         *   Vowels:
         *     Fold dotless capital I into lowercase dotless i ('I->U+131')
         *     Fold dotted capital I into lowercase dotted i ('U+130->i')
         *     Fold {A,a,U,u} + ^ (U+C2, U+E2, U+DB, U+FB) into letters without ^
         *     Fold {I,i} + ^ (U+CE, U+EE) into lowercase dotless i without ^
         *     Fold O + diaeresis (U+D6) into o + diaeresis (U+F6)
         *   Consonants:
         *     Fold C + cedilla (U+C7) into c + cedilla (U+E7)
         *     Fold G + breve (U+11E) into g + breve (U+11F)
         *     Fold S + cedilla (U+15E) into s + cedilla (U+15F)
         */
        $this->indexExtraOptions['tur'] =
            "
        charset_table = ".implode(', ', array_merge(
            array(
                'A->a', 'U+C2->a', 'U+E2->a', 'a', # case-folding: a with/without circumflex
                'B->b', 'b',
                'C->c', 'c',
                'U+C7->U+E7', 'U+E7', # case-folding: c-cedilla
                'D..H->d..h', 'd..h',
                'I->U+0131', 'U+CE->U+0131', 'U+EE->U+0131', 'U+0131', # case-folding: dotless i
                'U+CE->U+0131', 'U+EE->U+0131', # strip circumflex from I,i and map to dotless i
                'U+0130->i', 'i', # case-folding: dotted i
                'J..N->j..n', 'j..n',
                'O->o', 'o',
                'U+D6->U+F6', 'U+F6', # case-folding: o-umlaut
                'P..T->p..t', 'p..t',
                'U->u', 'U+DB->u', 'U+FB->u', 'u', # case-folding: u with/without circumflex
                'U+DC->U+FC', 'U+FC',   # case-folding: u-umlaut
                'V..Z->v..z', 'v..z',
                'U+11E->U+11F', 'U+11F', # case-folding: g-breve
                'U+15E->U+15F', 'U+15F' # case-folding: s-cedilla
            ),
            array_filter(
                $this->charsetTable,
                function($v) {
                    return $v != 'A..Z->a..z' && $v != 'a..z'
                    && $v != 'U+C0..U+D6->U+E0..U+F6' && $v != 'U+E0..U+F6' # Latin-1 supplement
                    && $v != 'U+D8..U+DE->U+F8..U+FE' && $v != 'U+F8..U+FF' # Latin-1 supplement
                    && $v != 'U+100..U+177/2' # A-macron to y-circumflex
                    && $v != 'U+300..U+36F'   # combining characters
                ; }
            )
        ))."\n";

        /* Remove the kanji part in Japanese readings. Note that this regexp
           will also affect Japanese sentences, but hopefully it will have
           no effect because they don’t use this [kanji|reading] syntax. */
        $this->indexExtraOptions['jpn'] =
            "
        regexp_filter = \[[^|]*\| =>";

        foreach ($this->morphology as $lang => $morphology) {
            if (!isset($this->indexExtraOptions[$lang])) {
                $this->indexExtraOptions[$lang] = "";
            }
            $this->indexExtraOptions[$lang] .= "
        index_exact_words       = 1
        morphology              = $morphology
        min_stemming_len        = 4";
        }
    }

    // In the following, the characters U+5B0..U+5C5, U+5C7 within
    // ignore_chars are Hebrew/Yiddish vowels, which should be ignored in
    // searches. No other language uses them, so ignoring them for all
    // languages should be safe.
    private function conf_beginning() {
        $charset_table_opt = implode(", ", $this->charsetTable);
        $ngram_chars_opt = implode(', ', $this->scriptsWithoutWordBoundaries);
        $regexp_filter = "";
        foreach ($this->regexpFilter as $regex) {
            $regexp_filter .= "    regexp_filter           = $regex\n";
        }

        return <<<EOT
source default
{
    type                     = mysql
    sql_host                 = localhost
    sql_user                 = {$this->dbConfig['username']}
    sql_pass                 = {$this->dbConfig['password']}
    sql_db                   = {$this->dbConfig['database']}
    sql_sock                 = {$this->sphinxConfig['socket']}
    sql_query_pre            = SET NAMES utf8
    sql_query_pre            = SET SESSION query_cache_type=OFF

}


index common_index
{
    index_field_lengths     = 1
    ignore_chars            = U+AD, U+5B0..U+5C5, U+5C7
$regexp_filter
    charset_table           = $charset_table_opt
    min_infix_len           = 3
    ngram_len               = 1
    ngram_chars             = $ngram_chars_opt
}

#################################################


EOT;
    }

    private function conf_language_indexes($languages) {
        $conf = '';
        $sourcePath = $this->sphinxConfig['indexdir'];
        foreach ($languages as $lang => $name) {
            foreach (array('main', 'delta') as $type) {
                $parent = array(
                    "${lang}_main_src" => 'default',
                    "${lang}_delta_src" => "${lang}_main_src"
                );
                $source = ($type == 'main') ? "${lang}_main_src" : "${lang}_delta_src";
                $conf .= "
    # $name ($type)
    source $source : $parent[$source]
    {
        sql_query_pre = SET NAMES utf8
        sql_query_pre = SET SESSION query_cache_type=OFF
        sql_query_pre = SET SESSION group_concat_max_len = 1024*1024";

                if ($type == 'main') {
                    $conf .= "
        sql_query_pre = DELETE FROM reindex_flags \
                            WHERE lang = '$lang'";
                } else {
                    $conf .= "
        sql_query_pre = UPDATE reindex_flags SET indexed = 1 \
                        WHERE lang = '$lang' \
                        AND indexed = 0";
                }

                $delta_join = ($type == 'main') ?
                    '' :
                    'join reindex_flags on reindex_flags.sentence_id = sent_start.id and reindex_flags.indexed = 1';
                $conf .= "
        sql_query_range = select min(id), max(id) from sentences
        sql_range_step = 100000
        sql_query = \
            select \
                r.id, r.text, r.created, r.modified, r.user_id, r.ucorrectness, r.has_audio, \
                GROUP_CONCAT(distinct tags.tag_id) as tags_id, \
                GROUP_CONCAT(distinct lists.sentences_list_id) as lists_id, \
                CONCAT('[', COALESCE(GROUP_CONCAT(distinct r.trans),''), ']') as trans \
            from ( \
                select \
                    sent_start.id as id, \
                    sent_start.text as text, \
                    UNIX_TIMESTAMP(sent_start.created) as created, \
                    UNIX_TIMESTAMP(sent_start.modified) as modified, \
                    sent_start.user_id as user_id, \
                    (sent_start.correctness + 128) as ucorrectness, \
                    (COUNT(audios_sent_start.id) > 0) as has_audio, \
                    \
                    CONCAT('{', \
                        'l:\"',sent_end.lang,'\",', \
                        'd:',MIN( IF(trans.sentence_id = transtrans.translation_id,1,2) ),',', \
                        'u:',COALESCE(sent_end.user_id, 0),',', \
                        'c:',sent_end.correctness + 1,',', \
                        'a:',COUNT(audios_sent_end.id) > 0, \
                    '}') as trans \
                from \
                    sentences sent_start \
                $delta_join\
                left join \
                    sentences_translations as trans \
                    on trans.sentence_id = sent_start.id \
                left join \
                    sentences_translations as transtrans \
                    on trans.translation_id = transtrans.sentence_id \
                left join \
                    sentences sent_end ON sent_end.id = \
                    IF(trans.sentence_id = transtrans.translation_id, \
                       trans.translation_id, \
                       transtrans.translation_id) \
                left join \
                    audios audios_sent_end ON sent_end.id = audios_sent_end.sentence_id \
                left join \
                    audios audios_sent_start ON sent_start.id = audios_sent_start.sentence_id \
                where \
                    sent_start.lang = '$lang' \
                    and sent_start.id >= \$start and sent_start.id <= \$end \
                group by id, sent_end.id \
            ) r \
            left join \
                tags_sentences tags on tags.sentence_id = r.id \
            left join \
                sentences_sentences_lists lists on lists.sentence_id = r.id \
            group by id

        sql_attr_timestamp = created
        sql_attr_timestamp = modified
        sql_attr_uint = user_id".
            /* "correctness" is an 8-bit signed integer whereas Sphinx only allows
             * unsigned intgerers (actually it allows 64-bit signed integers "bigint"s
             * but it’s a waste of space). So we add 128 an treat it as unsigned,
             * and that’s why the attribute is called "ucorrectness".
             */
        "
        sql_attr_uint = ucorrectness
        sql_attr_bool = has_audio
        sql_attr_multi = uint tags_id from field; SELECT id FROM tags ;
        sql_attr_multi = uint lists_id from field; SELECT id FROM sentences_lists ;
        sql_attr_json = trans

        sql_joined_field = \
            transcription from query; \
            select sentence_id, text from transcriptions order by sentence_id asc
    }
";
                // generate index for this pair
                $index = ($type == 'main') ?
                    "${lang}_main_index : common_index" :
                    "${lang}_delta_index : ${lang}_main_index";
                $conf .= "
    index $index
    {
        source = $source
        path = " . $sourcePath . DIRECTORY_SEPARATOR . $lang . '_' . $type;

                if ($type == 'main') {
                    if (isset($this->indexExtraOptions[$lang])) {
                        $conf .= $this->indexExtraOptions[$lang];
                    }
                } else {
                    $conf .= "
        killlist_target = ${lang}_main_index:id";
                }
                $conf .= "
    }
";
            }
        }

        $conf .= "

index und_index : common_index
{
    type = distributed

    ";
        foreach ($languages as $lang => $name) {
            $conf .= "    local           = ${lang}_main_index\n";
            $conf .= "    local           = ${lang}_delta_index\n";
        }

        $conf .= "
}
";
        return $conf;
    }

    public function conf_ending() {
        $sphinxLogDir = $this->sphinxConfig['logdir'];
        $log_opt = $sphinxLogDir . DIRECTORY_SEPARATOR . 'searchd.log';
        $query_log_opt = $sphinxLogDir . DIRECTORY_SEPARATOR . 'query.log';

        $conf = <<<EOT
indexer
{
    mem_limit               = 64M
}


searchd
{
    listen                  = {$this->sphinxConfig['host']}:{$this->sphinxConfig['port']}
    listen                  = localhost:{$this->sphinxConfig['sphinxql_port']}:mysql41
    log                     = $log_opt
    query_log               = $query_log_opt
    binlog_path             = {$this->sphinxConfig['binlog_path']}
    read_timeout            = 5

    pid_file                = {$this->sphinxConfig['pidfile']}
    seamless_rotate         = 1
    preopen_indexes         = 1
    unlink_old              = 1
}

EOT;
        return $conf;
    }

    public function conf($only = array()) {
        $languages = LanguagesLib::languagesInTatoeba();
        if ($only) {
            $languages = array_intersect_key($languages, array_flip($only));
        }
        $conf = '';
        $conf .= $this->conf_beginning();
        $conf .= $this->conf_language_indexes($languages);
        $conf .= $this->conf_ending();
        return $conf;
    }

    public function main() {
        $this->dbConfig = ConnectionManager::get('default')->config();
        $this->sphinxConfig = Configure::read('Sphinx');
        
        echo $this->conf($this->args);
    }
}
