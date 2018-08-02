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
 
$this->set('title_for_layout', $this->Pages->formatTitle(__('Contact us')));
?>
<div id="annexe_content">
    <div class="module">
    <h2><?php echo __('FAQ'); ?></h2>
    <p>
    <?php
    $faqUrl = $this->Html->url(array('action' => 'faq'));
    echo format(
        __(
            'Please make sure to <a href="{}">read the FAQ</a> '.
            'before asking a question.', true
        ), $faqUrl
    );
    ?>
    </p>
    </div>
    
    <div class="module">
        <h2><?php echo __('Follow us'); ?></h2>
        <p id="socialMedia">
            <a class="twitterLink" href="http://twitter.com/tatoeba_org"><?php echo __('Twitter'); ?></a>
            <a class="bloggerLink" href="http://blog.tatoeba.org"><?php echo __('Tatoeba Blog'); ?></a>
            <a class="facebookLink" href="http://www.facebook.com/group.php?gid=129340017083187"><?php echo __('Facebook'); ?></a>
        </p>
    </div>
</div>
    
<div id="main_content">
    <div class="module">
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
    
    <div class="module">
        <h2><?php echo __('Post on the Wall'); ?></h2>
        <?php
        echo format(
            __(
                'You can also tell us what you think by posting on the '.
                '<a href="{}">Wall</a>.', true
            ),
            $this->Html->url(array("controller"=>"wall"))
        );
        ?>
    </div>
    
    
    <div class="module">
        <h2><?php echo __('Join us on XMPP'); ?></h2>
        <p>
        <?php
        echo format(
            __(
                'We also have an XMPP room, <a href="{room}">'.
                'tatoeba@chat.tatoeba.org</a>, also available with the web '.
                'client below.  If you are not familiar with XMPP, you '.
                'can read the <a href="{help}">Help</a>.', true
            ),
            array(
                'room' => "xmpp:tatoeba@chat.tatoeba.org?join"
                'help' => $this->Html->url(
                    array(
                        "controller" => "pages",
                        "action" => "help"
                    )
                )
            )
        );
        ?>
        </p>
        <iframe src="https://candy.linkmauve.fr/tatoeba@chat.tatoeba.org" width="100%" height="600px">
            <p>
            <?php
            __('Sorry, your browser doesn’t support iframes.')
            ?>
            </p>
        </iframe>
    </div>
</div>
