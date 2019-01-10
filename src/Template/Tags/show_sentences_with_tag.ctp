<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
use App\Model\CurrentUser;

if ($tagExists) {
    $tagName = h($tagName);
    $title = format(__('Sentences with tag {tagName}'), compact('tagName'));
    ?>

    <div id="annexe_content">
        <?php $this->CommonModules->createFilterByLangMod(2); ?>
        <div class="module">
            <?php
            echo $this->Html->link(
                __('View all tags'),
                array(
                    "controller" => "tags",
                    "action" => "view_all",
                )
            );
            ?>
        </div>
    </div>

    <div id="main_content">
        <div class="section">
            <h2><?php
            $n = $this->Paginator->counter(array('format' => '{{count}}'));
            echo format(
                __n('{tagName} ({n} sentence)', '{tagName} ({n} sentences)', $n),
                compact('tagName', 'n')
            ); ?></h2>

            <div class="sortBy">
                <strong><?php echo __("Sort by:") ?></strong>
                <?php
                echo $this->Paginator->sort('added_time', __("date of tag"));
                ?>
            </div>

            <?php
            $this->Pagination->display();
            ?>

            <div class="sentencesList" id="sentencesList">
                <?php
                $useNewDesign = !CurrentUser::isMember()
                    || CurrentUser::getSetting('use_new_design');
                if ($useNewDesign) {
                    foreach ($allSentences as $item) {
                        $sentence = $item->sentence;
                        echo $this->element(
                            'sentences/sentence_and_translations',
                            array(
                                'sentence' => $sentence,
                                'translations' => $sentence->translations,
                                'user' => $sentence->user
                            )
                        );
                    }
                } else {
                    foreach ($allSentences as $i=>$sentence) {
                        // this should be done in the controller but this way
                        // we avoid another full loop on the sentence Array
                        $canUserRemove = CurrentUser::canRemoveTagFromSentence(
                            $taggerIds[$i]
                        );
                        $this->Tags->displaySentence(
                            $sentence->sentence,
                            $canUserRemove,
                            $tagId
                        );
                    }
                }
                ?>
            </div>

            <?php
            $this->Pagination->display();
            ?>

        </div>
    </div>
    <?php
} else {
    $title = format(__('No tag with id {tagId}'), compact('tagId'));
    ?>
    <div id="main_content">
        <?php
        echo $this->Html->link(
            __('View all tags'),
            array(
                "controller" => "tags",
                "action" => "view_all",
            )
        );
        ?>
    </div>
    <?php
}
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>
