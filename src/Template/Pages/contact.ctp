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
 * @link     https://tatoeba.org
 */

/**
 * Page to display contact information.
 *
 * @category Pages
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */ 
 
$this->set('title_for_layout', $this->Pages->formatTitle(__('Contact us')));
?>
<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
        <?php /* @translators: section title in the Contact page */ ?>
        <h2><?php echo __x('header', 'FAQ'); ?></h2>
        <p>
        <?php
        $faqUrl = $this->Url->build(array('action' => 'faq'));
        echo format(
            __(
                'Please make sure to <a href="{}">read the FAQ</a> '.
                'before asking a question.', true
            ), $faqUrl
        );
        ?>
        </p>
    </div>
    
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Follow us'); ?></h2>
        <p id="socialMedia">
            <a class="twitterLink" href="http://twitter.com/tatoeba_org"><?php echo __('Twitter'); ?></a>
            <a class="bloggerLink" href="http://blog.tatoeba.org"><?php echo __('Tatoeba Blog'); ?></a>
            <a class="facebookLink" href="http://www.facebook.com/group.php?gid=129340017083187"><?php echo __('Facebook'); ?></a>
        </p>
    </div>
</div>
    
<div id="main_content">
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Contact us'); ?></h2>
        <?php
        $email = 'team@tatoeba.org';
        echo format(
            __(
                'If you have any question, suggestion, or request (or if you would '.
                'just like to say thank you), feel free to drop us an email at {emailAddress}.',
                true
            ), array('emailAddress' => $email)
        );
        ?>
    </div>
    
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Post on the Wall'); ?></h2>
        <?php
        echo format(
            __(
                'You can also tell us what you think by posting on the '.
                '<a href="{}">Wall</a>.', true
            ),
            $this->Url->build(array("controller"=>"wall"))
        );
        ?>
    </div>
    
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Join our chatroom'); ?></h2>
        <p>
        <?php
        echo format(
            __(
                'We have an <strong>XMPP room</strong>: <a href="{room}">'.
                'tatoeba@chat.tatoeba.org</a>.  You can join with your '.
                'favorite XMPP client or <a href="{webclient}">from your '.
                'browser</a>.', true
            ),
            array(
                'room' => "xmpp:tatoeba@chat.tatoeba.org?join",
                'webclient' => "https://chat.tatoeba.org"
            )
        );
        ?>
        </p>
    </div>
</div>
