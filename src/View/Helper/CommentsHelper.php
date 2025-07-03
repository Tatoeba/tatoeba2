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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\Model\CurrentUser;
use App\View\Helper\AppHelper;


/**
 * Helper for contributions.
 *
 * @category SentenceComments
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class CommentsHelper extends AppHelper
{

    public $helpers = array('Form', 'Html', 'Sentences', 'Messages', 'Url');


    /**
     * Display sentence (for edit sentence_comment view)
     *
     * @param $sentence       Sentence to display.
     *
     * @return void
     */
    public function displaySentence($sentence)
    {
        if ($sentence) {
            $sentence->translations = [];
            $this->Sentences->displaySimpleSentencesGroup($sentence);
        } else {
            echo '<em>'.__('sentence deleted').'</em>';
        }
    }

    /**
     *
     *
     *
     */
    public function getMenuForComment($comment, $permissions, $replyIcon)
    {
        $menu = array();
        $commentId = $comment['id'];

        //send message
        if ($permissions['canReport']) {
            $menu[] = array(
                /* @translators: flag button to report a sentence comment (verb) */
                'text' => __('Report'),
                'icon' => 'flag',
                'url' => array(
                    'controller' => 'report_content',
                    'action' => 'sentence_comment',
                    $comment->id,
                    '?' => ['origin' => $this->getView()->getRequest()->getRequestTarget()],
                )
            );
        }
        if ($permissions['canPM']) {
            $menu[] = array(
                'text' => __('Send message'),
                'icon' => 'mail',
                'url' => array(
                    'controller' => 'private_messages',
                    'action' => 'write',
                    $comment->user->username
                )
            );
        }

        // hide
        if ($permissions['canHide']) {
            $hidden = $comment['hidden'];

            if ($hidden) {
                /* @translators: button to unhide a sentence comment (verb) */
                $hiddenLinkText = __('Unhide');
                $hiddenLinkAction = 'unhide_message';
            } else {
                /* @translators: button to hide a sentence comment (verb) */
                $hiddenLinkText = __('Hide');
                $hiddenLinkAction = 'hide_message';
            }

            $menu[] = array(
                'text' => $hiddenLinkText,
                'icon' => 'visibility_off',
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => $hiddenLinkAction,
                    $commentId
                )
            );
        }

        // edit
        if ($permissions['canEdit']) {
            $menu[] = array(
                /* @translators: edit button on sentence comment (verb) */
                'text' => __('Edit'),
                'icon' => 'edit',
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => "edit",
                    $commentId
                )
            );
        }

        // delete
        if ($permissions['canDelete']) {
            $menu[] = array(
                /* @translators: delete button on sentence comment (verb) */
                'text' => __('Delete'),
                'icon' => 'delete',
                'url' => array(
                    "controller" => "sentence_comments",
                    "action" => "delete_comment",
                    $commentId
                ),
                'confirm' => __('Are you sure?')
            );
        }

        // view
        $sentenceId = $comment['sentence_id'];
        $viewIcon = $replyIcon ? 'reply' : 'link';
        $menu[] = array(
            'text' => '#',
            'icon' => $viewIcon,
            'url' => array(
                "controller" => "sentences",
                "action" => "show",
                '#' => 'comment-'.$commentId,
                $sentenceId
            )
        );

        return $menu;
    }

}
?>
