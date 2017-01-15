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
    $numberOfSentences = (int) $this->Paginator->counter(
        array(
            "format" => "%count%"
        )
    );
    $this->Paginator->options(
        array(
            'url' => $this->request->params['pass']
        ) 
    );

    if (empty($lang)) {
        $title = format(__("{user}'s sentences"), array('user' => $userName));
    } else {
        $languageName = $this->Languages->codeToNameToFormat($lang);
        $title = format(__('{user}\'s sentences in {language}'),
                        array('user' => $userName, 'language' => $languageName));
    }
} else {
    $title = format(__("There's no user called {username}"), array('username' => $userName));
}

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    echo $this->element(
        'users_menu', 
        array('username' => $userName)
    );
    
    $this->CommonModules->createFilterByLangMod(2);
    ?>
</div>
    
    
<div id="main_content">
    <div class="module">

    <?php
    if ($userExists === false) {
        $this->CommonModules->displayNoSuchUser($userName, $backLink);
    } elseif ($numberOfSentences === 0) {
        echo '<h2>';
        if (!empty($lang)) {
            $langName = $this->Languages->codeToNameToFormat($lang);
            echo format(
                __('{user} does not have any sentence in {language}'),
                array('user' => $userName, 'language' => $langName)
            );
        } else {
            echo format(
                __("{user} does not have any sentence"),
                array('user' => $userName)
            );
        }
        echo '</h2>';

        echo $this->Html->link(__('Go back to previous page'), $backLink);

    } else {
        ?>

        <h2>
            <?php 
            echo $this->Paginator->counter(
                array(
                    'format' => $title . ' ' . __("(total %count%)")
                )
            ); 
            ?>
        </h2>
        
        <div class="sortBy">
            <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            echo $this->Paginator->sort(__('date modified'), 'modified');
            echo " | ";
            echo $this->Paginator->sort(__('date created'), 'created');
            ?>
        </div>
        
        <?php
        $paginationUrl = array($userName, $lang);
        $this->Pagination->display($paginationUrl);
        
        
        $type = 'mainSentence';
        $parentId = null;
        $withAudio = false;
        foreach ($user_sentences as $sentence) {
            $this->Sentences->displayGenericSentence(
                $sentence,
                $type,
                $parentId,
                $withAudio
            );
        }
        
        
        $this->Pagination->display($paginationUrl);
    }
    ?>
    </div>
</div>
