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
?>

<div class="topContent">
    <md-toolbar class="md-whiteframe-1dp md-primary">
        <div class="container">
            <div class="description">
                <strong>
                    <?php echo __("Tatoeba is a collection of sentences and translations.");
                    ?>
                </strong>
                <div>
                    <?php echo __("It's collaborative, open, free and even addictive.");
                    ?>
                </div>
            </div>
        </div>
    </md-toolbar>

    <div class="container" ng-cloak>
        <!-- Search -->
        <div class="search-bar" ng-controller="SearchBarController as ctrl">
            <?php
            echo $this->Form->create(null, [
                'url' => ['controller' => 'sentences', 'action' => 'search'],
                'type' => 'get',
                'id' => 'new-search-bar'
            ]);
            ?>
           
            <md-input-container class="md-icon-float md-button-right md-block md-title">
                <label><?= __('Search') ?></label>
                <md-icon>search</md-icon>
                <input id="query" name="query" accesskey="4" dir="auto" ng-model="ctrl.searchQuery" />                
                <md-button class="md-icon-button" reset-button target="query">
                    <md-icon>clear</md-icon>
                </md-button>
                <div class="hint">
                    <?= __(
                        "Tip: <em>=word</em> will search for ".
                        "an exact match on <em>word</em>"
                    ); ?>
                </div>
            </md-input-container>

            <div layout="row" layout-xs="column" layout-align="end" layout-align-xs="center">
                <div layout="row" layout-align="center center" flex-order="1" flex-order-xs="-1">
                    <md-button type="submit" class="md-raised md-primary">
                        <?= __('Search') ?>
                    </md-button>
                </div>

                <div layout="row" layout-align="center center" layout-wrap>
                    <md-button href="<?= h($this->Pages->getWikiLink('text-search')) ?>" target="_blank">
                        <?php
                        /* @translators: links to a page with tips to perform
                            searches, like search operators */
                        echo __('More tips');
                        ?>
                    </md-button>
                    <md-button href="<?= $this->Url->build(['controller' => 'sentences', 'action' => 'advanced_search']) ?>">
                        <?= __x('title', 'Advanced search') ?>
                    </md-button>
                </div>
            </div>

            <?php
            echo $this->Form->end();
            ?>
        </div>


    </div>
</div>
