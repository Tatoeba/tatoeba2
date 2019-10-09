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

$username = h($username);
if ($userExists) {
    $title = format(__("Latest contributions of {user}"), array('user' => $username));
} else {
    $title = format(__("There's no user called {username}"), array('username' => $username));
}
$this->set('title_for_layout', $this->Pages->formatTitle($title));
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
    <div class="section md-whiteframe-1dp">
    <?php
    if (!$userExists) {
        $this->CommonModules->displayNoSuchUser($username);
    } else {
        echo $this->Html->tag('h2', $title);
    
        if (isset($contributions)) {
            echo $this->Html->tag('p', format(
                __('Only the last {n} log entries are displayed here.'),
                ['n' => $this->Number->format($totalLimit)]
            ));

            $this->Pagination->display(['last' => false]);
            ?>

            <md-list id="logs">
            <?php
            $user = array(
                'username' => $username
            );
            foreach ($contributions as $contribution) {
                echo $this->element('logs/log_entry', array('log' => $contribution));
            }
            ?>
            </md-list>

            <?php
            $this->Pagination->display(['last' => false]);
        }
    }
    ?>
    </div>
</div>
