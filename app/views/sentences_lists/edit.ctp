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

$javascript->link('sentences_lists.remove_sentence_from_list.js', false);
$javascript->link('sentences_lists.edit_name.js', false);
$javascript->link('sentences_lists.add_new_sentence_to_list.js', false);
$javascript->link('jquery.jeditable.js', false);
?>


<div id="annexe_content">
    <div class="module">
    <h2><?php __('Actions'); ?></h2>
    <ul class="sentencesListActions">
        <li>
            <?php
            echo $html->link(
                __('Back to all the lists', true),
                array("controller"=>"sentences_lists", "action"=>"index")
            );
            ?>
        </li>
        <li>
            <?php
            echo $html->link(
                __('Send via Private Message', true),
                array(
                    'controller' => 'private_messages',
                    'action' => 'join',
                    'list',
                    $list['SentencesList']['id']
                )
            );
            ?>
        </li>

        <li>
            <?php
            __('Show translations :'); echo ' ';
            
            $path  = '/' . Configure::read('Config.language') . 
                '/sentences_lists/edit/' . $list['SentencesList']['id'] . '/';

            // TODO onChange should be define in a separate js file
            // TODO use of languagesArray is a hack as for the moment
            // "all languages" is always the first selected, so you're not able to
            // select
            // it would be better to do the following 
            //  1 - set the previous selected language or "none" by default 
            //      (create a specific method to in language helper)
            //  2 - "all languages" would display all translations 
            //    - "none" would display only the sentence
            echo $form->select(
                "translationLangChoice",
                $languages->languagesArray(),
                null,
                array(
                    "onchange" => "$(location).attr('href', '".
                        $path."' + this.value);"
                ),
                false
            );
            ?>
        </li>


        <?php
        // only the creator of the list can delete a public list
        if ($session->read('Auth.User.id') == $list['SentencesList']['user_id']) {
            $javascript->link('sentences_lists.set_as_public.js', false);
            echo '<li>';
            echo '<label for="isPublic">' . __('Set list as public', true) .
                '</label>';
            
            if ($list['SentencesList']['is_public'] == 1) {
                $checkboxValue = 'checked';
            } else {
                $checkboxValue = '';
            }
            
            echo ' '.$form->checkbox(
                'isPublic',
                array("name" => "isPublic", "checked" => $checkboxValue)
            );
            echo ' '.$html->image(
                'loading-small.gif',
                array("id"=>"inProcess", "style"=>"display:none;")
            );
            echo ' '.$html->link(
                '[?]',
                array("controller"=>"pages", "action"=>"help#sentences_lists")
            );
            echo '</li>';

            echo '<li class="deleteList">';
            echo $html->link(
                __('Delete this list', true),
                array(
                    "controller" => "sentences_lists",
                    "action" => "delete",
                    $list['SentencesList']['id']
                ),
                null,
                __('Are you sure?', true)
            );
            echo '</li>';
        }
        ?>
    </ul>
    </div>

    <div class="module">
    <h2><?php __('Printable versions'); ?></h2>
    <ul class="sentencesListActions">
        <li>
            <?php
            echo $html->link(
                __('Print as exercise', true),
                array(
                    "controller"=>"sentences_lists",
                    "action"=>"print_as_exercise",
                    $list['SentencesList']['id'],
                    'hide_romanization'
                ),
                array(
                    "onclick" => "window.open(this.href,‘_blank’);return false;",
                    "class" => "printAsExerciseOption"
                )
            );
            ?>
        </li>
        <li>
            <?php
            if (!isset($translationsLang)) { 
                $translationsLang = 'und';
            }
            echo $html->link(
                __('Print as correction', true),
                array(
                    "controller"=>"sentences_lists",
                    "action"=>"print_as_correction",
                    $list['SentencesList']['id'],
                    $translationsLang,
                    'hide_romanization'
                ),
                array(
                    "onclick" => "window.open(this.href,‘_blank’);return false;",
                    "class" => "printAsCorrectionOption"
                )
            );
            ?>
        </li>
        <li>
            <?php
            $javascript->link('sentences_lists.romanization_option.js', false);
            echo $form->checkbox(
                'display_romanization',
                array("id" => "romanizationOption", "class" => "display")
            );
            echo ' ';
            __('Check this box to display romanization in the print version');
            ?>
        </li>
    </ul>
    </div>


    <div class="module">
    <h2><?php __('Tips'); ?></h2>
    <?php
    if ($session->read('Auth.User.id') == $list['SentencesList']['user_id']) {
        echo '<p>';
        echo __('You can change the name of the list by clicking on it.');
        echo '</p>';
    }
    ?>
    <p>
        <?php
        __('You can remove a sentence from the list by clicking on the X icon.'); 
        ?>
    </p>
    <p>
        <?php
        __(
            'Removing a sentence will not delete it. '.
            'The sentence will just not be part of the list anymore.'
        );
        ?>
     </p>
    </div>
</div>



<div id="main_content">
    <div class="module">
    <?php
    $class = '';
    if ($session->read('Auth.User.id') == $list['SentencesList']['user_id']) {
        $class = 'class="editable editableSentencesListName"';
    }
    echo '<h2 id="l'.$list['SentencesList']['id'].'" '.$class.'>'.
        $list['SentencesList']['name'].'</h2>';

    echo '<div id="newSentenceInList">';
    echo $form->input(
        'text',
        array("label" => __('Add a sentence to this list : ', true))
    );
    echo $form->button('OK', array("id" => "submitNewSentenceToList"));
    echo '</div>';

    echo '<p>';
    echo sprintf(
        __(
            'NOTE : You can also add existing sentences with this icon %s '.
            '(while <a href="%s">browsing</a> for instance).', true
        ),
        $html->image('add_to_list.png'),
        $html->url(array("controller"=>"sentences", "action"=>"show", "random"))
    );
    echo '</p>';


    echo '<div class="sentencesListLoading" style="display:none">';
    echo $html->image('loading.gif');
    echo '</div>';

    echo '<span class="sentencesListId" id="_'.
        $list['SentencesList']['id'].'" />'; // to retrieve id

    echo '<ul class="sentencesList editMode">';
    if (count($list['Sentence']) > 0) {

        foreach ($list['Sentence'] as $sentence) {
            echo '<li id="sentence'.$sentence['id'].'">';
                // delete button
                echo '<span class="options">';
                echo '<a id="_'.$sentence['id'].'" class="removeFromListButton">';
                echo $html->image('close.png');
                echo '</a>';
                echo '</span>';

                // display sentence
                if ($translationsLang != 'und') {
                    $sentences->displaySentenceInList($sentence, $translationsLang);
                } else {
                    $sentences->displaySentenceInList($sentence);
                }
            echo '</li>';
        }
    }
    echo '</ul>';
    ?>
    </div>
</div>
