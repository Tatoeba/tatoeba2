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

/**
 * Display latest contributions.
 *
 * @category Contributions
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
 
$this->set('title_for_layout', "Tatoeba - " . __("Latest contributions", true)); 
?>

<div id="annexe_content">
    <?php $commonModules->createFilterByLangMod(); ?> 
    <div class="module">
        <h2><?php __('Meaning of the colors'); ?></h2>
        <ul id="logsLegend">
        <li class="sentenceAdded"><?php __('sentence added'); ?></li>
        <li class="sentenceModified"><?php __('sentence modified'); ?></li>
        <li class="sentenceDeleted"><?php __('sentence deleted'); ?></li>
        </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php __('Contributions'); ?></h2>
        <?php
        $pagination->display(array($langFilter));
        ?>

        <table id="logs">
        <?php
        foreach ($contributions as $contribution) {
            $type = $contribution['Contribution']['type'];
            $sentenceId = $contribution['Contribution']['sentence_id'];
            $datetime = $contribution['Contribution']['datetime'];
            $action = $contribution['Contribution']['action'];
            $userId = $contribution['Contribution']['user_id'];
            if ($type == 'sentence') {
                $text = $contribution['Contribution']['text'];
                $lang = $contribution['Contribution']['sentence_lang'];
                $logs->displaySentenceEntry(
                    $sentenceId,
                    $text, 
                    $lang, 
                    $users[$userId], 
                    $datetime, 
                    $action
                );
            } else if ($type == 'link') {
                $translationId = $contribution['Contribution']['translation_id'];
                $logs->displayLinkEntry(
                    $sentenceId, 
                    $translationId, 
                    $users[$userId], 
                    $datetime, 
                    $action
                );
            }
        }
        ?>
        </table>

        <?php
        $pagination->display(array($langFilter));
        ?>
    </div>
</div>
