<?php
/**
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009 Etienne Deparis <etienne.deparis@umaneti.net>

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

// TODO add an helper for this in order to have a cleaner code
 
if ($message['PrivateMessage']['title'] == '') {
    $messageTitle = __('[no subject]', true);
} else {
    $messageTitle = $message['PrivateMessage']['title'];
}
$this->pageTitle = __('Private messages', true) 
.' - ' 
. sprintf(
    __('%s from %s', true),
    $messageTitle, $message['Sender']['username']
);

echo $this->element('pmmenu');
?>
<div id="main_content">
    <div class="module">
    <h2><?php echo $messageTitle; ?></h2>

    <?php
    if ($message['PrivateMessage']['folder'] == 'Trash') {
        $delOrRestLink = $html->link(
            __('Restore', true), 
            array(
                'action' => 'restore', $message['PrivateMessage']['id']
            )
        );
    } else {
        $delOrRestLink = $html->link(
            __('Delete', true), 
            array(
                'action' => 'delete', 
                $message['PrivateMessage']['folder'], 
                $message['PrivateMessage']['id']
            )
        );
    }

    $replyLink = $html->link(
        __('Reply', true), 
        array(
            'action' => 'write',
            $message['Sender']['username'],
            $message['PrivateMessage']['id']
        )
    ); 
    
    $markAsUnread = $html->link(
        __('Mark as unread', true), 
        array(
            'action' => 'mark',
            'Inbox',
            $message['PrivateMessage']['id']
        )
    );
    
    echo $this->element(
        'pmtoolbox',
        array(
            'extralink' => '<li>' . $replyLink . '</li>'.
            '<li>' . $delOrRestLink . '</li>' .
            '<li>' . $markAsUnread . '</li>'
        )
    ); 
    
    $userName = $message['Sender']['username'];
    $userImage = $message['Sender']['image'];
    $messageDate = $message['PrivateMessage']['date'];
    
    // TODO Create a more general helper that can display comments on sentences, 
    // messages on Wall and private message, and use it to display this private 
    // message below
    ?>
    
    <div class="privateMessage">
    
        <ul class="meta" >
            <li class="image">
                <?php
                $wall->displayMessagePosterImage(
                    $userName,
                    $userImage
                )
                ?>
            </li>
            
            <li class="author">
                <?php $wall->displayLinkToUserProfile($userName); ?>
            </li>
            
            <li class="date">
                <?php echo $date->ago($messageDate); ?>
            </li>
        </ul>
        
        <div class="body">
        <?php
        $matches = array();
        $sentencesLists = array();
        $content = $message['PrivateMessage']['content'];
        if (preg_match_all("#\[list:(\d+)]#", $content, $matches) != false) {
            foreach ($matches[1] as $sl) {
                $sentencesLists[] = $this->requestAction(
                    '/sentences_lists/show/'.$sl.'/return'
                );
                $message['content'] = str_replace(
                    '[list:'.$sl.']', '', $message['content']
                );
            }
        }
        
        echo $clickableLinks->clickableURL(
            nl2br($content)
        );
        ?>
        </div>
        
        <?php
        foreach ($sentencesLists as $list) {
            echo '<h3>'.$list['SentencesList']['name'].'</h3>';
            
            if (count($list['Sentence']) > 0) {
                echo '<ul id="'.$list['SentencesList']['id'].'" class="sentencesList">';
                foreach ($list['Sentence'] as $sentence) {
                    echo '<li id="sentence'.$sentence['id'].'">';
                    // display sentence
                    if (isset($translationsLang)) {
                        $sentences->displaySentenceInList(
                            $sentence,
                            $translationsLang
                        );
                    } else {
                        $sentences->displaySentenceInList($sentence);
                    }
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                __('This list does not have any sentence');
            }
        }
        ?>
    </div>
    </div>
</div>
