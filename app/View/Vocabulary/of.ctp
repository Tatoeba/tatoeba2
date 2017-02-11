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
<?php
$this->Html->script('/js/vocabulary/of.ctrl.js', false);

$count = $this->Paginator->counter('%count%');
$title = format(
    __("{username}'s vocabulary items ({number})", $count),
    array('username' => $username, 'number' => $count)
);

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php echo $this->element('vocabulary/menu'); ?>

    <?php $this->CommonModules->createFilterByLangMod(2); ?>
</div>

<div id="main_content" ng-controller="VocabularyOfController as ctrl">
    <div class="section" md-whiteframe="1">
        <h2><?= $title ?></h2>

        <?php
        $paginationUrl = array($username);
        $this->Pagination->display($paginationUrl);
        ?>

        <md-list flex>
            <?php
            foreach($vocabulary as $item) {
                $divId = $item['Vocabulary']['id'];
                $lang = $item['Vocabulary']['lang'];
                $text = $item['Vocabulary']['text'];
                $numSentences = $item['Vocabulary']['numSentences'];
                $numSentencesLabel = $numSentences == 1000 ? '1000+' : $numSentences;
                $url = $this->Html->url(array(
                    'controller' => 'sentences',
                    'action' => 'search',
                    '?' => array(
                        'query' => '="' . $text . '"',
                        'from' => $lang
                    )
                ));
                ?>
                <md-list-item id="vocabulary_<?= $divId ?>">
                    <img class="vocabulary-lang" src="/img/flags/<?= $lang ?>.png"/>
                    <div class="vocabulary-text" flex><?= $text ?></div>
                    <md-button class="md-primary" href="<?= $url ?>">
                        <?= format(
                            __n(
                                '{number} sentence', '{number} sentences',
                                $numSentences,
                                true
                            ),
                            array('number' => $numSentencesLabel)
                        ); ?>
                    </md-button>
                    <? if ($canEdit) { ?>
                        <md-button ng-click="ctrl.remove('<?= $divId ?>')"
                                   class="md-icon-button">
                            <md-icon aria-label="Remove">delete</md-icon>
                        </md-button>
                    <? } ?>
                </md-list-item>
                <?php
            }
            ?>
        </md-list>

        <?php
        $paginationUrl = array($username);
        $this->Pagination->display($paginationUrl);
        ?>
    </div>

</div>
