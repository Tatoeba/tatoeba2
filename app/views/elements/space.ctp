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


<span id="profile" title="<?php __('Profile'); ?>">
<?php
$profileIcon = $html->image(
    'profile.png',
    array(
        "alt" => __('Profile', true),
        "width" => 14,
        "height" => 14
    )
);
echo $html->link(
    $profileIcon . $session->read('Auth.User.username'),
    array(
        'controller' => 'user',
        'action' => 'index'
    ),
    array(
        'escape' => false
    )
);
?>
</span>

<span id="inbox" title="<?php __('Inbox'); ?>">
<?php
$newMessages = $this->requestAction('/private_messages/check');
// TODO Get rid of requestAction
$mailIcon = $html->image(
    'mail.png',
    array(
        "alt" => __('Inbox', true),
        "width" => 14,
        "height" => 12
    )
);
echo $html->link(
    $mailIcon .' '. $newMessages,
    array(
        'controller' => 'private_messages', 
        'action' => 'folder', 
        'Inbox'
    ),
    array(
        'escape' => false
    )
);
?>
</span>

<span id="log_out">
<?php
$logOutIcon = $html->image(
    'log_out.png',
    array(
        "alt" => __('Log out', true),
        "title" => __('Log out', true),
        "width" => 14,
        "height" => 14
    )
);
echo $html->link(
    $logOutIcon,
    array(
        'controller' => 'users', 
        'action' => 'logout'
    ),
    array(
        'escape' => false
    )
);
?>
</span>