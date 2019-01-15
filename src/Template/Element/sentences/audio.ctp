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
use App\Model\CurrentUser;

// Workaround for error:
//    The "License" alias has already been loaded with the following config
// Until someone finds a good way to refactor the involved helpers.
$this->helpers()->unload('License');

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
    echo $this->Form->hidden(
        "id",
        array("value" => $sentenceId)
    );
    __d("admin", "Enabled");
    echo $this->Form->control(
        "hasaudio",
        array(
            "label" => false,
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
        $audio = $audios[0];
        if ($audio->user_id && $audio->user->username) {
            $ownerName = $audio->user->username;
        } else {
            $ownerName = $audios->external->username;
        }
    }
    echo $this->Form->control("ownerName",
        array(
            "value" => $ownerName
        )
    );
    echo $this->Form->submit(__d('admin', 'Submit'));
    echo $this->Form->end();
}
?>
</div>
