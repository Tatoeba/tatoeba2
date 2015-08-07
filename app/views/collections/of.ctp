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

if (!is_int($correctness)) {
    $title = __("All the sentences in {user}'s corpus", true);
} else {
    switch($correctness) {
        case -1:
            $title = __('Sentences marked as incorrect by {user}', true);
            break;
        case 0:
            $title = __('Sentences marked as unsure by {user}', true);
            break;
        default:
            $title = __('Sentences marked as correct by {user}', true);
            break;
    }
}

$title = format($title, array('user' => $username));
$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <div class="module">
        <?php
        echo $html->tag('h2', __('Filter', true));

        $menu = array(
            $html->link(
                __('Marked as correct', true),
                array('action' => 'of', $username, 'correct')
            ),
            $html->link(
                __('Marked as unsure', true),
                array('action' => 'of', $username, 'not-sure')
            ),
            $html->link(
                __('Marked as incorrect', true),
                array('action' => 'of', $username, 'incorrect')
            ),
            $html->link(
                __('All the sentences', true),
                array('action' => 'of', $username)
            ),
        );
        echo '<ul class="annexeMenu">';
        foreach($menu as $item) {
            echo $html->tag('li', $item, array('class' => 'item'));
        }
        echo '</ul>';
        ?>
    </div>
</div>

<div id="main_content">
    <div class="module">

    <h2>
        <?php
        echo $paginator->counter(array(
            'format' => $title . ' ' . __("(total %count%)", true)
        ));
        ?>
    </h2>

    <?php
    $paginationUrl = array($username, $correctnessLabel, $lang);
    $pagination->display($paginationUrl);

    $type = 'mainSentence';
    $parentId = null;
    $withAudio = false;
    foreach ($corpus as $sentence) {
        $sentences->displayGenericSentence(
            $sentence['Sentence'],
            $type,
            $parentId,
            $withAudio
        );
    }

    $pagination->display($paginationUrl);
    ?>
    </div>
</div>
