<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
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
 * @link     http://tatoeba.org
 */

/**
 * General view for the wall. Here are displayed all the messages.
 *
 * @category Wall
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */ 

$this->pageTitle = 'Tatoeba - ' . __('Wall', true);

?>
<div id="annexe_content" >
    <div class="module" >
        <h2><?php __('Tips'); ?></h2>
        <p>
            <?php
            __(
                'Here you can ask general questions like how to use Tatoeba, ' .
                'report bugs or strange behaviors, or simply socialize with the'.
                ' rest of the community.'
            );
            ?>
         </p>

        <p><?php __("Have fun! Don't be shy!"); ?></p>
    </div>

    <div class="module" >
        <h2><?php __('Latest messages'); ?></h2>
        <ul>
            <?php
            $mesg = count($tenLastMessages);
            
            for ($i = 0 ; $i < min(10, $mesg); $i++) {
                $currentMessage = $tenLastMessages[$i] ;
                echo '<li>';
                // text of the link
                $text = $date->ago($currentMessage['Wall']['date'])
                        . ", "
                        . __('by ', true)
                        . $currentMessage['User']['username'];
                // path of the link
                $path = array(
                    'controller' => 'wall',
                    'action' => 'show_message',
                    $currentMessage['Wall']['id']
                );
                // TODO Remove the whole if block when tree behavior ready
                if ($option == null) {
                    $path = array(
                        'controller' => 'wall',
                        'action' => 'index#message_'.$currentMessage['Wall']['id']
                    );
                }
                // link
                echo $html->link($text, $path);
                echo '</li>';
            };
            ?>
        </ul>
    </div>
</div>

<div id="main_content">
    <div class="module">
        
        <h2>
        <?php 
        if ($option != null) {
            echo $paginator->counter(
                array(
                    'format' => __(
                        'Wall (%count% threads)',
                        true
                    )
                )
            );
        } else {
            __('Wall');
            echo ' (';
            echo $html->link(
                'Display paginated version', // temporary text, no i18n
                array(
                    'controller' => 'wall',
                    'action' => 'index',
                    'paginated'
                )
            );
            echo ')';
        }
        ?>
        </h2>
        
        
        <?php
        // leave a comment part
        if ($isAuthenticated) {
            echo '<div id="sendMessageForm">'."\n";
            echo $wall->displayAddMessageToWallForm();
            echo '</div>'."\n";
        }
        ?>
        
        <?php
        // Pagination
        
        // TODO Remove "if paginated" as well when tree behavior ready
        if ($option == 'paginated') {
            // This is needed so that the paginator uses the right link.
            // Otherwise it won't add the 'paginated' in the link.
            $paginator->options(
                array(
                    'url' => array( 
                        'controller' => 'wall', 
                        'action' => 'index', 
                        'paginated'
                    )
                )
            );
        
            ?>
            <div class="paging">
            <?php 
            echo $paginator->prev(
                '<< '.__('previous', true), 
                array(), 
                null, 
                array('class'=>'disabled')
            ); 
            
            echo $paginator->numbers(array('separator' => ''));
            
            echo $paginator->next(
                __('next', true).' >>',
                array(),
                null, 
                array('class'=>'disabled')
            ); 
            ?>
            </div>
            <?php
        }
        ?>
        
        <ol class="wall">
        <?php
        // display comment part
        foreach ($firstMessages as $message) {
        
            $messageId = $message['Wall']['id'];
            
            echo '<li id="message_'.$messageId.'" class="topThread" >'."\n";
            // Root message
            $wall->createRootDiv(
                $message['Wall'], 
                $message['User'], 
                $messagesPermissions[$messageId]
            );

            // replies
            echo '<div class="replies" id="messageBody_'.$messageId .'" >';
            if (count($message['Reply']) >0) {
                echo '<ul>';
                foreach ($message['Reply'] as $reply ) {
                    $wall->createReplyDiv(
                        // this is because the allMessages array
                        // is indexed with message Id
                        $allMessages[$reply['id']],
                        $allMessages,
                        $messagesPermissions
                    );
                }
                echo '</ul>';
            }
            echo '</div>';
            echo '</li>';
        }
        ?>
        </ol>
        
        <?php
        // Pagination
        // TODO Remove "if paginated" when tree behavior ready
        if ($option != null) {
            ?>
            <div class="paging">
            <?php 
            echo $paginator->prev(
                '<< '.__('previous', true), 
                array(), 
                null, 
                array('class'=>'disabled')
            ); 
            
            echo $paginator->numbers(array('separator' => ''));
            
            echo $paginator->next(
                __('next', true).' >>',
                array(),
                null, 
                array('class'=>'disabled')
            ); 
            ?>
            </div>
            <?php
        }
        ?>
        
    </div>
</div>
