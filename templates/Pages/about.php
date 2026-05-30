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
use Cake\I18n\I18n;

$lang = I18n::getLocale();
$this->set('title_for_layout', $this->Pages->formatTitle(__('What is Tatoeba?')));
?>

<div id="main_content">
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('What is Tatoeba?'); ?></h2>

        <p>
            <?php
            echo __(
                'Tatoeba is a large database of sentences and translations. '.
                'Its content is ever-growing and results from the voluntary '.
                'contributions of thousands of members.'
            );
            ?>
        </p>
        <p>
            <?php
            echo __(
                'Tatoeba provides a tool for you to see examples of how words are '.
                'used in the context of a sentence. You specify words that '.
                'interest you, and it returns sentences containing these words '.
                'with their translations in the desired languages. '.
                'The name Tatoeba (<em>for example</em> in Japanese) captures '.
                'this concept.'
            );
            ?>
        </p>

        <p>
            <?php
            echo __(
                'The project was founded by Trang Ho in 2006, hosted on '.
                'Sourceforge under the codename of <em>multilangdict</em>.'
            );
            ?>
        </p>

        <p>
            <?php
            echo __(
                'The video below, made in 2010, presents the core ideas of Tatoeba.'
            );
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
