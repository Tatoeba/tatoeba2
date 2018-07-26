<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2018  Gilles Bedel
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
 */

class VocabularyHelper extends AppHelper
{
    public $helpers = array(
        'Html',
    );

    public function vocabulary($vocab) {
        $lang = $vocab['lang'];
        $text = $vocab['text'];
        $numSentences = $vocab['numSentences'];
        if (is_null($numSentences)) {
            $numSentencesLabel = __('Unknown number of sentences');
        } else {
            $numSentences = $numSentences == 1000 ? '1000+' : $numSentences;
            $numSentencesLabel = format(
                __n(
                    '{number} sentence', '{number} sentences',
                    $numSentences,
                    true
                ),
                array('number' => $numSentences)
            );
        }
        if (isset($vocab['query'])) {
            $url = $this->Html->url(array(
                'controller' => 'sentences',
                'action' => 'search',
                '?' => array(
                    'query' => $vocab['query'],
                    'from' => $lang
                )
            ));
        }
        ?>
        <img class="vocabulary-lang" src="/img/flags/<?= $lang ?>.png"/>
        <div class="vocabulary-text" flex><?= $text ?></div>
        <md-button class="md-primary" <?= isset($url) ? "href=\"$url\"" : 'ng-disabled="1"' ?>>
            <?= $numSentencesLabel ?>
        </md-button>
        <?
    }
}
?>
