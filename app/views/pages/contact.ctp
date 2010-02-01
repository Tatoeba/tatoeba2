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
?>

<div id="main_content">
    <div class="module">
        <?php
        echo '<h2>';
        __('Contact Trang');
        echo '</h2>';
        
        $email = 'trang.dictionary.project@gmail.com';
        echo sprintf(
            __(
                'If you would like to contact the author of this project, '.
                'feel free to drop an email at %s.', true
            ), $email
        );
        ?>
    </div>
    
    <div class="module">
        <?php
        echo '<h2>';
        __('Post on the wall');
        echo '</h2>';

        echo sprintf(
            __(
                'You can also tell us what you think by posting on the '.
                '<a href="%s">Wall</a>.', true
            ),
            $html->url(array("controller"=>"wall"))
        );
        ?>
    </div>
</div>
