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
namespace App\View\Helper;

use App\View\Helper\AppHelper;


/**
 * Helper to display navigation links (previous, next, random, go to)
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class NavigationHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Form',
        'Languages',
        'Images'
    );

    /**
     * Display navigation links for sentences.
     *
     * @param int $currentId Id of sentence currently displayed.
     *
     * @return void
     */
    public function displaySentenceNavigation(
        $currentId = null,
        $next = null,
        $prev = null
    ) {
        $controller = $this->request->params['controller'];
        $action = $this->request->params['action'];
        $input = $this->request->params['pass'][0];
        if ($currentId == null) {
            $currentId = intval($input);
            $next = $currentId + 1;
            $prev = $currentId - 1;
        }
        ?>
        <div class="navigation">
            <?php
            // go to form
            echo $this->Form->create(
                'Sentence',
                array(
                    'id' => 'SentenceGoToSentenceForm',
                    "url" => array("action" => "go_to_sentence"),
                    "type" => "get"
                )
            );
            echo $this->Form->input(
                'sentence_id',
                array(
                    "type" => "text",
                    "label" => __('Show sentence #: '),
                    "value" => $input,
                    "lang" => "",
                    "dir" => "ltr",
                )
            );
            echo $this->Form->submit(__('OK'));
            echo $this->Form->end();
            ?>

            <div class="languageSelect">
            <?php
            $this->Html->script('sentences.random.js', array('block' => 'scriptBottom'));


            $langArray = $this->Languages->languagesArrayAlone();
            $selectedLanguage = $this->request->getSession()->read('random_lang_selected');

            echo $this->Form->select(
                "randomLangChoiceInBrowse",
                $langArray,
                array(
                    'id' => 'randomLangChoiceInBrowse',
                    "value" => $selectedLanguage,
                    'class' => 'language-selector',
                    'data-current-sentence-id' => $currentId,
                    'empty' => false
                ),
                false
            );
            ?>

            <span class="smallTip">
            &lt;=
            <?php echo __('Language for previous, next or random sentence');
            ?>
            </span>
            </div>

            <ul class="options">

            <?php
            $prevClass = "inactive";
            $prevLink = "";
            $prevText = '« '.__('previous');

            if (!empty($prev)) {
                $prevClass = "active";
                $prevLink = array(
                    "controller" => $controller,
                    "action" => $action,
                    $prev
                );
            }

            ?>
            <li class="<?php echo $prevClass; ?>" id="prevSentence">
            <?php
            // previous
            if (empty($prev)) {
                echo $prevText;
            } else {
                echo $this->Html->link($prevText, $prevLink);
            }
            ?></li>

            <?php
            $nextClass = "inactive";
            $nextLink = "";
            $nextText = __('next').' »';

            if (!empty($next)) {
                $nextClass = "active";
                $nextLink = array(
                    "controller" => $controller,
                    "action" => $action,
                    $next
                );
            }

            ?>

            <li class="<?php echo $nextClass; ?>" id="nextSentence">
            <?php
            // next
            if (empty($next)) {
                echo $nextText;
            } else {
                echo $this->Html->link($nextText, $nextLink);
            }
            ?></li>


            <li class="active" id="randomLink">
            <?php
            // random
            echo $this->Html->link(
                __('random'),
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $selectedLanguage
                )
            );
            ?>
            </li>

            <li id="loadingAnimationForNavigation" style="display:none">
            <?php echo $this->Html->div('loader-small loader', ''); ?>
            </li>

            </ul>
        </div>
        <?php
    }


    /**
     * Display navigation for sentences lists.
     *
     * @return void
     */
    public function displaySentencesListsNavigation()
    {
        echo '<div class="navigation">';
            echo '<ul>';
            echo '<li class="option">';
            echo $this->Html->link(
                __('all lists'),
                array(
                    "controller" => "sentences_lists",
                    "action" => "index"
                )
            );
            echo '</li>';
            echo '</ul>';
        echo '</div>';
    }

}
?>
