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
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use App\Model\CurrentUser;

/**
 * Helper to display things related to private messages.
 *
 * @category PrivateMessages
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class PrivateMessagesHelper extends AppHelper
{
    public $helpers = array('Form', 'Messages');

    /**
     * Displays the form to write a private message.
     *
     * @param string $recipients List of usernames separated by a comma.
     * @param Entity $pm         PrivateMessage entity.
     */
    public function displayForm($pm, $recipients) {
        $headerTitle = $this->Messages->getHeaderTitle(
            $recipients,
            $pm->id,
            $pm->content,
            $pm->title
        );
        $isReply = $this->Messages->isReply($recipients, $pm->id, $pm->content);

        echo $this->Form->create($pm, [
            'id' => 'private-message-form',
            'url' => ['action' => 'send'],
            'class' => 'message form'
        ]);
        ?>

        <div class="header">
            <div class="info">
            <?php
            $user = CurrentUser::get('User');
            $this->Messages->displayAvatar($user);
            ?>
            </div>

            <div class="title">
            <?php echo $headerTitle ?>
            </div>
        </div>

        <div class="body">
            <div class="pmFields">
            <?php
            if (!$isReply) {
                echo $this->Form->hidden('messageId', array('value' => $pm->id));
            }
            echo $this->Form->hidden('submitType', array('value' => ''));
            $this->Form->unlockField('submitType');
            ?>
            <?php
            echo $this->Form->control('recipients', [
                'label' => __x('message', 'To'),
                'default' => $recipients,
                'maxlength' => 250,
                'class' => 'pmTo',
                'lang' => '',
                'dir' => 'ltr',
            ]);

            echo $this->Form->control('title', [
                'label' => __('Title'),
                'class' => 'pmTitle',
                'lang' => '',
                'dir' => 'auto',
            ]);
            ?>
            </div>

            <div class="textarea">
            <?php
            $content = $pm->content;
            if ($isReply) {
                $content = $this->formatReplyMessage($pm->content, $recipients);
            }
            echo $this->Form->textarea('content', [
                'lang' => '',
                'dir' => 'auto',
                'value' => $content
            ]);
            ?>
            </div>
            <div ng-cloak layout="row" layout-align="end center" layout-padding>
                <md-button type="submit" name="submitType" value="saveDraft" class="md-raised">
                    <?php echo __('Save as draft'); ?>
                </md-button>
                <md-button type="submit" name="submitType" value="send" class="md-raised md-primary">
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
