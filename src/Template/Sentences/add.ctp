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

$this->set('title_for_layout', $this->Pages->formatTitle(__('Add sentences')));

$filteredLanguage = $this->request->getSession()->read('vocabulary_requests_filtered_lang');

$vocabularyUrl = $this->Url->build(array(
    'controller' => 'vocabulary',
    'action' => 'add_sentences',
    $filteredLanguage
));
?>

<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
    <?php /* @translators: header text in side bar of the "Add sentences" page */ ?>
    <h2><?php echo __('Important'); ?></h2>
    <p>
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
</div>

<div id="main_content">

    <?php
    if (CurrentUser::getSetting('use_new_design')) {
        echo $this->element('sentences/add_sentences_angular');
    } else {
        echo $this->element('sentences/add_sentences_jquery');
    }
    ?>

    <section>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?php echo __('Not inspired?'); ?></h2>
            </div>
        </md-toolbar>

        <div layout="row" ng-cloak>
            <div layout="column" layout-align="center center" class="section md-whiteframe-1dp" flex="50">
                <?= __('Check the vocabulary requests for which there are very few or no sentences yet.'); ?>
                <md-button class="md-primary" href="<?= $vocabularyUrl ?>">
                    <?= __('Sentences wanted') ?>
                    <md-icon>keyboard_arrow_right</md-icon>
                </md-button>
            </div>

            <div layout="column" layout-align="center center" class="section md-whiteframe-1dp" flex="50">
                <?= __('Tatominer provides a list of the most searched words for which there are very few or no sentences yet.'); ?>
                <md-button class="md-primary" href="https://tatominer.netlify.app/" target="_blank">
                    <?= __('Go to Tatominer') ?>
                    <md-icon>keyboard_arrow_right</md-icon>
                </md-button>
            </div>
        </div>
    </section>
</div>
