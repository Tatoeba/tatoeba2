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
$this->set('title_for_layout', $pages->formatTitle(__('Languages of members', true)));
?>

<div id="annexe_content">
    <div class="module">
        <h2><?php __('Legend'); ?></h2>
        <ul class="usersLanguagesLegend">
            <?php
            for ($i = Language::MAX_LEVEL; $i >= 0; $i--) {
                $legend = $html->tag('span', $languages->getLevelsLabels($i));
                echo $html->tag('li', $languages->smallLevelBar($i) . $legend);
            }
            ?>
            <li>
                <div class="languageLevel">
                    <div class="unknownLevel key">?</div>
                </div><span><?php __('Unspecified'); ?></span>
            </li>
        </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php __('Languages of members'); ?></h2>
        <table class="usersLanguagesStats">
            <tr>
                <th></th>
                <th><?php __('Language'); ?></th>
                <?php
                for ($i = Language::MAX_LEVEL; $i >= 0; $i--) {
                    echo $html->tag('th', $languages->smallLevelBar($i));
                }
                ?>
                <th>
                    <div class="languageLevel">
                        <div class="unknownLevel">?</div>
                    </div>
                </th>
                <th><?php __('Total'); ?></th>
            </tr>
            <?php
            foreach($stats as $stat) {
                $language = $stat['Language'];
                $langCode = $language['code'];
                $langName = $html->link(
                    $languages->codeToNameAlone($langCode),
                    array(
                        'controller' => 'users',
                        'action' => 'for_language',
                        $langCode
                    )
                );

                echo '<tr>';
                echo $html->tag('td', $languages->icon($langCode, array()));
                echo $html->tag('td', $langName);
                echo $html->tag('td', $language['level_5']);
                echo $html->tag('td', $language['level_4']);
                echo $html->tag('td', $language['level_3']);
                echo $html->tag('td', $language['level_2']);
                echo $html->tag('td', $language['level_1']);
                echo $html->tag('td', $language['level_0']);
                echo $html->tag('td', $language['level_unknown']);
                echo $html->tag('td', $stat[0]['total']);
                echo '</tr>';
            }
            ?>
        </table>
    </div>
</div>
