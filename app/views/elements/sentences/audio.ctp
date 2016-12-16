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
 * @author   HO Ngoc Phuong Trang <trang@tatoeba.org>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
?>
 
<div class="module">
    <h2><?php __d('admin', 'Audio') ?></h2>
    <?php
    echo $form->create(
        "Sentence",
        array(
            "action" => "edit_audio",
            "type" => "post",
        )
    );
    echo $form->input(
        "id",
        array("value" => $sentenceId)
    );
    $hasaudio = count($audios) > 0;
    echo $form->input(
        "hasaudio", 
        array(
            "legend" => false,
            "type" => "radio",
            "options" => array(
                "yes" => "yes",
                "" => "no"
            ),
            "value" => $hasaudio
        )
    );

    $ownerName = '';
    $note = '';
    if ($hasaudio) {
        $audio = $audios[0];
        if ($audio['user_id'] && $audio['User']['username']) {
            $ownerName = $audio['User']['username'];
            $ownerUrl = $html->link(
                $ownerName,
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $ownerName
                )
            );
            $note = __d('admin', format(
                '{ownerName} is a member of Tatoeba.',
                array('ownerName' => $ownerUrl)
            ), true);
        } elseif (!empty($audio['external']['username'])) {
            $ownerName = $audio['external']['username'];
            $note = __d('admin', format(
                '<em>{ownerName}</em> is not a member of Tatoeba.',
                array('ownerName' => $ownerName)
            ), true);
        }
    }
    echo $form->input("ownerName",
        array(
            "value" => $ownerName
        )
    );
    if ($note) {
        echo $html->tag('p', $note);
    }
    echo $form->end(__d('admin', 'Submit', true));
    ?>
</div>
