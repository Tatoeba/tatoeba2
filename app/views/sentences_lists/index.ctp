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

$this->pageTitle = 'Tatoeba - ' . __('List of sentences', true);
?>

<div id="annexe_content" >

<?php
$attentionPlease->tatoebaNeedsYou();
?>

<?php
if ($session->read('Auth.User.id')) {
    if (isset($myLists) && count($myLists) > 0) {
        ?>
        <div class="module">
        <h2><?php __('Create a new list'); ?></h2>
        <?php
        echo $form->create('SentencesList');
        echo $form->input(
            'name',
            array(
                'type' => 'text'
            )
        );
        echo $form->end(__('create'));
        ?>
        </div>
        
    <?php 
    }
    ?>

        <div class="module">
        <h2><?php __('Still beta'); ?></h2>
        <p>
            <?php
            __(
                'This feature is still very basic. We will improve it as we '.
                'get more time and some feedback from users.'
            );
            ?>
        </p>
        <p class="more_link">
            <?php
            echo $html->link(
                __('Feedback', true),
                array("controller"=>"pages", "action"=>"contact")
            );
            ?>
        </p>
        </div>

<?php
} else {
    ?>
        <div class="module">
            <h2><?php __('About the lists'); ?></h2>
            <p>
                <?php
                __(
                    'Lists make it possible to gather and organize '.
                    'sentences in Tatoeba.'
                );
                ?>
            </p>
            <p>
                <?php
                __(
                    'You can create all kinds of lists! "Preparation for summer '.
                    'trip to Mexico", "English test #4", "My favorite geek '.
                    'quotes"...'
                );
                ?>
            </p>
        </div>

        <div class="module">
            <h2><?php __('Registration needed'); ?></h2>
            <p><?php __('You can create lists only if you are registered.'); ?></p>

            <p>
            <?php
            echo $html->link(
                __('Register', true),
                array("controller" => "users", "action" => "register"),
                array("class" => "registerButton")
            );
            ?>
            </p>

            <p><?php __('If you are already registered, please log in.'); ?></p>
        </div>
<?php
}
?>


</div>

<div id="main_content">

<?php
if (isset($myLists)) {

    // Lists of the user
    echo '<div class="module">';

        if (count($myLists) > 0) {
            echo '<h2>';
            echo __('My lists');
            echo '</h2>';

            $javascript->link(JS_PATH . 'sentences_lists.edit_name.js', false);
            $javascript->link(JS_PATH . 'jquery.jeditable.js', false);

            $lists->displayListTable($myLists);

        } else {
            echo '<h2>';
            __('Create a new list');
            echo '</h2>';
            echo $form->create('SentencesList');            
            echo $form->input(
                'name',
                array(
                    'type' => 'text'
                )
            );
            echo $form->end('Create');
        }
    echo '</div>';
}

// The public lists
if (count($publicLists) > 0) {
    echo '<div class="module">';
        echo '<h2>';
        echo __('Public lists');
        echo '</h2>';
        
        $lists->displayListTable($publicLists);
    
    echo '</div>';
}

// All the lists
if (count($otherLists) > 0) {
    echo '<div class="module">';
        echo '<h2>';
        echo __('All the other lists');
        echo '</h2>';
            
        $lists->displayListTable($otherLists);

    echo '</div>';
}
?>

</div>
