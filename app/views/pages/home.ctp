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
    <?php
    if (!$session->read('Auth.User.id')) {
        ?>
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
            ?></p>
        </div>
    <?php
    }
    ?>
    
    <div class="module">
        <h2><?php __('Number of sentences'); ?></h2>
        <?php 
        echo $this->element('sentences_statistics');
        ?>
    </div>

    <?php
    // Warning message prompting the user to specify languages
    if ($session->read('Auth.User.id')) {

        // TODO  HACK SPOTTED , requestAction is hackish ! 
        $count_unknown_language = $this->requestAction(
            '/sentences/count_unknown_language'
        );
        if ($count_unknown_language > 0) {
            echo '<div class="module">';
            echo '<p class="warning">';
            __(
                'WARNING: The language of some the sentences you have added could '.
                'not be detected. '
            );
            echo '</p>';
            
            echo '<p class="more_link">';
            echo $html->link(
                __('Click here to set the languages', true),
                array("controller" => "sentences", "action" => "unknown_language")
            );
            echo '</p>';
            echo '</div>';
        }
        $javascript->link('sentences.add_translation.js', false);
    }
    ?>
        
    <div class="module">
        <h2><?php __('Latest messages'); ?></h2>
        <?php 
        foreach ($latestMessages as $value) {
            echo '<div class="lastWallMessages">';
                echo '<div class="header">';
                echo $date->ago($value['Wall']['date']);
                // Text of link
                $text = sprintf(
                    __(' by %s', true), 
                    $value['User']['username']
                );
                // Path of link
                $pathToUserProfile = array("controller"=>"user",
                        "action"=>"profile",
                        $value['User']['username']);
                // Link
                echo $html->link($text, $pathToUserProfile);
                echo '</div>';
                
                echo '<div class="body">';
                // Display only 200 first character of message
                echo substr($value['Wall']['content'], 0, 200);
                // Text of the Link
                if (strlen($value['Wall']['content']) > 200) {
                    echo ' [...]';
                }
                
                echo '</div>';
                
                echo '<div class="link">';
                // Path of the link
                $pathToWallMessage = array(
                            'controller' => 'wall',
                            'action' => 'index#message_'.$value['Wall']['id']
                        );
                // Link
                echo $html->link('>>>', $pathToWallMessage);
                echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
    
</div>

<div id="main_content">

    <div class="module">
        <?php echo $this->element('random_sentence'); ?>
    </div>

    <div class="module">
        <h2>
            <?php __('Latest contributions'); ?> 
            <span class="annexe">
                (
                    <?php
                    echo $html->link(
                        __('show more...', true),
                        array("controller"=>"contributions")
                    ); 
                    ?>
                ) (
                    <?php 
                    echo $html->link(
                        __('show activity timeline', true),
                        array(
                            "controller"=>"contributions",
                            "action"=>"activity_timeline"
                        )
                    );
                    ?>
                )
            </span>
        </h2>
            <?php echo $this->element('latest_contributions'); ?>
    </div>
    <div class="module">
        <h2>
            <?php __('Latest comments'); ?>
            <span class="annexe">
                (
                    <?php
                    echo $html->link(
                        __('show more...', true),
                        array("controller"=>"sentence_comments")
                    ); 
                    ?>
                )
            </span>
        </h2>
        <?php
        echo $this->element(
            'latest_sentence_comments',
            array(
                'sentenceComments' => $sentenceComments,
                'commentsPermissions' => $commentsPermissions
            )
        ); 
        ?>
    </div>
</div>

