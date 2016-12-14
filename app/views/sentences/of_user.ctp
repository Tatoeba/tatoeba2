<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
$userName = Sanitize::paranoid($userName, array("_"));

if ($userExists === true) {
    $numberOfSentences = (int) $paginator->counter(
        array(
            "format" => "%count%"
        )
    );
    $paginator->options(
        array(
            'url' => $this->params['pass']
        ) 
    );

    if (empty($lang)) {
        $title = format(__("{user}'s sentences", true), array('user' => $userName));
    } else {
        $languageName = $languages->codeToNameToFormat($lang);
        $title = format(__('{user}\'s sentences in {language}', true),
                        array('user' => $userName, 'language' => $languageName));
    }
} else {
    $title = format(__("There's no user called {username}", true), array('username' => $userName));
}

$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    echo $this->element(
        'users_menu', 
        array('username' => $userName)
    );
    
    $commonModules->createFilterByLangMod(2);
    ?>
</div>
    
    
<div id="main_content">
    <div class="module">

    <?php
    if ($userExists === false) {
        $commonModules->displayNoSuchUser($userName, $backLink);
    } elseif ($numberOfSentences === 0) {
        echo '<h2>';
        if (!empty($lang)) {
            $langName = $languages->codeToNameToFormat($lang);
            echo format(
                __('{user} does not have any sentence in {language}', true),
                array('user' => $userName, 'language' => $langName)
            );
        } else {
            echo format(
                __("{user} does not have any sentence", true),
                array('user' => $userName)
            );
        }
        echo '</h2>';

        echo $html->link(__('Go back to previous page', true), $backLink);

    } else {
        ?>

        <h2>
            <?php 
            echo $paginator->counter(
                array(
                    'format' => $title . ' ' . __("(total %count%)", true)
                )
            ); 
            ?>
        </h2>
        
        <div class="sortBy">
            <strong><?php __("Sort by:") ?> </strong>
            <?php
            echo $this->Paginator->sort(__('date modified', true), 'modified');
            echo " | ";
            echo $this->Paginator->sort(__('date created', true), 'created');
            ?>
        </div>
        
        <?php
        $paginationUrl = array($userName, $lang);
        $pagination->display($paginationUrl);
        
        
        $type = 'mainSentence';
        $parentId = null;
        $withAudio = false;
        foreach ($user_sentences as $sentence) {
            $sentences->displayGenericSentence(
                $sentence,
                $type,
                $parentId,
                $withAudio
            );
        }
        
        
        $pagination->display($paginationUrl);
    }
    ?>
    </div>
</div>
