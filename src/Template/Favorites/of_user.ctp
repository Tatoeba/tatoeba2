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

$username = h($username);

if ($userExists) {
    $numberOfSentences = $this->Paginator->param('count');

    if (strlen($filter) > 0) {
        $title = format(__("{user}'s favorite sentences matching “{filter}”"), array('user' => $username, 'filter' => $filter));
    } else {
        $title = format(__("{user}'s favorite sentences"), array('user' => $username));
    }
} else {
    $title = format(__("There's no user called {username}"), array('username' => $username));
}

$this->set('title_for_layout', $this->Pages->formatTitle($title));

$this->Html->script('/js/favorites.add.js', ['block' => 'scriptBottom']);

// Sidebar menu
if ($userExists) {
    echo $this->Html->div(
        null,
        $this->element('users_menu', array('username' => $username)),
        ['id' => "annexe_content"]
    );
} ?>

<div id="main_content">
    <section class="md-whiteframe-1dp" id="favorites-list" data-success="<?php echo __("Favorite successfully removed."); ?>" >

    <?php
    if (!$userExists) {
        $this->CommonModules->displayNoSuchUser($username);
    } else {
        $title = $this->Paginator->counter($title . ' ' . __('(total {{count}})'));

        ?>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= $title ?></h2>
            </div>
        </md-toolbar>

        <md-content layout-padding>
        <?php
        if ($numberOfSentences > 0 || !empty($filter)) {

            echo $this->Form->create(null, ['type' => 'get']);
            ?>
            <div layout="row" layout-align="center start">
                <md-input-container flex>
                    <?php
                    echo $this->Form->control('filter', [
                        'label' => __('Sentence text:'),
                        'lang' => '',
                        'dir' => 'auto',
                        'value' => $this->safeForAngular($filter),
                    ]);
                    ?>
                </md-input-container>
                <md-button type="submit" class="search-submit-button md-raised">
                    <md-icon>search</md-icon>
                    <?php /* @translators: search button in favorites page (verb) */ ?>
                    <?= __x('button', 'Search') ?>
                </md-button>
            </div>
            <?php
            echo $this->Form->end();

            $this->Pagination->display();

            $type = 'mainSentence';
            $parentId = null;
            $withAudio = false;
            $ownerName = null;
            foreach ($favorites as $favorite) {
                if (empty($favorite->sentence->text)) {
                    $sentenceId = $favorite->favorite_id;
                    $linkToSentence = $this->Html->link(
                        $this->Pages->formatSentenceIdWithSharp($sentenceId),
                        array(
                            'controller' => 'sentences',
                            'action' => 'show',
                            $sentenceId
                        )
                    );
                    ?>

                    <div layout="row" layout-align="center" class="sentence deleted">
                        <div class="content column remove" flex>
                            <div class="sentenceContent" data-sentence-id="<?= $sentenceId ?>">
                                <?= format(
                                    __('Sentence {id} has been deleted.'),
                                    array('id' => $linkToSentence)
                                ); ?>
                            </div>
                        </div>
                        <div class="favorite-page column" layout="row" layout-align="bottom">
                        <?php $this->Menu->favoriteButton($sentenceId, true, true, true); ?>
                        </div>
                    </div>
                <?php
                } else {
                    $this->Sentences->displayGenericSentence(
                        $favorite->sentence,
                        $type,
                        $withAudio,
                        $parentId
                    );
                }
            }
            $this->Pagination->display();

            if ($numberOfSentences == 0) {
                echo format(__('This user does not have any favorites matching “{filter}”.'), compact('filter'));
            }
            ?>
            </md-content>
            <?php
        } else {
            echo __('This user does not have any favorites.');
        }
    }
    ?>
    </section>
</div>
