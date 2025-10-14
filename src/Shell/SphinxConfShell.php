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

    /**
     * Languages with stemming support
     *
     * This list should be updated according to what the
     * currently installed Snowball library supports.
     * Here is a quick and dirty command to figure it out:
     *
     *   strings /usr/bin/searchd | grep UTF_8_stem$ | sort
     *
     * About the language codes, note that we use ISO 639-3
     * while Snowball uses ISO 639-2, so the array goes like
     *
     *   '<iso3>' => 'libstemmer_<iso2>',
     *
     * See also https://github.com/snowballstem/snowball/blob/master/libstemmer/modules.txt
     */
    public $morphology = array(
        'ara' => 'libstemmer_ara', # Arabic
        'eus' => 'libstemmer_eus', # Basque
        'cat' => 'libstemmer_cat', # Catalan
        'dan' => 'libstemmer_dan', # Danish
        'nld' => 'libstemmer_nld', # Dutch
        'eng' => 'libstemmer_eng', # English
        'fin' => 'libstemmer_fin', # Finnish
        'fra' => 'libstemmer_fra', # French
        'deu' => 'lemmatize_de_all', # German
        'ell' => 'libstemmer_ell', # Greek
        'hin' => 'libstemmer_hin', # Hindi
        'hun' => 'libstemmer_hun', # Hungarian
        'ind' => 'libstemmer_ind', # Indonesian
        'gle' => 'libstemmer_gle', # Irish
        'ita' => 'libstemmer_ita', # Italian
        'lit' => 'libstemmer_lit', # Lithuanian
        'npi' => 'libstemmer_nep', # Nepali
        'nob' => 'libstemmer_nor', # Norwegian (Bokmål)
        'por' => 'libstemmer_por', # Portuguese
        'ron' => 'libstemmer_ron', # Romanian
        'rus' => 'libstemmer_rus', # Russian
        'spa' => 'libstemmer_spa', # Spanish
        'swe' => 'libstemmer_swe', # Swedish
        'tam' => 'libstemmer_tam', # Tamil
        'tur' => 'libstemmer_tur', # Turkish
    );

    public $charsetTable = array(
        # Ascii
        '0..9', 'a..z', '_', 'A..Z->a..z',
        # Searchable symbols
        '$',
        # Latin-1 Supplement, with case folding (0080-00FF)
        'U+A8->U+308', 'U+AA->U+61', 'U+AF->U+304', 'U+B2..U+B3->U+32..U+33', 'U+B4->U+301', 'U+B5->U+3BC', 'U+B8->U+327', 'U+B9->U+31', 'U+BA->U+6F',
        'U+C0..U+D6->U+E0..U+F6', 'U+D8..U+DE->U+F8..U+FE', 'U+DF', 'U+E0..U+F6', 'U+F8..U+FF',
        # Latin extended-A, with case folding (0100-017F)
        'U+100..U+137/2', 'U+138', 'U+139..U+148/2', 'U+149', 'U+14A..U+177/2', 'U+178->U+FF', 'U+179..U+17E/2', 'U+17F',
        # Latin extended-B, with case folding (0180-024F)
        'U+180', 'U+181->U+253', 'U+182..U+185/2', 'U+186->U+254', 'U+187->U+188', 'U+188',
        'U+189->U+256', 'U+18A->U+257', 'U+18B->U+18C', 'U+18C', 'U+18D', 'U+18E->U+1DD', 'U+18F->U+259',
        'U+190->U+25B', 'U+191->U+192', 'U+192', 'U+193->U+260', 'U+194->U+263', 'U+195', 'U+196->U+269', 'U+197->U+268', 'U+198->U+199',
        'U+199..U+19B', 'U+19C->U+26F', 'U+19D->U+272', 'U+19E', 'U+19F->U+275',
        'U+1A0..U+1A5/2', 'U+1A6->U+280', 'U+1A7->U+1A8', 'U+1A8',
        'U+1A9->U+283', 'U+1AA', 'U+1AB', 'U+1AC->U+1AD', 'U+1AD', 'U+1AE->U+288', 'U+1AF->U+1B0',
        'U+1B0', 'U+1B1->U+28A', 'U+1B2->U+28B', 'U+1B3..U+1B6/2', 'U+1B7->U+292', 'U+1B8->U+1B9', 'U+1B9',
        'U+1BA', 'U+1BB', 'U+1BC->U+1BD', 'U+1BD..U+1BF',
        'U+1C0..U+1C3', 'U+1C4->U+1C6', 'U+1C5', 'U+1C6', 'U+1C7->U+1C9', 'U+1C8',
        'U+1C9..U+1CC', 'U+1CD..U+1DC/2', 'U+1DD', 'U+1DE..U+1EF/2',
        'U+1F0', 'U+1F1->U+1F3', 'U+1F2', 'U+1F3', 'U+1F4->U+1F5', 'U+1F5', 'U+1F6->U+195', 'U+1F7->U+1BF', 'U+1F8..U+21F/2',
        'U+220->U+19E', 'U+221', 'U+222..U+233/2', 'U+234..U+238',
        'U+239', 'U+23A->U+2C65', 'U+23B->U+23C', 'U+23C', 'U+23D->U+19A', 'U+23E->U+2C66', 'U+23F',
        'U+240', 'U+241->U+242', 'U+242', 'U+243->U+180', 'U+244->U+289', 'U+245->U+28C', 'U+246..U+24F/2',
        # Latin extended-C, with case folding (2C60-2C7F)
        'U+2c60..U+2c61/2', 'U+2c62->U+26b', 'U+2c63->U+1d7d', 'U+2c64->U+27d', 'U+2c65..U+2c66', 'U+2c67..U+2c6c/2', 'U+2c6d->U+251', 'U+2c6e->U+271', 'U+2c6f->U+250', 'U+2c70->U+252', 'U+2c71', 'U+2c72..U+2c73/2', 'U+2c74', 'U+2c75..U+2c76/2', 'U+2c77..U+2c7b', 'U+2c7c->U+6a', 'U+2c7d->U+56', 'U+2c7e->U+23f', 'U+2c7f->U+240',
        # IPA Extensions
        'U+250..U+2AF',
        # Phonetic Extensions
        'U+1D00..U+1D2B', 'U+1D2C->U+61', 'U+1D2D->U+E6', 'U+1D2E->U+62', 'U+1D2F', 'U+1D30..U+1D31->U+64..U+65', 'U+1D32->U+1DD',
        'U+1D33..U+1D3A->U+67..U+6E', 'U+1D3B', 'U+1D3C->U+6F', 'U+1D3D->U+223', 'U+1D3E->U+70', 'U+1D3F->U+72', 'U+1D40..U+1D41->U+74..U+75',
        'U+1D42->U+77', 'U+1D43->U+61', 'U+1D44..U+1D45->U+250..U+251', 'U+1D46->U+1D02', 'U+1D47->U+62', 'U+1D48..U+1D49->U+64..U+65',
        'U+1D4A->U+259', 'U+1D4B..U+1D4C->U+25B..U+25C', 'U+1D4D->U+67', 'U+1D4E', 'U+1D4F->U+6B', 'U+1D50->U+6D', 'U+1D51->U+14B',
        'U+1D52->U+6F', 'U+1D53->U+254', 'U+1D54..U+1D55->U+1D16..U+1D17', 'U+1D56->U+70', 'U+1D57..U+1D58->U+74..U+75', 'U+1D59->U+1D1D',
        'U+1D5A->U+26F', 'U+1D5B->U+76', 'U+1D5C->U+1D25', 'U+1D5D..U+1D5F->U+3B2..U+3B4','U+1D60..U+1D61->U+3C6..U+3C7', 'U+1D62->U+69',
        'U+1D63->U+72', 'U+1D64..U+1D65->U+75..U+76', 'U+1D66..U+1D67->U+3B2..U+3B3', 'U+1D68->U+3C1', 'U+1D69..U+1D6A->U+3C6..U+3C7',
        'U+1D6B..U+1D77', 'U+1D78->U+43D', 'U+1D79..U+1D7F',
        # Latin Extended Additional, with case folding (1E00-1EFF)
        'U+1E00..U+1E95/2', 'U+1E96..U+1E9F', 'U+1EA0..U+1EFF/2',
        # Spacing Modifier Letters
        'U+2BB', 'U+2BF', 'U+2C0', 'U+2D0',
        # Combining Diacritical Marks
        'U+300..U+36F',
        # Arabic
        'U+620->U+64a', 'U+621', 'U+622->U+627', 'U+623->U+627', 'U+624', 'U+625->U+627', 'U+626..U+628', 'U+629->U+647', 'U+62a..U+63a',
        'U+641..U+648', 'U+649->U+64a', 'U+64a',
        'U+660..U+669', 'U+66e', 'U+66f', 'U+671..U+6a8', 'U+6a9->U+643', 'U+6aa..U+6bf',
        'U+6c0->U+647', 'U+6c1->U+647', 'U+6c2..U+6d3', 'U+6d5', 'U+6e5', 'U+6e6', 'U+6ee', 'U+6ef',
        'U+6f0..U+6f9->U+660..U+669', 'U+6fa..U+6fc', 'U+6ff',
        # Greek and Coptic (accents folded)
        'U+370..U+373/2', 'U+376..U+377/2', 'U+37b..U+37d', 'U+37f->U+3f3',
        'U+386->U+3b1', 'U+388->U+3b5', 'U+389->U+3b7', 'U+38a->U+3b9', 'U+38c->U+3bf', 'U+38e->U+3c5', 'U+38f->U+3c9', 'U+390->U+3b9', 'U+391..U+3a1->U+3b1..U+3c1',
        'U+3a3..U+3a9->U+3c3..U+3c9', 'U+3aa->U+3b9', 'U+3ab->U+3c5', 'U+3ac->U+3b1', 'U+3ad->U+3b5', 'U+3ae->U+3b7', 'U+3af->U+3b9', 'U+3b0->U+3c5', 'U+3b1..U+3c9', 'U+3ca->U+3b9', 'U+3cb->U+3c5', 'U+3cc->U+3bf', 'U+3cd->U+3c5', 'U+3ce->U+3c9', 'U+3cf->U+3d7', 'U+3d0->U+3b2', 'U+3d1->U+3b8',
        'U+3d2->U+3c5', 'U+3d3->U+3c5', 'U+3d4..U+3d5->U+3c5..U+3c6', 'U+3d6->U+3c0', 'U+3d7', 'U+3d8..U+3ef/2', 'U+3f0->U+3ba',
        'U+3f1..U+3f2->U+3c1..U+3c2', 'U+3f3', 'U+3f4->U+3b8',
        'U+3f5->U+3b5', 'U+3f7..U+3f8/2', 'U+3f9->U+3c3', 'U+3fa..U+3fb/2', 'U+3fc', 'U+3fd..U+3ff->U+37b..U+37d',
        # Greek Extended (accents folded)
        'U+1f00->U+3b1', 'U+1f01->U+3b1', 'U+1f02->U+3b1', 'U+1f03->U+3b1', 'U+1f04->U+3b1', 'U+1f05->U+3b1', 'U+1f06->U+3b1', 'U+1f07->U+3b1', 'U+1f08->U+3b1', 'U+1f09->U+3b1', 'U+1f0a->U+3b1', 'U+1f0b->U+3b1', 'U+1f0c->U+3b1', 'U+1f0d->U+3b1', 'U+1f0e->U+3b1', 'U+1f0f->U+3b1',
        'U+1f10->U+3b5', 'U+1f11->U+3b5', 'U+1f12->U+3b5', 'U+1f13->U+3b5', 'U+1f14->U+3b5', 'U+1f15->U+3b5', 'U+1f18->U+3b5', 'U+1f19->U+3b5', 'U+1f1a->U+3b5', 'U+1f1b->U+3b5', 'U+1f1c->U+3b5', 'U+1f1d->U+3b5',
        'U+1f20->U+3b7', 'U+1f21->U+3b7', 'U+1f22->U+3b7', 'U+1f23->U+3b7', 'U+1f24->U+3b7', 'U+1f25->U+3b7', 'U+1f26->U+3b7', 'U+1f27->U+3b7', 'U+1f28->U+3b7', 'U+1f29->U+3b7', 'U+1f2a->U+3b7', 'U+1f2b->U+3b7', 'U+1f2c->U+3b7', 'U+1f2d->U+3b7', 'U+1f2e->U+3b7', 'U+1f2f->U+3b7',
        'U+1f30->U+3b9', 'U+1f31->U+3b9', 'U+1f32->U+3b9', 'U+1f33->U+3b9', 'U+1f34->U+3b9', 'U+1f35->U+3b9', 'U+1f36->U+3b9', 'U+1f37->U+3b9', 'U+1f38->U+3b9', 'U+1f39->U+3b9', 'U+1f3a->U+3b9', 'U+1f3b->U+3b9', 'U+1f3c->U+3b9', 'U+1f3d->U+3b9', 'U+1f3e->U+3b9', 'U+1f3f->U+3b9',
        'U+1f40->U+3bf', 'U+1f41->U+3bf', 'U+1f42->U+3bf', 'U+1f43->U+3bf', 'U+1f44->U+3bf', 'U+1f45->U+3bf', 'U+1f48->U+3bf', 'U+1f49->U+3bf', 'U+1f4a->U+3bf', 'U+1f4b->U+3bf', 'U+1f4c->U+3bf', 'U+1f4d->U+3bf',
        'U+1f50->U+3c5', 'U+1f51->U+3c5', 'U+1f52->U+3c5', 'U+1f53->U+3c5', 'U+1f54->U+3c5', 'U+1f55->U+3c5', 'U+1f56->U+3c5', 'U+1f57->U+3c5', 'U+1f59->U+3c5', 'U+1f5b->U+3c5', 'U+1f5d->U+3c5', 'U+1f5f->U+3c5',
        'U+1f60->U+3c9', 'U+1f61->U+3c9', 'U+1f62->U+3c9', 'U+1f63->U+3c9', 'U+1f64->U+3c9', 'U+1f65->U+3c9', 'U+1f66->U+3c9', 'U+1f67->U+3c9', 'U+1f68->U+3c9', 'U+1f69->U+3c9', 'U+1f6a->U+3c9', 'U+1f6b->U+3c9', 'U+1f6c->U+3c9', 'U+1f6d->U+3c9', 'U+1f6e->U+3c9', 'U+1f6f->U+3c9',
        'U+1f70->U+3b1', 'U+1f71->U+3b1',
        'U+1f72->U+3b5', 'U+1f73->U+3b5',
        'U+1f74->U+3b7', 'U+1f75->U+3b7',
        'U+1f76->U+3b9', 'U+1f77->U+3b9',
        'U+1f78->U+3bf', 'U+1f79->U+3bf',
        'U+1f7a->U+3c5', 'U+1f7b->U+3c5',
        'U+1f7c->U+3c9', 'U+1f7d->U+3c9',
        'U+1f80->U+3b1', 'U+1f81->U+3b1', 'U+1f82->U+3b1', 'U+1f83->U+3b1', 'U+1f84->U+3b1', 'U+1f85->U+3b1', 'U+1f86->U+3b1', 'U+1f87->U+3b1', 'U+1f88->U+3b1', 'U+1f89->U+3b1', 'U+1f8a->U+3b1', 'U+1f8b->U+3b1', 'U+1f8c->U+3b1', 'U+1f8d->U+3b1', 'U+1f8e->U+3b1', 'U+1f8f->U+3b1',
        'U+1f90->U+3b7', 'U+1f91->U+3b7', 'U+1f92->U+3b7', 'U+1f93->U+3b7', 'U+1f94->U+3b7', 'U+1f95->U+3b7', 'U+1f96->U+3b7', 'U+1f97->U+3b7', 'U+1f98->U+3b7', 'U+1f99->U+3b7', 'U+1f9a->U+3b7', 'U+1f9b->U+3b7', 'U+1f9c->U+3b7', 'U+1f9d->U+3b7', 'U+1f9e->U+3b7', 'U+1f9f->U+3b7',
        'U+1fa0->U+3c9', 'U+1fa1->U+3c9', 'U+1fa2->U+3c9', 'U+1fa3->U+3c9', 'U+1fa4->U+3c9', 'U+1fa5->U+3c9', 'U+1fa6->U+3c9', 'U+1fa7->U+3c9', 'U+1fa8->U+3c9', 'U+1fa9->U+3c9', 'U+1faa->U+3c9', 'U+1fab->U+3c9', 'U+1fac->U+3c9', 'U+1fad->U+3c9', 'U+1fae->U+3c9', 'U+1faf->U+3c9',
        'U+1fb0->U+3b1', 'U+1fb1->U+3b1', 'U+1fb2->U+3b1', 'U+1fb3->U+3b1', 'U+1fb4->U+3b1', 'U+1fb6->U+3b1', 'U+1fb7->U+3b1', 'U+1fb8->U+3b1', 'U+1fb9->U+3b1', 'U+1fba->U+3b1', 'U+1fbb->U+3b1', 'U+1fbc->U+3b1',
        'U+1fbe->U+3b9',
        'U+1fc2->U+3b7', 'U+1fc3->U+3b7', 'U+1fc4->U+3b7', 'U+1fc6->U+3b7', 'U+1fc7->U+3b7',
        'U+1fc8->U+3b5', 'U+1fc9->U+3b5',
        'U+1fca->U+3b7', 'U+1fcb->U+3b7', 'U+1fcc->U+3b7',
        'U+1fd0->U+3b9', 'U+1fd1->U+3b9', 'U+1fd2->U+3b9', 'U+1fd3->U+3b9', 'U+1fd6->U+3b9', 'U+1fd7->U+3b9', 'U+1fd8->U+3b9', 'U+1fd9->U+3b9', 'U+1fda->U+3b9', 'U+1fdb->U+3b9',
        'U+1fe0->U+3c5', 'U+1fe1->U+3c5', 'U+1fe2->U+3c5', 'U+1fe3->U+3c5',
        'U+1fe4->U+3c1', 'U+1fe5->U+3c1',
        'U+1fe6->U+3c5', 'U+1fe7->U+3c5', 'U+1fe8->U+3c5', 'U+1fe9->U+3c5', 'U+1fea->U+3c5', 'U+1feb->U+3c5',
        'U+1fec->U+3c1',
        'U+1ff2->U+3c9', 'U+1ff3->U+3c9', 'U+1ff4->U+3c9', 'U+1ff6->U+3c9', 'U+1ff7->U+3c9',
        'U+1ff8->U+3bf', 'U+1ff9->U+3bf',
        'U+1ffa->U+3c9', 'U+1ffb->U+3c9', 'U+1ffc->U+3c9',
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
        # Cyrillic: combining marks
        'U+483..U+489',
        # Cyrillic: second checkerboard range
        'U+48A..U+4BF/2',
        # Cyrillic: palochka
        'U+4C0->U+4CF', 'U+4CF',
        # Cyrillic: third checkerboard range
        'U+4C1..U+4CE/2',
        # Cyrillic: fourth checkerboard range
        'U+4D0..U+4FF/2',
        # Cyrillic Supplement
        'U+500..U+52F/2',
        # Cyrillic Extended-A
        'U+2DE0..U+2DFF',
        # Cyrillic Extended-B
        'U+A640..U+A66D/2', 'U+A66E..U+A672', 'U+A674..U+A67D', 'U+A67F', 'U+A680..U+A69B/2', 'U+A69C..U+A69F',
        # Cyrillic Extended-C: historical variants
        'U+1C80->U+432', 'U+1C81->U+434', 'U+1C82->U+43E', 'U+1C83->U+441', 'U+1C84->U+442', 'U+1C85->U+442', 'U+1C86->U+44A', 'U+1C87->U+463', 'U+1C88->U+A64B',
        # Georgian
        'U+10a0..U+10c5->U+2d00..U+2d25', 'U+10d0..U+10fa', 'U+10fc', 'U+2d00..U+2d25',
        # Bengali
        'U+980..U+9FC',
        # Devanagari + Devanagari Extended
        'U+900..U+963', 'U+966..U+97F', 'U+A8E0..U+A8FB',
        # Armenian + Alphabetic Presentation Forms (Armenian Small Ligatures)
        'U+531..U+556->U+561..U+586', 'U+559', 'U+561..U+586', 'U+FB13..U+FB17',
        # Malayalam
        'U+D00..U+D7F',
        # Ethiopic
        'U+1200..U+1248', 'U+124A..U+124D', 'U+1250..U+1256', 'U+1258', 'U+125A..U+125D', 'U+1260..U+1288',
        'U+128A..U+128D', 'U+1290..U+12B0', 'U+12B2..U+12B5', 'U+12B8..U+12BE', 'U+12C0', 'U+12C2..U+12C5',
        'U+12C8..U+12D6', 'U+12D8..U+1310', 'U+1312..U+1315', 'U+1318..U+135A', 'U+135D..U+135F', 'U+1369..U+137C',
        'U+1380..U+1399', 'U+2D80..U+2DA6', 'U+2DA8..U+2DAE', 'U+2DB0..U+2DB6', 'U+2DB8..U+2DBE', 'U+2DC0..U+2DC6',
        'U+2DC8..U+2DCE', 'U+2DD0..U+2DD6', 'U+2DD8..U+2DDE', 'U+AB01..U+AB06', 'U+AB09..U+AB0E', 'U+AB11..U+AB16',
        'U+AB20..U+AB26', 'U+AB28..U+AB2E',
        # Cherokee
        'U+13A0..U+13EF->U+AB70..U+ABBF', 'U+13F0..U+13F5->U+13F8..U+13FD', 'U+13F8..U+13FD', 'U+AB70..U+ABBF',
        # Mon (called Myanmar by Unicode)
        'U+1000..U+1049', 'U+104C..U+109F', 'U+AA60..U+AA7F', 'U+A9E0..U+A9FE',
        # Sinhala
        'U+D82', 'U+D83', 'U+D85..U+D96', 'U+D9A..U+DB1', 'U+DB3..U+DBB', 'U+DBD', 'U+DC0..U+DC6',
        'U+DCA', 'U+DCF..U+DD4', 'U+DD6', 'U+DD8..U+DDF', 'U+DE6..U+DEF', 'U+DF2', 'U+DF3',
        'U+111E1..U+111E9', 'U+111EA..U+111F4',
        # Tamil
        'U+B82', 'U+B83', 'U+B85..U+B8A', 'U+B8E..U+B90', 'U+B92..U+B95', 'U+B99', 'U+B9A', 'U+B9C',
        'U+B9E', 'U+B9F', 'U+BA3', 'U+BA4', 'U+BA8..U+BAA', 'U+BAE..U+BB9', 'U+BBE..U+BC2', 'U+BC6..U+BC8',
        'U+BCA..U+BCD', 'U+BD0', 'U+BD7', 'U+BE6..U+BFA',
        # Telugu
        'U+C00..U+C03', 'U+C05..U+C0C', 'U+C0E..U+C10', 'U+C12..U+C28', 'U+C2A..U+C39', 'U+C3D..U+C44',
        'U+C46..U+C48', 'U+C4A..U+C4D', 'U+C55', 'U+C56', 'U+C58', 'U+C59', 'U+C60..U+C63', 'U+C66..U+C6F',
        'U+C78..U+C7F',
        # Gurmukhi (one of the scripts for [Eastern] Punjabi)
        'U+A01..U+A03', 'U+A05..U+A0A', 'U+A0F', 'U+A10', 'U+A13..U+A19', 'U+A1A..U+A28', 'U+A2A..U+A30',
        'U+A32', 'U+A33', 'U+A35', 'U+A36', 'U+A38', 'U+A39', 'U+A3C', 'U+A3E..U+A42', 'U+A47', 'U+A48',
        'U+A4B..U+A4D', 'U+A51', 'U+A59..U+A5C', 'U+A5E', 'U+A66..U+A75',
        # Gujarati
        'U+A81..U+A83', 'U+A85..U+A8D', 'U+A8F..U+A91', 'U+A93..U+AA8', 'U+AAA..U+AB0', 'U+AB2', 'U+AB3',
        'U+AB5..U+AB9', 'U+ABC..U+AC5', 'U+AC7..U+AC9', 'U+ACB..U+ACD', 'U+AD0', 'U+AE0..U+AE3', 'U+AE6..U+AEF',
        # Latin fullwidth to halfwidth
        'U+FF10..U+FF19->U+30..U+39', 'U+FF21..U+FF3A->U+61..U+7A', 'U+FF41..U+FF5A->U+61..U+7A',
        # Neo-Tifinagh (one of the Berber scripts)
        'U+2D30..U+2D67', 'U+2D6F',
        # Syriac (script of Assyrian)
        'U+710..U+74A', 'U+74D..U+74F',
        # Odia/Oriya
        'U+B01..U+B03', 'U+B05..U+B0C', 'U+B0F..U+B10', 'U+B13..U+B28',
        'U+B2A..U+B30', 'U+B32..U+B33', 'U+B35..U+B39', 'U+B3C..U+B44',
        'U+B47..U+B48', 'U+B4B..U+B4D', 'U+B56..U+B57', 'U+B5C..U+B5D',
        'U+B5F..U+B63', 'U+B66..U+B77',
        # Kannada
        'U+C80..U+C8C', 'U+C8E..U+C90', 'U+C92..U+CA8', 'U+CAA..U+CB3',
        'U+CB5..U+CB9', 'U+CBC..U+CC4', 'U+CC6..U+CC8', 'U+CCA..U+CCD',
        'U+CD5..U+CD6', 'U+CDE', 'U+CE0..U+CE3', 'U+CE6..U+CEF', 'U+CF1..U+CF2',
        # Dhivehi
        'U+780..U+7B1',
        # Gothic
        'U+10330..U+1034A',
        # Old Turkic (otk)
        'U+10C00..U+10C48',
        # Warang Citi, for Ho (hoc)
        'U+118A0..U+118BF->U+118C0..U+118DF',
        'U+118C0..U+118DF', 'U+118E0..U+118F2', 'U+118FF',
        # Mongolian (mon) and Manchu (mnc)
        'U+1810..U+1819', 'U+1820..U+1878', 'U+1880..U+18AA',
        # Phoenician alphabet
        'U+10900..U+1091B',
        # Tagalog (tgl)
        'U+1700..U+1714',
        # Cree syllabics
        'U+1401..U+166D', 'U+166F..U+167F', 'U+18B0..U+18F5',
        # Glagolitic
        'U+2c00..U+2c2e->U+2c30..U+2c5e', 'U+2c30..U+2c5e',
        # Coptic
        'U+2c80..U+2ce3/2', 'U+2ce4', 'U+2ceb..U+2cee/2', 'U+2cef..U+2cf1', 'U+2cf2..U+2cf3/2', 'U+2cfd',
        # Syloti Nagri
        'U+a800..U+a827',
        # Ol Chiki, for Santali (sat)
        'U+1c50..U+1c7d',
        # Hanifi Rohingya
        'U+10D00..U+10D27', 'U+10D30..U+10D39'
    );

    public $scriptsWithoutWordBoundaries = array(
        # CJK
        'U+3000..U+312F', 'U+3300..U+9FFF', 'U+F900..U+FAFF',
        'U+1B000..U+1B16F', 'U+20000..U+2EBEF', 'U+2F800..U+2FA1F',
        # Hangul
        'U+1100..U+11FF', 'U+3130..U+318F', 'U+A960..U+A97F', 'U+AC00..U+D7FF',
        # Hangul halfwidth to fullwidth
        'U+FFA1..U+FFBE->U+3131..U+314E', 'U+FFC2..U+FFC7->U+314F..U+3154',
        'U+FFCA..U+FFCF->U+3155..U+315A', 'U+FFD2..U+FFD7->U+315B..U+3160', 'U+FFDA..U+FFDC->U+3161..U+3163',
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
        'U+E81', 'U+E82', 'U+E84', 'U+E87', 'U+E88', 'U+E8A', 'U+E8D', 'U+E94..U+E97', 'U+E99..U+E9F',
        'U+EA1..U+EA3', 'U+EA5', 'U+EA7', 'U+EAA', 'U+EAB', 'U+EAD', 'U+EAE', 'U+EB0..U+EB9', 'U+EBB',
        'U+EBC', 'U+EBD', 'U+EC0..U+EC4', 'U+EC6', 'U+EC8..U+ECD', 'U+ED0..U+ED9', 'U+EDC..U+EDF',
        # Tibetan (bod) (not sure about marks and signs)
        'U+F00', 'U+F20..U+F33', 'U+F40..U+F47', 'U+F49..U+F6C', 'U+F71..U+F87', 'U+F90..U+F97',
        'U+F99..U+FBC', 'U+FD0..U+FD2',
        # Khmer (khm)
        'U+1780..U+17D2', 'U+17E0..U+17E9', 'U+17F0..U+17F9', 'U+19E0..U+19FF',
        # Thai (tha)
        'U+E01..U+E2E', 'U+E30..U+E3A', 'U+E40..U+E4E', 'U+E50..U+E59',
        # Yi syllables, used by Nuosu (iii)
        'U+A000..U+A48C',
        # Javanese (jav)
        'U+A980..U+A9C0', 'U+A9CF..U+A9D9',
        # Cuneiform, used by Sumerian (sux):
        'U+12000..U+12399', 'U+12400..U+1246E', 'U+12480..U+12543',
        # Egyptian Hieroglyphs (egy)
        'U+13000..U+1342E',
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
                    $v != 'U+100..U+137/2' &&
                    $v != 'U+14A..U+177/2' &&
                    $v != 'U+1DE..U+1EF/2' &&
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
                'I->U+131', 'U+131', # case-folding: dotless i
                'U+CE->U+131', 'U+EE->U+131', # strip circumflex from I,i and map to dotless i
                'U+130->i', 'i', # case-folding: dotted i
                'J..N->j..n', 'j..n',
                'O->o', 'o',
                'U+D6->U+F6', 'U+F6', # case-folding: o-umlaut
                'P..T->p..t', 'p..t',
                'U->u', 'U+DB->u', 'U+FB->u', 'u', # case-folding: u with/without circumflex
                'U+DC->U+FC', 'U+FC',   # case-folding: u-umlaut
                'V..Z->v..z', 'v..z',
                'U+100..U+129/2', 'U+132..U+137/2',
            ),
            array_filter(
                $this->charsetTable,
                function($v) {
                    return $v != 'A..Z->a..z' && $v != 'a..z'
                    && $v != 'U+C0..U+D6->U+E0..U+F6' && $v != 'U+E0..U+F6' # Latin-1 supplement
                    && $v != 'U+D8..U+DE->U+F8..U+FE' && $v != 'U+F8..U+FF' # Latin-1 supplement
                    && $v != 'U+100..U+137/2' # A-macron to k-cedilla
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

        /* Arabic vowel marks are optional, so it's easier to search if they are
         * ignored. The common ignore_chars setting accomplishes that, but it's
         * redundant with the Arabic stemmer. By adding them as regular
         * characters, it becomes possible to use them with the "exact match"
         * operator. We only ignore soft hyphen. */
        $this->indexExtraOptions['ara'] =
            "
        charset_table = ".implode(', ', array_merge(
            array('U+640', 'U+64b..U+65f', 'U+670', 'U+6dc'),
            $this->charsetTable
        ))."
        ignore_chars = U+AD\n";

        /* In Ottoman Turkish, a few characters have rarely used variants:
         * U+6ad as a variant of U+643 and U+647 as a variant of U+6d5.
         */
        $this->indexExtraOptions['ota'] =
            "
        charset_table = ".implode(', ', array_merge(
            array(
                'U+641..U+646', 'U+647->U+6d5', 'U+648',
                'U+6aa..U+6ac', 'U+6ad->U+643', 'U+6ae..U+6bf',
            ),
            array_filter(
                $this->charsetTable,
                function($v) {
                    return $v != 'U+641..U+648' and $v != 'U+6aa..U+6bf'
                ; }
            )
        ))."\n";

        /**
         * Allow case-insensitive search in German
         * by not casefolding Latin characters.
         */
        $noLatinCaseFoldingMap = [
            'A..Z->a..z' => 'A..Z',
            # Latin-1 Supplement (0080-00FF)
            'U+C0..U+D6->U+E0..U+F6' => 'U+C0..U+D6',
            'U+D8..U+DE->U+F8..U+FE' => 'U+D8..U+DE',
            # Latin extended-A (0100-017F)
            'U+100..U+137/2' => 'U+100..U+137',
            'U+139..U+148/2' => 'U+139..U+148',
            'U+14A..U+177/2' => 'U+14A..U+177',
            'U+179..U+17E/2' => 'U+179..U+17E',
        ];
        $this->indexExtraOptions['deu'] =
            "
        charset_table = ".implode(', ', array_map(
            function($value) use ($noLatinCaseFoldingMap) {
                return $noLatinCaseFoldingMap[$value] ?? $value;
            },
            $this->charsetTable
        ))."\n";

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
    // The characters U+640, U+64b..U+65f, U+670, U+6dc are Arabic diacritics,
    // which should be ignored. For a language with a stemmer that removes them
    // (e.g. Arabic) they can be re-enabled to allow searching for exact matches.
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
    blend_chars             = ?
    ignore_chars            = U+AD, U+5B0..U+5C5, U+5C7, U+640, U+64b..U+65f, U+670, U+6dc
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
        $transcriptableLangs = $this->loadModel('Transcriptions')->transcriptableLanguages();
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
                    "join reindex_flags rf on rf.sentence_id = sent_start.id and rf.indexed = 1 and rf.type = 'change'";
                $kill_list_query = ($type == 'main') ?
                    '' :
                    "sql_query_killlist = select sentence_id from reindex_flags \
                        where lang = '$lang' and indexed = 1 and type = 'removal'";
                $transcriptions_query = in_array($lang, $transcriptableLangs) ?
                    'select sentence_id, text from transcriptions order by sentence_id asc' :
                    'select 1, 1 from dual where 1 = 0'; # a no-op query with 2-column empty result
                $conf .= "
        sql_query_range = select min(id), max(id) from sentences
        sql_range_step = 100000
        sql_query = \
            select \
                r.id, r.text, r.created, r.modified, r.user_id, r.ucorrectness, r.has_audio, \
                r.origin_known, r.is_original, r.owner_is_native, \
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
                    (sent_start.based_on_id IS NOT NULL) as origin_known, \
                    (sent_start.based_on_id = 0) as is_original, \
                    COALESCE(ul_sent_start.level = 5, 0) as owner_is_native, \
                    \
                    CONCAT('{', \
                        'l:\"',sent_end.lang,'\",', \
                        'd:',MIN( IF(trans.sentence_id = transtrans.translation_id,1,2) ),',', \
                        'u:',COALESCE(sent_end.user_id, 0),',', \
                        'c:',sent_end.correctness + 1,',', \
                        'a:',COUNT(audios_sent_end.id) > 0,',', \
                        'n:',COALESCE(ul_sent_end.level = 5, 0), \
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
                left join \
                    users_languages ul_sent_start ON sent_start.user_id = ul_sent_start.of_user_id \
                                                 AND sent_start.lang = ul_sent_start.language_code \
                left join \
                    users_languages ul_sent_end ON sent_end.user_id = ul_sent_end.of_user_id \
                                               AND sent_end.lang = ul_sent_end.language_code \
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
        $kill_list_query

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
        sql_attr_bool = origin_known
        sql_attr_bool = is_original
        sql_attr_bool = owner_is_native
        sql_attr_json = trans

        sql_joined_field = \
            transcription from query; \
            $transcriptions_query
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
        killlist_target = ${lang}_main_index";
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

    public function conf($only = null) {
        $languages = LanguagesLib::languagesInTatoeba();
        if (is_null($only)) {
            if (!$this->params['all']) {
                $Sentences = $this->loadModel('Sentences');
                $only = array_filter($Sentences->languagesHavingSentences());
            }
        }
        if (!is_null($only)) {
            $languages = array_intersect_key($languages, array_flip($only));
        }
        $conf = '';
        $conf .= $this->conf_beginning();
        $conf .= $this->conf_language_indexes($languages);
        $conf .= $this->conf_ending();
        return $conf;
    }

    /**
     * Manticore has an internal buffer limit of 8192 bytes:
     * https://github.com/manticoresoftware/manticoresearch/blob/69c389347d44f136cd93b899640d7f4f4a6ce750/src/sphinxutils.cpp#L1201
     * To ensure that the configuration stays below the limit, we need to add
     * a backslash-escaped newline into overly long lines.
     */
    public function escape_long_lines($conf, $limit = 8192) {
        $limit -= 3; // we need enough room for the escape characters + \0
        $lines = explode("\n", $conf);
        $conf = "";
        foreach ($lines as $line) {
            for ($i = 0; $i < strlen($line); $i += $limit) {
                $conf .= substr($line, $i, $limit);
                if ($i + $limit < strlen($line)) {
                    $conf .= "\\\n";
                }
            }
            $conf .= "\n";
        }
        return $conf;
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser
            ->addOption('all', [
                'short' => 'a',
                'boolean' => true,
                'default' => false,
                'help' => 'Include all languages (default is to only include languages having sentences).',
            ])
            ->setDescription('Generates configuration file for Manticore Search.');
        return $parser;
    }

    public function main() {
        $this->dbConfig = ConnectionManager::get('default')->config();
        $this->sphinxConfig = Configure::read('Sphinx');
        
        if (count($this->args)) {
            $langs = $this->args;
        } else {
            $langs = null;
        }

        echo $this->escape_long_lines($this->conf($langs));
    }
}
