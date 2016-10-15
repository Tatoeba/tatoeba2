<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

$username = Sanitize::paranoid($username, array("_"));

if (!empty($lang)) {
    $title = format(
        __('Translate {language} sentences that belong to {user}', true),
        array('language' => $languages->codeToNameToFormat($lang),
              'user'     => $username)
    );
} else {
    $title = format(
        __('Translate sentences that belong to {user}', true),
        array('user' => $username)
    );
}
$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">    
    <?php     
    echo $this->element(
        'users_menu', 
        array('username' => $username)
    );
    
    $commonModules->createFilterByLangMod(2); 
    ?> 
</div>

<div id="main_content">    
    
    <div class="section">
    <?php 
    echo $this->Pages->formatTitleWithResultCount($paginator, $title);

    if ($results != null) {
        $paginationUrl = array(
            $username,
            $lang
        );
        $pagination->display($paginationUrl);

        if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
            foreach ($results as $sentence) {
                echo $this->element(
                    'sentences/sentence_and_translations',
                    array(
                        'sentence' => $sentence['Sentence'],
                        'translations' => $sentence['Translation'],
                        'user' => $sentence['User']
                    )
                );
            }
        } else {
            foreach ($results as $sentence) {
                $sentences->displaySentencesGroup(
                    $sentence['Sentence'],
                    $sentence['Transcription'],
                    $sentence['Translation'],
                    $sentence['User']
                );
            }
        }
        
        $pagination->display($paginationUrl);
    }
    ?>
    </div>
</div>
