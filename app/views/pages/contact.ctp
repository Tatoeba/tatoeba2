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
 * Page to display contact information.
 *
 * @category Pages
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */ 
 
$this->pageTitle = 'Tatoeba - ' . __('Contact us', true);
?>
<div id="annexe_content">
    <div class="module">
    <h2><?php __('FAQ'); ?></h2>
    <p>
    <?php
    $faqUrl = $html->url(array('action' => 'faq'));
    echo sprintf(
        __(
            'Please make sure to <a href="%s">read the FAQ</a> '.
            'before asking a question.', true
        ), $faqUrl
    );
    ?>
    </p>
    </div>
    
    <div class="module">
        <h2><?php __('Follow us'); ?></h2>
        <p id="socialMedia">
            <a class="twitterLink" href="http://twitter.com/tatoeba_org"><?php __('Twitter'); ?></a>
            <a class="bloggerLink" href="http://blog.tatoeba.org"><?php __('Tatoeba Blog'); ?></a>
            <a class="facebookLink" href="http://www.facebook.com/group.php?gid=129340017083187"><?php __('Facebook'); ?></a>
        </p>
    </div>
</div>
    
<div id="main_content">
    <div class="module">
        <h2><?php __('Contact us'); ?></h2>
        <?php
        $email = 'team@tatoeba.org';
        echo sprintf(
            __(
                'If you have any question, suggestion, or request (or if you would '.
                'just like to say thank you), feel free to drop us an email at %s.',
                true
            ), $email
        );
        ?>
    </div>
    
    <div class="module">
        <h2><?php __('Post on the Wall'); ?></h2>
        <?php
        echo sprintf(
            __(
                'You can also tell us what you think by posting on the '.
                '<a href="%s">Wall</a>.', true
            ),
            $html->url(array("controller"=>"wall"))
        );
        ?>
    </div>
    
    
    <div class="module">
        <h2><?php __('Join us on IRC'); ?></h2>
        <?php
        echo sprintf(
            __(
                'We also have an IRC channel on freenode, #tatoeba. If you are not '.
                'familiar with IRC, you can read the <a href="%s">Help</a>.', true
            ),
            $html->url(
                array(
                    "controller" => "pages",
                    "action" => "help"
                )
            )
        );
        ?>
    </div>
</div>