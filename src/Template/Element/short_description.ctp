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

    <div class="container">
        <!-- Search -->
        <div class="search-bar" ng-controller="SearchBarController as ctrl">
            <?php
            echo $this->Form->create('Sentence', [
                'url' => ['controller' => 'sentences', 'action' => 'search'],
                'type' => 'get',
                'id' => 'new-search-bar'
            ]);

            if (!isset($selectedLanguageFrom)) {
                $selectedLanguageFrom = 'und';
            }

            if (!isset($selectedLanguageTo)) {
                $selectedLanguageTo = 'und';
            }

            ?>
            <fieldset class="input text languages" style="line-height: 40px">
                <?php
                $langFrom = $this->element(
                    'language_dropdown', 
                    array(
                        'name' => 'from',
                        'selectedLanguage' => $selectedLanguageFrom,
                        'languages' => $this->Search->getLangs()
                    )
                );

                $langTo = $this->element(
                    'language_dropdown', 
                    array(
                        'name' => 'to',
                        'selectedLanguage' => $selectedLanguageTo,
                        'languages' => $this->Search->getLangs()
                    )
                );
                echo format(
                    __('Search sentences in {langFrom} '.
                        'translated into {langTo} containing:', true),
                    array('langFrom' => $langFrom, 'langTo' => $langTo)
                );
                ?>
            </fieldset>

            <fieldset class="input text search-input">
                <?php
                $clearButton = $this->Html->tag('button', '✖', array(
                    'id' => 'clearSearch',
                    'type' => 'button',
                    'title' => __('Clear search'),
                    'ng-click' => 'ctrl.clearSearch()'
                ));
                echo $this->Form->input(
                    'query',
                    array(
                        'id' => 'SentenceQuery',
                        'label' => '',
                        'accesskey' => 4,
                        'lang' => '',
                        'dir' => 'auto',
                        'after' => $clearButton,
                        'ng-model' => 'ctrl.searchQuery',
                        'placeholder' => __('Enter a word or a phrase')
                    )
                );
                ?>
            </fieldset>

            <fieldset class="submit">
                <md-button type="submit" class="search-submit-button md-raised md-primary">
                    <md-icon ng-cloak>search</md-icon>
                </md-button>
            </fieldset>


            <div class="extra-links">
                <div class="advanced-search">
                    <?php
                    echo $this->Html->link(
                        __x('title', 'Advanced search'),
                        array(
                            'controller' => 'sentences',
                            'action' => 'advanced_search'
                        )
                    );
                    ?>
                </div>

                <div class="tip">
                    <?php
                    echo __(
                        "Tip: <em>=word</em> will search for ".
                        "an exact match on <em>word</em>"
                    );
                    echo "<br/>";
                    echo $this->Html->link(
                        /* @translators: links to a page with tips to perform
                           searches, like search operators */
                        __('More tips'),
                        'http://en.wiki.tatoeba.org/articles/show/text-search',
                        array(
                            'target' => '_blank'
                        )
                    );
                    ?>
                </div>
            </div>

            <?php
            echo $this->Form->end();
            ?>
        </div>


    </div>
</div>
