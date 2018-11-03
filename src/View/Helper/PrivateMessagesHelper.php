<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
namespace App\View\Helper;


/**
 * Helper to display things related to private messages.
 *
 * @category PrivateMessages
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class PrivateMessagesHelper extends AppHelper
{
    public $helpers = array('Form', 'Messages');

    /**
     * Displays the form to write a private message.
     *
     * @param string $recipient Recipient of the message.
     * @param string $title     Title of the message.
     * @param string $content   Content of the message.
     * @param int    $messageId Id of message if message is draft
     */
    public function displayForm(
        $recipients = null, $title = null, $content = null, $messageId = null
    ) {
        $headerTitle = $this->Messages->getHeaderTitle(
            $recipients,
            $messageId,
            $content,
            $title
        );

        echo $this->Form->create(
            'PrivateMessage',
            array(
                'url' => array('action' => 'send'),
                'class' => 'message form'
            )
        );
        ?>

        <div class="header">
            <div class="info">
            <?php
            $user = CurrentUser::get('User');
            $this->Messages->displayAvatar($user['User']);
            ?>
            </div>

            <div class="title">
            <?php echo $headerTitle ?>
            </div>
        </div>

        <div class="body">
            <div class="pmFields">
            <div ng-hide="true">
            <?php
            echo $this->Form->input('messageId', array('value' => $messageId));
            echo $this->Form->input('submitType', array('value' => ''));
            ?>
            </div>
            <?php
            echo $this->Form->input(
                'recpt',
                array(
                    'label' => __x('message', 'To'),
                    'default' => $recipients,
                    'type' => 'text',
                    'maxlength' => 250,
                    'class' => 'pmTo',
                    'lang' => '',
                    'dir' => 'ltr',
                )
            );

            echo $this->Form->input(
                'title',
                array(
                    'default' => $title,
                    'type' => 'text',
                    'label' => __('Title'),
                    'class' => 'pmTitle',
                    'lang' => '',
                    'dir' => 'auto',
                )
            );
            ?>
            </div>

            <div class="textarea">
            <?php
            if ($this->Messages->isReply($recipients, $messageId, $content)) {
                $content = $this->formatReplyMessage($content, $recipients);
            }
            echo $this->Form->input(
                'content',
                array(
                    'label' => '',
                    'default' => $content,
                    'type' => 'textarea',
                    'lang' => '',
                    'dir' => 'auto',
                )
            );
            ?>
            </div>
            <div layout="row" layout-align="end center" layout-padding>
                <md-button type="submit" name="data[PrivateMessage][submitType]" value="saveDraft" class="md-raised">
                    <?php echo __('Save as draft'); ?>
                </md-button>
                <md-button type="submit" name="data[PrivateMessage][submitType]" value="send" class="md-raised md-primary">
                    <?php echo __('Send'); ?>
                </md-button>
            </div>
        </div>

        <?php
        echo $this->Form->end();
    }


    /**
     * function to format the text of the messages in case of answer
     *
     * @param string $content The content of the message
     * @param string $sender  The author of the original message
     *
     * @return string
     */
    public function formatReplyMessage($content, $sender)
    {
        $messNextRegExp = preg_replace(
            "#\r?\n#iU", "\n> ",
            wordwrap($content, 60)
        );
        return "\n" . format(__('{sender} wrote:'), compact('sender')) . "\n> "
            . $messNextRegExp;
    }
}
?>
