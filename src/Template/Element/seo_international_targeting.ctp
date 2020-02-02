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

$uiLanguages = Configure::read('UI.languages');
$currentLang = $this->request->getParam('lang');

if ($currentLang) {
    $pathWithoutLang = substr($this->request->getPath(), 4);
    $host = $this->request->host();
    $scheme = $this->request->scheme();
    foreach ($uiLanguages as $lang) {
        $alternateURL = $scheme . '://' . $host . '/' . $lang[0] . $pathWithoutLang;
        $hreflang = LanguagesLib::languageTag($lang[0], $lang[1]);
        ?>
        <link rel="alternate"
              hreflang="<?php echo $hreflang; ?>"
              href="<?php echo $alternateURL; ?>">
        <?php
    }
}
?>
