<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2026 Bernhard Seckinger <kumakyoo@kumakyoo.de>
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
 * @author   Bernhard Seckinger <kumakyoo@kumakyoo.de>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

if (empty($lang)) {
    $title = __('Translation statistics for all languages');
} else {
    $title = format(
        __('Translation statistics for {language}'),
        array('language' => $this->Languages->codeToNameToFormat($lang))
    );
}

$this->set('title_for_layout', $this->Pages->formatTitle($title));

?>
<div id="annexe_content">
    <?php $this->CommonModules->createFilterByLangMod(); ?>
</div>

<div id="main_content">
<div class="section md-whiteframe-1dp">
    <h2><?= $title; ?></h2>

    <p>
    <?php
        if (empty($lang)) {
            echo '<p>'.format(
                __n('There is one sentence in the corpus.',
                'There are {total}&nbsp;sentences in the corpus.',
                $total),
                array('total' => $total)
            );
        }
        else {
            echo '<p>'.format(
                __n('There is one sentence in {language}.',
                'There are {total}&nbsp;sentences in {language}.',
                $total),
                array('language' => $this->Languages->codeToNameToFormat($lang),
                      'total' => $total)
            );
       }
    ?>                          

    <?php 
    if (empty($stats)) {
        echo __('There are no sentences with translations.');
        return;
    }
    echo format(
        __n('One sentence has at least one translation.',
            '{total}&nbsp;sentences have at least one translation.',
            $totalTranslations),
        array('total' => $totalTranslations)
    );
    ?>

    <table class="language-stats">
    <tr>
        <th colspan="3">
        <?php /* @translators: table header text in "Translation statistics for language" page */ ?>
        <th><?php echo __('Language'); ?></th>
        <?php /* @translators: table header text in "Translation statistics for language" page */ ?>
        <th><?php echo __('Translations'); ?></th>
    </tr>

    <?php
    $rank = 1;
    foreach ($stats as $language) {
        $langCode = $language['code'];
        $numSentences = $language['translations'];
        
        if (!isset($max)) {
            // table is sorted by $numSentences, thus the first entry is the maximum
            $max = $numSentences;
        }
        
        if ($max == 0) {
            $percent = 0;
        } else {
            $percent = ($numSentences / $max) * 100;
        }
        $numSentencesDiv  = '<div class="bar" style="width:'.$percent.'%"></div>';
        $numSentencesDiv .= $this->Number->format($numSentences);

        $languageIcon = $this->Languages->icon($langCode);

        if (empty($lang)) {
            $from = $langCode;
            $to = '';
        } else {
            $from = $lang;
            $to = $langCode;
        }

        $langName = $this->Languages->codeToNameAlone($langCode);
        if (empty($langCode)) {
            $langCode = 'unknown';
            $languageLink = 'unknown';
        }
        else
        {
            $languageLink = $this->Html->link(
                $langName,
                [
                  'controller' => 'sentences',
                  'action' => 'search',
                  '?' => [
                    'from' => $from,
                    'to' => $to,
                    'trans_to' => $to,
                    'sort' => 'created',
                    'trans_filter' => 'limit',
                    'trans_link' => 'direct'
                  ]
               ]
            );
        }

        echo '<tr>';

        echo $this->Html->tag('td', $this->Number->format($rank));
        echo $this->Html->tag('td', $languageIcon);
        echo $this->Html->tag('td', $langCode);
        echo $this->Html->tag('td', $languageLink);
        echo $this->Html->tag('td', $numSentencesDiv, array('class' => 'num-sentences'));

        echo '</tr>'.PHP_EOL;

        $rank++;
    }
    ?>
    </table>
</div>
</div>
