<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
use Cake\I18n\I18n;

$this->set('title_for_layout', $this->Pages->formatTitle(__('Number of sentences per language')));

$max = $stats[0]['sentences'];
?>
<div id="annexe_content">
    <?= $this->element('audio_stats',
        [ 'stats' => $audioStats ],
        [ 'cache' => [
            'config' => 'stats',
            'key' => 'audio_stats_'.I18n::getLocale(),
        ]]
    ); ?>
</div>

<div id="main_content">
<div class="section md-whiteframe-1dp">
    <h2>
    <?php 
    echo format(__n('One sentence', '{n}&nbsp;sentences', $totalSentences),
                array('n' => $this->Number->format($totalSentences)));
    ?>
    </h2>
    
    <table class="languages-stats">
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <?php /* @translators: table header text in "Number of sentences per language" page */ ?>
        <th><?php echo __('Language'); ?></th>
        <th><?php echo __('Sentences'); ?></th>
    </tr>

    <?php
    $rank = 1;
    foreach ($stats as $language) {
        $langCode = $language['code'];
        $numSentences = $language['sentences'];
        if ($max == 0) {
            $percent = 0;
        } else {
            $percent = ($numSentences / $max) * 100;
        }
        $numSentencesDiv  = '<div class="bar" style="width:'.$percent.'%"></div>';
        $numSentencesDiv .= $this->Number->format($numSentences);

        $languageIcon = $this->Languages->icon($langCode);

        $langName = $this->Languages->codeToNameAlone($langCode);
        if (empty($langCode)) {
            $langCode = 'unknown';
        }
        $languageLink = $this->Html->link(
            $langName,
            array(
                "controller" => "sentences",
                "action" => "show_all_in",
                $langCode,
                'none',
                'none',
                'indifferent'
            )
        );

        echo '<tr>';

        echo $this->Html->tag('td', $this->Number->format($rank));
        echo $this->Html->tag('td', $languageIcon);
        echo $this->Html->tag('td', $langCode);
        echo $this->Html->tag('td', $languageLink);
        echo $this->Html->tag('td', $numSentencesDiv, array('class' => 'num-sentences'));

        echo '</tr>';

        $rank++;
    }
    ?>
    </table>
</div>
</div>
