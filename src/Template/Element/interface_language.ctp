<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

use App\Lib\LanguagesLib;
use Cake\Core\Configure;

$lang = Configure::read('Config.language');
$configUiLanguages = Configure::read('UI.languages');
$languages = array();

foreach ($configUiLanguages as $langs) {
    list($isoCode, $suffix, $name) = $langs;
    $fullIsoCode = LanguagesLib::languageTag($isoCode, $suffix);
    $languages[] = array(
        'text' => $name,
        'value' => $isoCode,
        'lang' => $fullIsoCode,
        'dir' => LanguagesLib::getLanguageDirection($isoCode),
    );
}

usort(
    $languages,
    function($a, $b) {
        return strnatcmp($a['text'], $b['text']);
    }
);

echo $this->Form->select(
    'languageSelection',
    $languages,
    array(
        "onchange" => "changeInterfaceLang(this.value)",
        "empty" => false,
        "value" => $lang
    )
);
?>
