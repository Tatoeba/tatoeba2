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

$this->pageTitle = __('Tatoeba : Collecting example sentences', true);
echo $javascript->link('sentences.statistics.js', false);
echo $javascript->link('sentences.show_another.js', false);

$key = isset($this->params['lang']) ? $this->params['lang'] : 'eng';

$lang = 'eng';
if (isset($this->params['lang'])) {
    Configure::write('Config.language', $this->params['lang']);
    $lang = $this->params['lang'];
}

$selectedLanguage = $session->read('random_lang_selected');


?>
<div id="annexe_content">
    <div class="module">
        <h2><?php __('Join the community!'); ?></h2>
        <?php
        __(
            "The more contributors there are, the more useful Tatoeba will ".
            "become! Besides, by contributing, not only you will be helpful ".
            "to the rest of the world, but you will also get to learn a lot."
        );
        ?>
        <p>
            <?php
            echo $html->link(
                __('Register', true),
                array("controller" => "users", "action" => "register"),
                array("class" => "registerButton")
            );
            ?>
        </p>
    </div>
    
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>
    
    <div class="module">
        <h2><?php __('Some numbers'); ?></h2>
        <p>
            <?php
            echo sprintf(
                __('%s contributions today', true),
                $nbrContributions
            );
            echo "<br />";
            echo sprintf(
                __('%s members so far', true),
                $nbrActiveMembers
            );
            echo "<br />";
            echo sprintf(
                __('%s supported languages', true),
                $languages->getNumberOfLanguages()
            );
            echo "<br />";
            echo $html->link(
                __('View more', true),
                array(
                    "controller" => "pages",
                    "action" => "display",
                    "home"
                )
            );
            ?>
        </p>
    </div>
    
    <div class="module">
        <h2><?php __('Number of sentences'); ?></h2>
        <?php 
        echo $this->element('sentences_statistics');
        ?>
    </div>
</div>

<div id="main_content">
    <div class="main_module">
        <h2><?php __('What is Tatoeba?'); ?></h2>
        <p>
            <?php
            __(
                'At its core, Tatoeba is a large database of <strong>example '.
                'sentences</strong> translated into several languages. '.
                'But as a whole, it is much more than that.'
            );
            // TODO : write something in the "About
            // echo ' ' . $html->link(__('Learn more...',true), 
            //     array('controller' => 'pages', 'action' => 'about'));
            ?>
        </p>
    </div>

    <div class="module">
        <h2><?php __('What can I do in Tatoeba?'); ?></h2>
        <div class="keyIdea">
            <?php 
            __(
                '<span class="keyword">Learn</span> '.
                '<span class="sub-keyword">languages</span>'
            ); 
            ?>
            <ul>
                <li>
                    <?php
                    echo $html->link(
                        __('Search sentences', true), 
                        array("controller"=>"pages", "action"=>"search")
                    );
                    ?>
                </li>
                <li>
                    <?php
                    echo $html->link(
                        __('Create lists', true),
                        array("controller"=>"sentences_lists")
                    );
                ?>
                </li>
            </ul>
        </div>

        <div class="keyIdea">
            <?php 
            __(
                '<span class="keyword">Share</span> '.
                '<span class="sub-keyword">your knowledge</span>'
            ); 
            ?>
            <ul>
                <li>
                    <?php
                    echo $html->link(
                        __('Translate sentences', true),
                        array(
                            "controller"=>"pages",
                            "action"=>"help#translating"
                        )
                    );
                    ?>
                </li>
                <li>
                    <?php
                    echo $html->link(
                        __('Correct the mistakes', true),
                        array(
                            "controller"=>"pages",
                            "action"=>"help#correcting"
                        )
                    );
                    ?>
                </li>
            </ul>
        </div>

        <div class="keyIdea">
            <?php 
            __(
                '<span class="keyword">Interact</span> '.
                '<span class="sub-keyword">with the community</span>'
            ); 
            ?>
            <ul>
                <li>
                    <?php
                    echo $html->link(
                        __('Post comments', true),
                        array("controller"=>"sentence_comments")
                    );
                    ?>
                </li>
                <li>
                    <?php
                    echo $html->link(
                        __('Contact other members', true),
                        array(
                            "controller"=>"users",
                            "action"=>"all"
                        )
                    );
                    ?>
                </li>
            </ul>
        </div>
        </div>

    <div class="module">
        <?php echo $this->element('random_sentence'); ?>
    </div>
</div>

