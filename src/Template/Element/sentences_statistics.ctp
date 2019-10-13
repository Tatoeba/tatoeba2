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
?>

<md-list class="annexe-menu sentences-stats md-whiteframe-1dp">
    <md-subheader>
        <?= format(
            __n('One sentence','{n}&nbsp;sentences', $numSentences),
            ['n' => $this->Number->format($numSentences)]
        ); ?>
    </md-subheader>
    
    <?php
    foreach ($stats as $stat) {
        $langCode = $stat->code;
        $numberOfSentences = $stat->sentences;
        $url = $this->Url->build([
            'controller' => 'sentences',
            'action' => 'show_all_in',
            $langCode,
            'none',
            'none',
            'indifferent',
        ]);
        ?>
        <md-list-item href="<?= $url ?>">
            <?= $this->Languages->icon($langCode) ?>
            <p><?= $this->Number->format($numberOfSentences) ?></p>
        </md-list-item>
        <?php
    }
    ?>
    
    <md-list-item>
    <?php 
    echo $this->Html->link(
        __('show all languages'),
        array(
            'controller' => 'stats',
            'action' => 'sentences_by_language'
        )
    );
    ?>
    </md-list-item>

</md-list>
