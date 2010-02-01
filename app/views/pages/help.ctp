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

$this->pageTitle = 'Tatoeba : ' . __('Help', true);
?>

<div id="annexe_content">
    <div class="module">
        <h2><?php __('Table of contents'); ?></h2>
        <ul>
            <li><a href="#adding"><?php __('Adding new sentences'); ?></a></li>
            <li><a href="#translating"><?php __('Translating sentences'); ?></a></li>
            <li><a href="#correcting"><?php __('Correcting mistakes'); ?></a></li>
            <li><a href="#adopting"><?php __('Adopting sentences'); ?></a></li>
            <li><a href="#sentences_lists"><?php __('Sentences lists'); ?></a></li>
        </ul>
    </div>
    
    <div class="module">
        <h2><?php __('Need more help?'); ?></h2>
        <p><?php
           __(
               'If you cannot find the answer to your question, do not hesitate '.
               'to contact us.'
           );
           ?>
        </p>
        <p class="more_link">
            <?php
            echo $html->link(
                __('Contact us', true),
                array("controller"=>"pages", "action"=>"contact")
            );
            ?>
        </p>
    </div>
</div>

<div id="main_content">
    <a name="adding"></a>
    <div class="module">
        <h2><?php __('Adding new sentences'); ?></h2>
        <p>
            <?php
            __(
                'Ideally, for any word you may want to search, we would like you '.
                'to always get results. Indeed, one of the goala of the project '.
                'is to illustrate as many words and expressions as possible.'
            );
            ?>
        </p>
        <p>
            <?php
            __(
                'However, even if there is a large amount of sentences, there '.
                'is still a lot of vocabulary that is not covered. This is why '.
                'we encourage you to add new sentences with new vocabulary, even '.
                'if you do not know how to translate it into any language.'
            );
            ?>
        </p>
        <p>
            <?php 
            echo sprintf(
                __(
                    'Just <a href="%s">register</a> if you have not done so '.
                    'already, then log in and go to the <a href="%s">contribution '.
                    'section</a>.', true
                ),
                $html->url(array("controller"=>"users", "action"=>"register")),
                $html->url(array("controller"=>"pages", "action"=>"contribute"))
            );
            ?>
        </p>
    </div>
    
    <a name="translating"></a>
    <div class="module">
        <h2><?php __('Translating sentences'); ?></h2>
        <p>
            <?php
            __(
                'Translating is one of the most important tasks in Tatoeba, since '.
                'the main goal of the project is to gather sentences translated '.
                'into several languages.'
            );
            ?>
        </p>
        <p>
            <?php
            echo sprintf(
                __(
                    'You can translate a sentence from pretty much everywhere. '.
                    'Just click on this icon %s whenever you see it.', true
                ),
                $html->image('translate.png')
            );
            __('');
            ?>
        </p>
        <p>
            <?php 
            __('Note that translations are limited to registered users.'); 
            echo ' '; 
            echo $html->link(
                __('Click here to register.', true),
                array("controller"=>"users", "action"=>"register")
            ); 
            ?>
        </p>
    </div>
    
    <a name="correcting"></a>
    <div class="module">
        <h2><?php __('Correcting mistakes'); ?></h2>
        <p>
            <?php
            __(
                'Tatoeba is a project that is open to everyone, and we are aware '.
                'that people make mistakes.'
            );
            ?>
        </p>
        <p>
            <?php
            echo sprintf(
                __(
                    'It is not possible for you to directly correct mistakes in '.
                    'sentences that belong to other people because only the owner '.
                    'of the sentence can edit it. You can however post a comment '.
                    '(%s) on the sentence to notify the mistake. The owner will '.
                    'have to correct it himself or herself.', true
                ),
                $html->image('comments.png')
            );
            ?>
        </p>
        <p>
            <?php
            __(
                'In certain cases, the sentence do not have an owner. Read the '.
                'paragraph below (on adopting sentences) to learn more.'
            );
            ?>
        </p>
    </div>
    
    <a name="adopting"></a>
    <div class="module">
        <h2><?php __('Adopting sentences'); ?></h2>
        <p>
            <?php
            __(
                'When you add a sentence, this sentence "belongs" to you - only '.
                'you can edit it. However, most of the sentences in Tatoeba come '.
                'from a Japanese-English corpus called Tanaka Corpus. '.
                'These sentences do not have any owner because they have been '.
                'collected outside of Tatoeba.'
            );
            ?>
        </p>
        <p>
            <?php
            echo sprintf(
                __(
                    'If you see a mistake in an "orphan" sentence, you will '.
                    'not be able correct it because you are not the owner. '.
                    'This is why there is an "adopt" option (%s). Once you '.
                    'adopt a sentence, you become its owner and therefore can '.
                    'edit it.', true
                ),
                $html->image('adopt.png')
            );
            ?>
        </p>
        <p>
            <?php
            __(
                'Adopting a sentence is also part the "quality process". '.
                'You can find more information about it here:'
            );
            ?>
            <a href="http://blog.tatoeba.org/2009/01/new-validation-system.html">
                http://blog.tatoeba.org/2009/01/new-validation-system.html
            </a>
        </p>
    </div>
    
    <a name="sentences_lists"></a>
    <div class="module">
        <h2><?php __('Sentences lists'); ?></h2>
        <p>
            <?php
            __(
                'You can create lists of sentences in Tatoeba. By default the list '.
                'is <strong>private</strong>, which means it can only be edited by '.
                'the person who created it.'
            );
            ?>
        </p>
        <p>
            <?php
            __(
                'However it is also possible to let any member in Tatoeba add and '.
                'remove sentences by setting a list as <strong>public</strong>.'
            );
            ?>
        </p>
        <p>
            <?php
            __(
                'Someday we will also introduce <strong>group</strong> lists, '.
                'that only a restricted group of members can edit.'
            );
            ?>
        </p>
    </div>
    
    <div style="height:300px"></div>
</div>

