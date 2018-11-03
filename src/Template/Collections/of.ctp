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
    'ok' => __('Sentences marked as "OK"'),
    'unsure' => __('Sentences marked as "unsure"'),
    'not-ok' => __('Sentences marked as "not OK"'),
    'all' => __("All sentences"),
    'outdated' => __("Outdated ratings")
);

if ($correctnessLabel) {
    $category = $correctnessLabel;
} else {
    $category = 'all';
}


if ($userExists) {
    $title = format(
        __("{user}'s collection - {category}"),
        array('user' => $username, 'category' => $categories[$category])
    );
} else {
    $title = format(__("There's no user called {username}"), array('username' => $username));
}

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    if (!CurrentUser::get('settings.users_collections_ratings')) {
        echo '<div class="module">';
        echo $this->Html->tag('p', __('This feature is currently deactivated.'));
        echo $this->Html->tag('p',
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
        echo $this->Html->tag('h2', __('Filter'));

        $menu = array();
        foreach($categories as $categoryKey => $categoryValue) {
            $menu[] = $this->Html->link(
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
            echo $this->Html->tag('li', $item, array('class' => 'item'));
        }
        echo '</ul>';
        ?>
    </div>
</div>

<div id="main_content">
    <div class="module correctness-info">

        <?php
        if (!$userExists) {
            $this->CommonModules->displayNoSuchUser($username);
        } else {
            $title = $this->Paginator->counter(array(
                'format' => $title . ' ' . __("(total %count%)")
            ));
            echo $this->Html->tag('h2', $title);
        ?>
        <div class="sortBy">
            <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            echo $this->Paginator->sort('modified', __("date modified"));
            echo " | ";
            echo $this->Paginator->sort('created', __("date created"));
            echo " | ";
            echo $this->Paginator->sort('sentence_id', __("sentence id"));
            ?>
        </div>
        <?php
            $paginationUrl = array($username, $correctnessLabel, $lang);
            $this->Pagination->display($paginationUrl);

            $type = 'mainSentence';
            $parentId = null;
            $withAudio = false;
            foreach ($corpus as $sentence) {
                echo '<div>';

                if (empty($sentence['Sentence']['id'])) {
                    $sentenceId = $sentence['UsersSentences']['sentence_id'];
                    $linkToSentence = $this->Html->link(
                        '#'.$sentenceId,
                        array(
                            'controller' => 'sentences',
                            'action' => 'show',
                            $sentenceId
                        )
                    );

                    echo $this->Html->div('sentence deleted',
                        format(
                            __('Sentence {id} has been deleted.'),
                            array('id' => $linkToSentence)
                        )
                    );
                } else {
                    $this->Sentences->displayGenericSentence(
                        $sentence['Sentence'],
                        $type,
                        $parentId,
                        $withAudio
                    );
                }

                $correctness = $sentence['UsersSentences']['correctness'];
                echo $this->Html->div(
                    'correctness',
                    $this->Images->correctnessIcon($correctness),
                    array('title' => $sentence['UsersSentences']['modified'])
                );

                echo '</div>';
            }

            $this->Pagination->display($paginationUrl);
        }
        ?>
    </div>
</div>
