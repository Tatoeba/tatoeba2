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
$username = h($username);
$this->set('title_for_layout', $this->Pages->formatTitle(
    format(__("{user}'s Wall messages"), array('user' => $username))
));
?>

<div id="annexe_content">
    <?php
        echo $this->element(
        'users_menu', 
        array('username' => $username)
    );
    ?>
</div>


<div id="main_content">
<div class="section">
    <h2>
    <?php 
    echo $this->Paginator->counter(
        array(
            'format' => format(
                __('{user}\'s messages on the Wall (total&nbsp;{n})'),
                array('user' => $username, 'n' => '{{count}}')
            )
        )
    );
    ?>
    </h2>
    
    <?php
    $this->Pagination->display();
    ?>
    
    <div class="wall">
    <?php
    foreach ($messages as $message) {
        $this->Wall->createThread(
            $message,
            $message->user,
            null,
            null
        );
    }
    ?>
    </div>
    
    <?php
    $this->Pagination->display();
    ?>
</div>
</div>
