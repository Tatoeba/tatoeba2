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
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use Cake\Core\Configure;
use App\Model\Search;


class VocabularyHelper extends AppHelper
{
    public $helpers = array(
        'Html', 'Url'
    );

    public function vocabulary($vocab) {
        $lang = $vocab['lang'];
        $text = $this->_View->safeForAngular($vocab['text']);
        $numSentences = $vocab['numSentences'];
        if (is_null($numSentences)) {
            $numSentencesLabel = __('Unknown number of sentences');
        } else {
            $numSentencesLabel = format(
                __n(
                    '{number} sentence', '{number} sentences',
                    $numSentences,
                    true
                ),
                array('number' => $numSentences == 1000 ? '1000+' : $numSentences)
            );
        }
        if (Configure::read('Search.enabled')) {
            $url = $this->Url->build(array(
                'controller' => 'sentences',
                'action' => 'search',
                '?' => array(
                    'query' => Search::exactSearchQuery($text),
                    'from' => $lang,
                    'orphans' => '',
                    'unapproved' => ''
                )
            ));
        }
        ?>
        <img class="vocabulary-lang" src="/img/flags/<?= $lang ?>.svg"/>
        <div class="vocabulary-text" flex><?= $text ?></div>
        <md-button ng-cloak class="md-primary" <?= isset($url) ? "href=\"$url\"" : 'ng-disabled="1"' ?>>
            <?= $numSentencesLabel ?>
        </md-button>
        <?php
    }
}
?>
