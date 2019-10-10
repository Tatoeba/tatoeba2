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
 * @link     https://tatoeba.org
 */

/**
 * General view for the wall. Here are displayed all the messages.
 *
 * @category Wall
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('Wall')));

echo $this->Html->script('jquery.scrollTo.min.js', array('block' => 'scriptBottom'));
echo $this->Html->script('wall.reply.js', array('block' => 'scriptBottom'));
echo $this->Html->script('wall.show_and_hide_replies.js', array('block' => 'scriptBottom'));

?>
<div id="annexe_content" >
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Tips'); ?></h2>
        <p>
        <?php
        echo format(
            __(
                'Before asking a question, '.
                'make sure to read the <a href="{}">FAQ</a>.', true
            ),
            $this->Url->build(array('controller' => 'pages', 'action' => 'faq'))
        );
        ?>
        </p>
        <p>
        <?php
        echo format(
            __(
                'We aim to maintain a healthy atmosphere for civilized discussions. '.
                'Please read our '.
                '<a href="{}">rules against bad behavior</a>.', true
            ),
            'https://en.wiki.tatoeba.org/articles/show/rules-against-bad-behavior'
        );
        ?>
        </p>
    </div>

    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Latest messages'); ?></h2>
        <ul class="latest-messages">
            <?php
            $mesg = count($tenLastMessages);

            foreach ($tenLastMessages as $currentMessage) {
                echo '<li>';
                // text of the link
                $text = format(__('{date}, by {author}'),
                               array(
                                   'date' => $this->Date->ago($currentMessage->date),
                                   'author' => $currentMessage->user->username
                               ));

                $path = array(
                    'controller' => 'wall',
                    'action' => 'index',
                    '#' => 'message_'.$currentMessage->id
                    );
                // link
                $isInitialPost = $currentMessage->parent_id == null;
                echo $this->Html->link($text, $path, array(
                    'escape' => false,
                    'class' => $isInitialPost ? 'initial-post' : null,
                ));
                echo '</li>';
            };
            ?>
        </ul>
    </div>

    <div class="wallBanner">
    <?php
    echo $this->Html->link(
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
    <div>
        <h2>
            <?php
            $threadsCount = $this->Paginator->param('count');
            echo format(__n('Wall (one thread)', 'Wall ({n}&nbsp;threads)', $threadsCount),
                        array('n' => $threadsCount));
            ?>
        </h2>

        <?php
        // leave a comment part
        if ($isAuthenticated) {
            echo $this->Wall->displayAddMessageToWallForm();

            echo '<div style="display:none">'."\n";
            echo $this->Wall->displayAddMessageToWallForm(true);
            echo '</div>'."\n";
        }
        ?>

        <?php
        $this->Pagination->display();
        ?>

        <div class="wall">
        <?php
        // display comment part
        foreach ($allMessages as $message) {
            $this->Wall->createThread(
                $message,
                $message->user,
                $message['Permissions'],
                $message->children
            );
        }
        ?>
        </div>

        <?php
        $this->Pagination->display();
        ?>
    </div>
</div>
