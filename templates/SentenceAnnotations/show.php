<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<div id="annexe_content">
    <?php
    $this->SentenceAnnotations->displayGoToBox();

    $this->SentenceAnnotations->displaySearchBox();

    if(isset($sentence)){
        $this->SentenceAnnotations->displayNewIndexBox($sentence['id']);
    }
    ?>
</div>

<div id="main_content">
    <div class="module" ng-non-bindable>
    <?php
    if(isset($sentence)){
        ?>
        <h2>
        <?php
        echo format(__('Sentence #{number}') , array('number' => $sentence['id']));
        ?>
        </h2>

        <p class="original">
        <?php echo $sentence['text']; ?>
        </p>

        <?php

        foreach($annotations as $annotation){
            ?>
            <hr/>

            <p>
            <?php echo h($annotation['text']); ?>
            </p>

            <?php
            echo $this->Form->create($annotation, array(
                "url" => array("action" => "save"))
            );

            // hidden ids necessary for saving
            echo '<div>';
            echo $this->Form->hidden('id');
            echo $this->Form->hidden('sentence_id');
            echo '</div>';

            // id of the "meaning" (i.e. English sentence for Tanaka sentences annotations)
            echo $this->Form->control('meaning_id', ['type' => 'text']);

            // annotations text
            echo $this->Form->textarea('text', ['cols' => 60, 'rows' => 3]);

            // delete link
            echo $this->Html->link('delete',
                [
                    'controller' => 'sentence_annotations',
                    'action' => 'delete',
                    $annotation->id,
                    $annotation->sentence_id
                ],
                [
                    'style' => 'float:right',
                    'confirm' => 'Are you sure?'
                ]
            );

            // save button
            echo $this->Form->submit('save');
            echo $this->Form->end();
        }
    }
    ?>
    </div>
</div>
