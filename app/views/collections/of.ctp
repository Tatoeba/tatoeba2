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

$categories = array(
    'ok' => __('Sentences marked as "OK"', true),
    'unsure' => __('Sentences marked as "unsure"', true),
    'not-ok' => __('Sentences marked as "not OK"', true),
    'all' => __("All sentences", true),
    'outdated' => __("Outdated ratings", true)
);

if (!is_int($correctness)) {
    $category = 'all';
} else {
    switch($correctness) {
        case -1:
            $category = 'not-ok';
            break;
        case 0:
            $category = 'unsure';
            break;
        default:
            $category = 'ok';
            break;
    }
}

if ($userExists) {    
    $title = format(
        __("{user}'s collection - {category}", true),
        array('user' => $username, 'category' => $categories[$category])
    );
} else {
    $title = format(__("There's no user called {username}", true), array('username' => $username));
}

$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    if (!CurrentUser::get('settings.users_collections_ratings')) {
        echo '<div class="module">';
        echo $html->tag('p', __('This feature is currently deactivated.', true));
        echo $html->tag('p',
            __(
                'You can activate it in your settings: "Activate the feature '.
                'to rate sentences and build your collection..."',
                true
            )
        );
        echo '</div>';
    }
    ?>
    <div class="module">
        <?php
        echo $html->tag('h2', __('Filter', true));

        $menu = array();
        foreach($categories as $categoryKey => $categoryValue) {
            $menu[] = $html->link(
                $categoryValue,
                array(
                    'action' => 'of',
                    $username,
                    $categoryKey
                )
            );
        }

        echo '<ul class="annexeMenu">';
        foreach($menu as $item) {
            echo $html->tag('li', $item, array('class' => 'item'));
        }
        echo '</ul>';
        ?>
    </div>
</div>

<div id="main_content">
    <div class="module correctness-info">

        <?php
        if (!$userExists) {
            $commonModules->displayNoSuchUser($username, $backLink);
        } else {
            $title = $paginator->counter(array(
                'format' => $title . ' ' . __("(total %count%)", true)
            ));
            echo $html->tag('h2', $title);
        ?>
        <div class="sortBy">
            <strong><?php __("Sort by:") ?> </strong>
            <?php 
            echo $this->Paginator->sort(__("date modified",true), 'modified');
            echo " | ";
            echo $this->Paginator->sort(__("date created",true), 'created');
            echo " | ";
            echo $this->Paginator->sort(__("sentence id",true), 'sentence_id');
            ?>
        </div>
        <?php
            $paginationUrl = array($username, $correctnessLabel, $lang);
            $pagination->display($paginationUrl);

            $type = 'mainSentence';
            $parentId = null;
            $withAudio = false;
            foreach ($corpus as $sentence) {
                echo '<div>';

                if (empty($sentence['Sentence']['id'])) {
                    $sentenceId = $sentence['UsersSentences']['sentence_id'];
                    $linkToSentence = $html->link(
                        '#'.$sentenceId,
                        array(
                            'controller' => 'sentences',
                            'action' => 'show',
                            $sentenceId
                        )
                    );

                    echo $html->div('sentence deleted',
                        format(
                            __('Sentence {id} has been deleted.', true),
                            array('id' => $linkToSentence)
                        )
                    );
                } else {
                    $sentences->displayGenericSentence(
                        $sentence['Sentence'],
                        null,
                        $type,
                        $parentId,
                        $withAudio
                    );
                }

                $correctness = $sentence['UsersSentences']['correctness'];
                echo $html->div(
                    'correctness',
                    $images->correctnessIcon($correctness),
                    array('title' => $sentence['UsersSentences']['modified'])
                );

                echo '</div>';
            }

            $pagination->display($paginationUrl);
        }
        ?>
    </div>
</div>
