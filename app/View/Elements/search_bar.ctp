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

if (isset($this->request->params['lang'])) {
    Configure::write('Config.language', $this->request->params['lang']);
}
?>

<md-toolbar class="search_bar md-whiteframe-1dp md-primary">
<?php
if ($selectedLanguageFrom == null) {
    $selectedLanguageFrom = 'und';
}

if ($selectedLanguageTo == null) {
    $selectedLanguageTo = 'und';
}
echo $this->Form->create(
    'Sentence',
    array(
        "url" => array("action" => "search"),
        "type" => "get"
    )
);
?>
<div layout-gt-sm="row" layout-align-gt-sm="center end" layout-margin
     layout="column" layout-align="center center">
    <div layout="column" flex>
        <div layout="row" layout-align="end center" class="search-bar-extra">
            <?php
            echo $this->Html->link(
                __('Help'),
                'http://en.wiki.tatoeba.org/articles/show/text-search',
                array(
                    'target' => '_blank'
                )
            );
            echo $this->Html->link(
                __('Advanced search'),
                array(
                    'controller' => 'sentences',
                    'action' => 'advanced_search'
                )
            );
            ?>
        </div>

        <div layout="row">
            <label for="SentenceQuery">
                <?php echo __('Search'); ?>
            </label>
            <input id="SentenceQuery"
                   type="text"
                   name="query"
                   value="<?= Sanitize::html($searchQuery) ?>"
                   accesskey="4"
                   lang=""
                   dir="auto"
                   flex>
            <md-icon id="clearSearch">clear</md-icon>
        </div>
    </div>

    <div layout="row" layout-align="center end">
        <div layout="column">
            <?php
            echo $this->Search->selectLang(
                'from',
                $selectedLanguageFrom,
                array(
                    'div' => false,
                    'label' => __('From'),
                )
            );
            ?>
        </div>

        <div id="arrow">
            <md-icon>swap_horiz</md-icon>
        </div>

        <div layout="column">
            <?php
            echo $this->Search->selectLang(
                'to',
                $selectedLanguageTo,
                array(
                    'div' => false,
                    'label' => __x('language', 'To'),
                )
            );
            ?>
        </div>
    </div>

    <md-button type="submit" class="search-submit-button md-raised">
        <md-icon>search</md-icon>
    </md-button>
</div>

<?php
echo $this->Form->end();
?>
</md-toolbar>
