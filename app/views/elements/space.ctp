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
 * @link     http://tatoeba.org
 */

$lang = 'eng';
if (isset($this->params['lang'])) {
    Configure::write('Config.language', $this->params['lang']);
    $lang = $this->params['lang'];
}
?>
<strong title="<?php echo __('My profile', false); ?>">
    <?php
    echo $html->link(
        $session->read('Auth.User.username'),
        array('controller' => 'user', 'action' => 'index'),
        array('class' => 'menuItem')
    );
    ?> :
</strong>

<?php
$newMessages = $this->requestAction('/private_messages/check');
if ($newMessages > 0) {
    $inboxLink = '<strong>'.$html->link(
        __('Inbox', true) . ' ('. $newMessages . ')',
        array('controller' => 'private_messages', 'action' => 'folder', 'Inbox'),
        array('class' => 'menuItem')
    ). '</strong>';
} else {
    $inboxLink = $html->link(
        __('Inbox', true),
        array('controller' => 'private_messages', 'action' => 'folder', 'Inbox'),
        array('class' => 'menuItem')
    );
}
?>


<ul>
    <li><?php echo $inboxLink; ?></li>
    <li><?php
        echo $html->link(
            __('Log out', true),
            array('controller' => 'users', 'action' => 'logout'),
            array('class' => 'menuItem')
        );
        ?>
    </li>
</ul>
