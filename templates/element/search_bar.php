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

use Cake\Core\Configure;

$searchQuery = h(str_replace('{{', '\{\{', json_encode($searchQuery)));
?>

<md-toolbar id="search-bar-minimal" ng-cloak>
    <div class="md-toolbar-tools">
    <?php
    echo $this->Form->create(null, [
        'layout' => 'column',
        'url' => ['controller' => 'sentences', 'action' => 'search'],
        'type' => 'get',
        'flex' => '',
        'ng-cloak' => ''
    ]);
    ?>

    <div layout="row" layout-align="center center" flex>
        <md-input-container class="md-accent" flex md-no-float>
            <input name="query" 
                accesskey="4" 
                type="search"
                dir="auto" 
                ng-model="ctrl.searchQuery" 
                ng-init="ctrl.searchQuery = <?= $searchQuery ?>"
                <?php /* @translators: placeholder for the search input in the search bar */ ?>
                placeholder="<?= __x('placeholder', 'Search') ?>"/>
        </md-input-container>
        <md-button type="submit" class="md-icon-button md-raised"><md-icon>search</md-icon></md-button>
    </div>
    <?php
    echo $this->Form->end();
    ?>
    </div>
</md-toolbar>

<md-toolbar id="search-bar" ng-controller="SearchBarController as ctrl" class="md-whiteframe-1dp md-primary">
<?php
echo $this->Form->create(null, [
    'id' => 'SentenceSearchForm',
    'name' => 'ctrl.form',
    "url" => false,
    'ng-submit' => 'ctrl.submit(ctrl.form)',
    "type" => "get"
]);
?>
<div ng-cloak
     layout-gt-sm="row" layout-align-gt-sm="center end" layout-margin
     layout="column" layout-align="center center">
    <div layout="column" flex>
        <div layout="row" layout-align="end center" class="search-bar-extra">
            <?php
            echo $this->Html->link(
                __('Help'),
                $this->Pages->getWikiLink('text-search'),
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
                <?php /* @translators: keywords field label in top search bar (not displayed) */ ?>
                <?php echo __x('label', 'Search'); ?>
            </label>
            <input id="SentenceQuery"
                   type="search"
                   name="query"
                   ng-model="ctrl.searchQuery"
                   ng-init="ctrl.searchQuery = <?= $searchQuery ?>"
                   accesskey="4"
                   lang=""
                   dir="auto"
                   flex>
            <md-icon id="clearSearch" tabindex="-1" ng-click="ctrl.clearSearch()">clear</md-icon>
        </div>
    </div>

    <div layout-gt-xs="row" layout-align-gt-xs="center end"
         layout="column" layout-align="center center">
        <div layout="column">
            <?php /* @translators: search language field label in top search bar */ ?>
            <label for="SentenceFrom"><?= __('From') ?></label>
            <?php
            echo $this->element(
                'language_dropdown', 
                array(
                    'id' => 'SentenceFrom',
                    'name' => 'from',
                    'initialSelection' => $selectedLanguageFrom,
                    'languages' => $this->Languages->getSearchableLanguagesArray(),
                    /* @translators: placeholder used in translation language selection dropdown in top search bar */
                    'placeholder' => __x('searchbar', 'Any language'),
                    'selectedLanguage' => 'ctrl.langFrom',
                )
            );
            ?>
        </div>

        <div id="arrow" tabindex="-1" ng-click="ctrl.swapLanguages()">
            <md-icon>swap_horiz</md-icon>
        </div>

        <div layout="column">
            <label for="SentenceTo">
                <?php /* @translators: translation language field label in top search bar */ ?>
                <?= __x('language', 'To') ?>
            </label>
            <?php
            echo $this->element(
                'language_dropdown', 
                array(
                    'id' => 'SentenceTo',
                    'name' => 'to',
                    'initialSelection' => $selectedLanguageTo,
                    'languages' => $this->Languages->getSearchableLanguagesArray(),
                    /* @translators: placeholder used in translation language selection dropdown in top search bar */
                    'placeholder' => __x('searchbar', 'Any language'),
                    'selectedLanguage' => 'ctrl.langTo',
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
