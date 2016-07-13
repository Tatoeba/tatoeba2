<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
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

$this->set('title_for_layout', __('New message', true) . __(' - Tatoeba', true));

echo $this->element('pmmenu');
?>
<div id="main_content">
    <div class="module">
     <?php
     if ($isNewUser && !$canSend) {
         echo "<p>";
             __("To help keep Tatoeba free of spam and other malicious messages new users can send only 5 messages per day."
             );
         echo "</p>";
         echo "<p>";
             __(
                 "Please wait until you can send more messages. "
             );
             __(
                 "If you have received this message in error, ".
                 "please contact administrators at ".
                 "team@tatoeba.org."
             );
         echo "</p>";
     } else if ($isNewUser) {
         echo "<p>";
             __(
                 "To help keep Tatoeba free of spam and other malicious messages
                 new users can send only 5 messages per day."
             );
         echo "</p>";
         echo "<p>";
         echo format(
             __n(
                 'You have sent one message today.',
                 'You have sent {n}&nbsp;messages today.',
                 $messagesToday, true
             ),
             array('n' => $messagesToday)
         );
         echo "</p>";
         echo "<br/>";
     }

     if ($canSend) {
        if (isset($messageId)) {
            $privateMessages->displayForm($recipients, $title, $content, $messageId);
        } else if (isset($hasRecoveredMessage)) {
            $privateMessages->displayForm($recipients, $title, $content);
        } else {
            $privateMessages->displayForm($recipients);
        }
     }
     ?>
    </div>
</div>
