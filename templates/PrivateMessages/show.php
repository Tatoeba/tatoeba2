<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 Etienne Deparis <etienne.deparis@umaneti.net>
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

if (empty($message->title)) {
    $messageTitle = __('[no subject]');
} else {
    $messageTitle = $message->title;
}
$this->set('title_for_layout', $this->Pages->formatTitle(
    $messageTitle
    .' - ' 
    .__('Private messages') 
));

?>
<md-toolbar class="md-hue-2">
    <div class="md-toolbar-tools">
        <h2 flex><?= __('Private messages') ?></h2>
        
        <?php
        $this->Pagination->display();
        ?>
    </div>
</md-toolbar>

<section layout="row" flex ng-cloak>
    <?= $this->element('pmmenu'); ?>

    <md-content class="md-whiteframe-1dp" flex>
        <md-toolbar class="md-hue-1">
            <div class="md-toolbar-tools">
                <h2 flex>
                    <?= h($this->safeForAngular($message->title)) ?>
                </h2>
            </div>
        </md-toolbar>
        <?php
        $author = $message->author;
        echo $this->element('private_messages/message', [
            'message' => $message,
            'user' => $author
        ]);
        ?>
        
        <a name="reply"></a>
        <?php
        if ($message->folder == 'Inbox' && $message->type == 'human') {
            echo $this->element('private_messages/form', [
                'pm' => $message,
                'recipients' => $author->username,
                'isReply' => true
            ]);
        }
        ?>
    </md-content>
</section>
