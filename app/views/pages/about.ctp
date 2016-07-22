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
 * @link     http://tatoeba.org
 */

$lang = LanguagesLib::languageTag(Configure::read('Config.language'));
?>

<div id="main_content">
    <div class="section md-whiteframe-1dp">
        <h2><?php __('What is Tatoeba?'); ?></h2>

        <p>
            <?php
            __(
                'Tatoeba is a platform that aims to build a large database '.
                'of sentences translated into as many languages as possible. '.
                'The initial idea was to have a tool in which you could search '.
                'certain words, and it would return sentences containing '.
                'these words with their translations in the desired languages. '.
                'The name Tatoeba resulted from this concept, because '.
                '<em>tatoeba</em> means <em>for example</em> in Japanese.');
            ?>
        </p>

        <div class="amara-embed" data-height="450px" data-width="620px"
             data-resizable="true" data-initial-language="<?= $lang ?>"
             data-show-subtitles-default="true"
             data-url="https://www.youtube.com/watch?v=ac9SmJuwHqk">
        </div>

        <script type="text/javascript" src='https://amara.org/embedder-iframe'>
        </script>
    </div>
</div>