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
 
$query = Sanitize::html($query);
?>

<div class="module">
    <h2>
    <?php echo sprintf(__('Add a sentence containing %s', true), $query); ?>
    </h2>

    <p>
    <?php
    __(
        'There is no result for this search (yet) but you '.
        'can help us by feeding the corpus with new vocabulary!'
    );
    ?>
    </p>

    <?php
    if ($session->read('Auth.User.id')) {
        ?>
        <p>
        <?php
        echo sprintf(
            __(
                'Feel free to <a href="%s">submit a sentence</a> '.
                'with the words you were searching.', true
            ),
            $html->url(array('controller' => 'sentences', 'action' => 'add'))
        );
        ?>
        </p>
        <?php
    } else {

        __('If you are interested, please register.');
        
        echo $html->link(
            'register',
            array("controller" => "users", "action" => "register"),
            array("class"=>"registerButton")
        );
        
    }
    ?>
</div>