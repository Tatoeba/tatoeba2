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
 * @link     http://tatoeba.org
 */

/**
 * @category Contributions
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$username = Sanitize::paranoid($username, array("_"));
if ($userExists) {
    $title = format(__("Logs of {user}'s contributions", true), array('user' => $username));
} else {
    $title = format(__("There's no user called {username}", true), array('username' => $username));
}
$this->set('title_for_layout', $pages->formatTitle($title));
?>
<div id="annexe_content">
    <?php
    if ($userExists) {
        echo $this->element(
            'users_menu', 
            array('username' => $username)
        );
    }
    ?>
</div>

<div id="main_content">
    <div class="section" md-whiteframe="1">
    <?php
    if (!$userExists) {
        $commonModules->displayNoSuchUser($username, $backLink);
    } else {
        echo $html->tag('h2', $title);
    
        if (isset($contributions)) {
            
            $pagination->display(array($username));
            ?>

            <div id="logs">
            <?php
            $user = array(
                'username' => $username
            );
            foreach ($contributions as $contribution) {
                echo $this->element('logs/log_entry', array('log' => $contribution));
            }
            ?>
            </div>

            <?php
            $pagination->display(array($username));
        }
    }
    ?>
    </div>
</div>
