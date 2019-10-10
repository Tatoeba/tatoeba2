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
use App\Model\Entity\Language;

$this->set('title_for_layout', $this->Pages->formatTitle(__('Languages of members')));
?>

<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Legend'); ?></h2>
        <ul class="usersLanguagesLegend">
            <?php
            for ($i = Language::MAX_LEVEL; $i >= 0; $i--) {
                $legend = $this->Html->tag('span', $this->Languages->getLevelsLabels($i));
                echo $this->Html->tag('li', $this->Languages->smallLevelBar($i) . $legend);
            }
            ?>
            <li>
                <div class="languageLevel">
                    <div class="unknownLevel key">?</div>
                </div><span><?php echo __('Unspecified'); ?></span>
            </li>
        </ul>
    </div>
</div>

<div id="main_content">
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Languages of members'); ?></h2>
        <table class="usersLanguagesStats">
            <tr>
                <th></th>
                <th><?php echo __('Language'); ?></th>
                <?php
                for ($i = Language::MAX_LEVEL; $i >= 0; $i--) {
                    echo $this->Html->tag('th', $this->Languages->smallLevelBar($i));
                }
                ?>
                <th>
                    <div class="languageLevel">
                        <div class="unknownLevel">?</div>
                    </div>
                </th>
                <th><?php echo __('Total'); ?></th>
            </tr>
            <?php
            foreach($stats as $language) {
                $langCode = $language->code;
                $langName = $this->Html->link(
                    $this->Languages->codeToNameAlone($langCode),
                    array(
                        'controller' => 'users',
                        'action' => 'for_language',
                        $langCode
                    )
                );

                echo '<tr>';
                echo $this->Html->tag('td', $this->Languages->icon($langCode));
                echo $this->Html->tag('td', $langName);
                echo $this->Html->tag('td', $this->Number->format($language->level_5));
                echo $this->Html->tag('td', $this->Number->format($language->level_4));
                echo $this->Html->tag('td', $this->Number->format($language->level_3));
                echo $this->Html->tag('td', $this->Number->format($language->level_2));
                echo $this->Html->tag('td', $this->Number->format($language->level_1));
                echo $this->Html->tag('td', $this->Number->format($language->level_0));
                echo $this->Html->tag('td', $this->Number->format($language->level_unknown));
                echo $this->Html->tag('td', $this->Number->format($language->total));
                echo '</tr>';
            }
            ?>
        </table>
    </div>
</div>
