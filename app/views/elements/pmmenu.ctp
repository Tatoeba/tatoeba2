<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Etienne Deparis <etienne.deparis@umaneti.net>
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
 * @author   Etienne Deparis <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

?>
<div id="annexe_content">
    <div class="module">
        <h2><?php echo __('Private messages', true); ?></h2>
        <ul>
            <li>
                <?php
                echo $html->link(
                    __('New message', true), array('action' => 'write')
                ); 
                ?>
            </li>
            <li>&nbsp;</li>
            <li>
                <?php
                echo $html->link(
                    __('Inbox', true), array('action' => 'folder', 'Inbox')
                ); 
                ?>
                >
                <?php
                echo $html->link(
                    __('Unread', true), array('action' => 'folder', 'Inbox', 'unread')
                ); 
                ?>
            </li>
            <li>
                <?php
                echo $html->link(
                    __('Sent', true), array('action' => 'folder', 'Sent')
                );
                ?>
            </li>
            <li><?php
                echo $html->link(
                    __('Trash', true), array('action' => 'folder', 'Trash')
                );
                ?>
            </li>
        </ul>
    </div>
    
    <div class="module">
    <h2><?php __('Question about Tatoeba?'); ?></h2>
    <p>
    <?php 
    __(
        'If you would like to ask a question about Tatoeba, please send '.
        'an email at <strong>team@tatoeba.fr</strong> rather than a private '.
        'message. '
    ); 
    ?>
    </p>
    <p>
    <?php
        __('Both sysko and Trang will receive it and you may have a reply faster.');
    ?>
    </p>
    </div>
    
</div>
