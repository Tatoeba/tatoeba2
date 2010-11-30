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

$this->pageTitle = 'Tatoeba - ' . sprintf(__("%s's Wall messages", true), $username);
?>

<div id="main_content">
<div class="module">
    <h2>
    <?php 
    echo $paginator->counter(
        array(
            'format' => sprintf(
                __("%s's messages on the Wall (total %s)", true),
                $username,
                '%count%'
            )
        )
    );
    ?>
    </h2>
    
    <?php
    $paginatorUrl = array($username);
    $pagination->display($paginatorUrl);
    ?>
    
    <ol class="wall">
    <?php
    foreach ($messages as $message) {
        $wall->createThread(
            $message['Wall'],
            $message['User'],
            null,
            null
        );
    }
    ?>
    </ol>
    
    <?php
    $paginatorUrl = array($username);
    $pagination->display($paginatorUrl);
    ?>
</div>
</div>