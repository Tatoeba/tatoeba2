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

$this->set('title_for_layout', $pages->formatTitle(__('Wall', true)));

$javascript->link('jquery.scrollTo-min.js', false);
$javascript->link('wall.reply.js', false);
$javascript->link('wall.show_and_hide_replies.js', false);

?>
<div id="annexe_content" >
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>
    
    <div class="module" >
        <h2><?php __('Tips'); ?></h2>
        <p>
        <?php
        __(
            'Here you can ask general questions like how to use Tatoeba, ' .
            'report bugs or strange behavior, or simply socialize with the'.
            ' rest of the community.'
        );
        ?>
        </p>
        
        <p>
        <?php
        echo format(
            __(
                'Before asking a question, '.
                'make sure to read the <a href="{}">FAQ</a>.', true
            ),
            $html->url(array('controller' => 'pages', 'action' => 'faq'))
        );
        ?>
        </p>
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
                $text = format(__('{date}, by {author}', true),
                               array(
                                   'date' => $date->ago($currentMessage['Wall']['date']),
                                   'author' => $currentMessage['User']['username']
                               ));
                
                $path = array(
                    'controller' => 'wall',
                    'action' => 'index#message_'.$currentMessage['Wall']['id']
                    );
                // link
                echo $html->link($text, $path, array('escape' => false));
                echo '</li>';
            };
            ?>
        </ul>
    </div>
        
    <div class="wallBanner">
    <?php 
    echo $html->link(
        __(
            'You may write in any language you want. '.
            'At Tatoeba, all languages are equal.', true
        ),
        array(
            "controller" => "sentences",
            "action" => "show",
            785667
        )
    );
    ?>
    </div>

</div>

<div id="main_content">
    <div class="module">
        <h2>
            <?php
            $threadsCount = $paginator->counter(array('format' => '%count%'));
            echo format(__n('Wall (one thread)', 'Wall ({n}&nbsp;threads)', $threadsCount, true),
                        array('n' => $threadsCount));
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
        $pagination->display();
        ?>
        
        <div class="wall">
        <?php
        // display comment part
        foreach ($allMessages as $message) {
            $wall->createThread(
                $message['Wall'],
                $message['User'],
                $message['Permissions'],
                $message['children']
            );
        }
        ?>
        </div>
        
        <?php
        $pagination->display();
        ?>
    </div>
</div>
