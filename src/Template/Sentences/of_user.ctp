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
 * @link     https://tatoeba.org
 */
$userName = h($userName);

if ($userExists === true) {
    $numberOfSentences = $this->Paginator->param('count');
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

    echo $this->Html->script('sentences/only_original.ctrl.js', ['block' => 'scriptBottom']);
    ?>
    <div ng-controller="OriginalSentencesController" class="section md-whiteframe-1dp" layout="column">
        <md-checkbox
            class="md-primary"
            ng-model="original"
            ng-checked="<?= $onlyOriginal ?>"
            ng-click="toggle()">
            <?= __('Only show original sentences') ?>
        </md-checkbox>
    </div>
</div>

<div id="main_content">
    <div class="section md-whiteframe-1dp">

    <?php
    if ($userExists === false) {
        $this->CommonModules->displayNoSuchUser($userName);
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

        echo $this->Html->link(__('Go back to previous page'), 'javascript:history.back()');

    } else {
        ?>

        <h2>
            <?php
            echo $this->Paginator->counter($title . ' ' . __("(total {{count}})"));
            ?>
        </h2>

        <div class="sortBy">
            <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            if ($onlyOriginal) {
                $urlOptions = $this->Paginator->generateUrlParams(
                    array('?' => array('only_original' => ''))
                );
                $this->Paginator->options(array('url' => $urlOptions));
            }
            echo $this->Paginator->sort('modified', __('date modified'));
            echo " | ";
            echo $this->Paginator->sort('created', __('date created'));
            ?>
        </div>

        <?php
        $this->Pagination->display();


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


        $this->Pagination->display();
    }
    ?>
    </div>
</div>
