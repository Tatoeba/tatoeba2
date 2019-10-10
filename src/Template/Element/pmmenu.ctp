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
 * @link     https://tatoeba.org
 */

?>
<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Private messages'); ?></h2>
        <ul>
            <li>
                <?php
                echo $this->Html->link(
                    __('New message'), array('action' => 'write')
                ); 
                ?>
            </li>
            <?php
                if ($this->request->params['action'] == 'folder'
                    && $this->request->params['pass']
                    && $this->request->params['pass'][0] == 'Trash') {
                    echo $this->Html->tag('li', $this->Html->link(
                        __('Empty trash'),
                        array('action' => 'empty_folder', 'Trash'),
                        array('confirm' => __('Are you sure?'))
                    ));
                }
            ?>
            <li>&nbsp;</li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Inbox'), array('action' => 'folder', 'Inbox')
                ); 
                ?>
                &gt;
                <?php
                echo $this->Html->link(
                    __('Unread'), array('action' => 'folder', 'Inbox', 'unread')
                ); 
                ?>
            </li>
            <li><?php
                echo $this->Html->link(
                    __('Drafts'), array('action' => 'folder', 'Drafts')
                );
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(
                    __('Sent'), array('action' => 'folder', 'Sent')
                );
                ?>
            </li>
            <li><?php
                echo $this->Html->link(
                    __('Trash'), array('action' => 'folder', 'Trash')
                );
                ?>
            </li>
        </ul>
    </div>
</div>
