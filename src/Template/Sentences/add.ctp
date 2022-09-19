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
use App\Model\CurrentUser;

if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
    $this->set('isResponsive', true);
}

$this->set('title_for_layout', $this->Pages->formatTitle(__('Add sentences')));

$filteredLanguage = $this->request->getSession()->read('vocabulary_requests_filtered_lang');

$vocabularyUrl = $this->Url->build(array(
    'controller' => 'vocabulary',
    'action' => 'add_sentences',
    $filteredLanguage
));
?>

<!--start layout -->

<!-- start title bar -->
<md-toolbar class="md-hue-2" ng-cloak ng-controller="SidenavController">
    <div class="md-toolbar-tools">
        <h2 flex=""><?php echo __('Add new sentences'); ?></h2>

        <md-button ng-click="toggle('sidenav')" hide-gt-sm>
            <md-icon>info</md-icon>
            <?= __('Important') ?>
        </md-button>
    </div>
</md-toolbar>
<!-- end title bar -->

<!-- start content -->
<section layout="row" ng-cloak>
    <md-content id="main_content" class="md-whiteframe-1dp" flex>
        <!-- start add sentence form -->
        <?php
        if (CurrentUser::getSetting('use_new_design')) {
            echo $this->element('sentences/add_sentences_angular');
        } else {
            echo $this->element('sentences/add_sentences_jquery');
        }
        ?>
        <!-- end add sentence section -->

        <!-- start inspiration suggestions -->
        <section >
            <md-toolbar class="md-hue-2">
                <div class="md-toolbar-tools">
                    <h2><?php echo __('Not inspired?'); ?></h2>
                </div>
            </md-toolbar>

            <div layout="column" layout-gt-sm="row" ng-cloak>
                <div layout="column" layout-align="center center" class="section" flex="50" style="margin-bottom:0">
                    <?= __('Check the vocabulary requests for which there are very few or no sentences yet.'); ?>
                    <md-button class="md-primary" href="<?= $vocabularyUrl ?>">
                        <?= __('Sentences wanted') ?>
                        <md-icon>keyboard_arrow_right</md-icon>
                    </md-button>
                </div>
                <md-divider vertical layout-gt-sm></md-divider>
                <div layout="column" layout-align="center center" class="section" flex="50" style="margin-bottom:0">
                    <?= __('Tatominer provides a list of the most searched words for which there are very few or no sentences yet.'); ?>
                    <md-button class="md-primary" href="https://tatominer.netlify.app/" target="_blank">
                        <?= __('Go to Tatominer') ?>
                        <md-icon>keyboard_arrow_right</md-icon>
                    </md-button>
                    <div class="hint" layout="row" layout-align="center center">
                        <md-icon>info</md-icon>
                        <div><?= __('Not all languages are supported yet.'); ?></div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end inspiration suggestions -->
    </md-content>

    <md-sidenav class="md-sidenav-right md-whiteframe-1dp"
            fullscreen
            md-component-id="sidenav"
            md-disable-scroll-target="body"
            md-is-locked-open="$mdMedia('gt-sm')">
        <md-toolbar>
            <div class="md-toolbar-tools" ng-controller="SidenavController">
                <h2 flex class="flex"><?php echo __('Important'); ?></h2>
                <md-button class="close md-icon-button" ng-click="toggle('sidenav')" hide-gt-sm>
                    <md-icon>close</md-icon>
                </md-button>
            </div>
        </md-toolbar>
        <div class="section">
            <?php
            echo __(
                "<strong>We like quality.</strong> Every detail matters. ".
                "Please do not forget punctuation and capital letters."
            );
            ?>
            </p>

            <p>
            <?php
            echo __(
                "<strong>We like diversity.</strong> Unleash your creativity! ".
                "Avoid using the same words, names, topics, or patterns over and over again."
            );
            ?>
            </p>

            <p>
            <?php
            echo __(
                "<strong>We like sharing our data.</strong> Avoid copy-pasting sentences, ".
                "or at least make sure they are not copyrighted and are compatible with the CC BY license. ".
                "Otherwise we cannot use them."
            );
            ?>
            </p>
        </div>
    </md-sidenav>
</section>


