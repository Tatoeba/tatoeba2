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

echo $this->element('pmmenu');
?>
<div id="main_content">
    <div class="module">

        <h2><?php echo __('New message', true); ?></h2>

     <?php
     if ($isNewUser && $canSend) {
         echo "<p>";
             __(
                 "To help keep Tatoeba free of spam and other malicious messages
                 new users can send only 5 messages per day."
             );
         echo "</p>";
         echo "<p>";
         echo sprintf(
             __(
                 "You have sent %s messages today. ", true
             ), $messagesToday
         );
         echo "</p>";
         echo "<br/>";
         $privateMessages->displayForm($recipients);
     } else if ($isNewUser) {
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
     } else {
         $privateMessages->displayForm($recipients);
     }
     ?>
    </div>
</div>
