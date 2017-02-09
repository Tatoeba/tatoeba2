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

$hasaudio = count($audios) > 0;
$shouldDisplayBlock = $hasaudio || CurrentUser::isAdmin();
if (!$shouldDisplayBlock) {
    return;
}

?>
<div class="module">
    <h2><?php echo __('Audio') ?></h2>
<?php

if ($hasaudio) {
    $this->Audio->displayAudioInfo($audios[0]);
}

if (CurrentUser::isAdmin()) {
    if ($hasaudio) {
        echo "<hr>";
    }
    echo $this->Form->create(
        "Sentence",
        array(
            "url" => array("action" => "edit_audio"),
            "type" => "post",
        )
    );
    echo $this->Form->input(
        "id",
        array("value" => $sentenceId)
    );
    __d("admin", "Enabled");
    echo $this->Form->input(
        "hasaudio",
        array(
            "legend" => false,
            "type" => "radio",
            "options" => array(
                1 => "yes",
                0 => "no"
            ),
            "value" => $hasaudio
        )
    );

    $ownerName = '';
    if ($hasaudio) {
        if ($audios[0]['user_id'] && $audios[0]['User']['username']) {
            $ownerName = $audios[0]['User']['username'];
        } else {
            $ownerName = $audios[0]['external']['username'];
        }
    }
    echo $this->Form->input("ownerName",
        array(
            "value" => $ownerName
        )
    );
    echo $this->Form->end(__d('admin', 'Submit'));
}
?>
</div>
