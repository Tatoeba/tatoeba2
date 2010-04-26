<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  BEN YAALA Salem <salem.benyaala@gmail.com>
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
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * index view for User.
 *
 * @category User
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

// TODO /!\ use an helper to deobfuscate this /!\

// TODO : indent the code 
// TODO : stop using one letter class name , c? t?
// TODO : get <a> outside the __()
// TODO : use dedicated variable instead of calling the array several times

$this->pageTitle = 'Your profile';

if (!$session->read('Auth.User.id')) {
    echo $this->element('login');
} else {
    $currentUserId = $session->read('Auth.User.id');
    $userName = $user['User']['username'];
    // Include specific css and js files
    echo $javascript->link('profile.edit.js', false);
    ?>
    <div id="annexe_content">
        <div class="module">
            <h2><?php __('Your links'); ?></h2>
            <ul>
                <li>
                <?php
                echo sprintf(
                    __("<a href='%s'>View all my sentences</a>", true),
                    $html->url(
                        array(
                            "controller" => "sentences",
                            "action" => "my_sentences"
                        )
                    )
                );
                ?>
                </li>
                <li>
                <?php
                echo sprintf(
                    __("<a href='%s'>View my favorite sentences</a>", true),
                    $html->url(
                        array(
                            "controller" => "favorites",
                            "action" => "of_user",
                            $currentUserId
                        )
                    )
                );
                ?>
                </li>

                <li>
                <?php
                echo sprintf(
                    "<a href='%s'>".__("View my comments", true)."</a>",
                    $html->url(
                        array(
                            "controller" => "sentence_comments",
                            "action" => "of_user",
                            $userName
                        )
                    )
                );
                ?>
                </li>

                <li>
                <?php
                echo sprintf(
                    "<a href='%s'>".__("View comments on my sentences", true)."</a>",
                    $html->url(
                        array(
                            "controller" => "sentence_comments",
                            "action" => "on_sentences_of_user",
                            $userName
                        )
                    )
                );
                ?>
                </li>

                <li>
                <?php
                echo sprintf(
                    __("<a href='%s'>Sentences with undetected language</a>", true),
                    $html->url(
                        array(
                            "controller" => "sentences",
                            "action" => "unknown_language"
                        )
                    )
                );
                ?>
                </li>
            </ul>
        </div>
        <div class="module">
            <h2><?php __('Activity information'); ?></h2>
            <dl>
                <dt><?php __('Member since'); ?></dt>
                <dd><?php echo date('F j, Y', strtotime($user['User']['since'])); ?></dd>
                <dt><?php __('Last login'); ?></dt>
                <dd><?php echo date('F j, Y \\a\\t G:i', $user['User']['last_time_active']); ?></dd>
                <dt><?php __('Comments posted'); ?></dt>
                <dd><?php echo $userStats['numberOfComments']; ?></dd>
                <dt><?php __('Sentences owned'); ?></dt>
                <dd><?php echo $userStats['numberOfSentences']; ?></dd>
                <dt><?php __('Sentences favorited'); ?></dt>
                <dd><?php echo $userStats['numberOfFavorites']; ?></dd>
                <dt><?php __('Number of contributions'); ?></dt>
                <dd><?php echo $userStats['numberOfContributions']; ?></dd>
            </dl>
        </div>
    </div>
    <div id="main_content">
        <div class="module">
            <h2><?php echo $userName; ?></h2>
            <!-- TODO HACK SPOTTED style inside the html, use css file -->
            <div id="pimg" style="cursor:pointer;">
                <?php
                // TODO HACK spotted  ternary operator mix with associative
                // array of associative array inside a call function 
                // ultra combo 
                echo $html->image(
                    'profiles/' . (empty($user['User']['image']) ?
                        'tatoeba_user.png' :
                        $user['User']['image'] ),
                    array(
                        'alt' => $userName
                    )
                );
                ?>
            </div>
            <div id="pimg_edit" class="toolbox">
                <div class="t">
    <?php __('Change your profile image?'); ?>
                    <span class="x" title="<?php __('Close'); ?>">
    <?php echo $html->image('close.png', array('alt' => __('Close', true))); ?>
                    </span>
                </div>
                <div class="c">
    <?php
    echo $form->create(
        'profile_image',
        array(
            'url' => array(
                'controller' => 'user',
                'action' => 'save_image'
            ),
            'type' => 'file'
        )
    );
    echo $form->file('image');
    echo $form->end(__('Upload', true));
    ?>
                </div>
            </div>
        </div>
        <div id="pdescription_edit" class="toolbox">
            <div class="t">
    <?php __('Tell us something about you'); ?>
                <span class="x" title="<?php __('Close'); ?>">
    <?php echo $html->image('close.png', array('alt' => __('Close', true))); ?>
                </span>
            </div>
            <div class="c">
    <?php
    echo $form->create(
        'profile_description',
        array(
            'url' => array(
                'controller' => 'user',
                'action' => 'save_description'
            )
        )
    );
    echo $form->textarea(
        'description',
        array(
            'value' => $user['User']['description']
        )
    );
    echo $form->end(__('Save', true));
    ?>
            </div>
        </div>
        <div id="pdescription" class="module">
            <h2>
    <?php __('Something about you'); ?>
                <span id="pdescription_edit_link" class="edit_link">
    <?php echo $html->image('edit.png', array('alt' => __('Edit', true))); ?>
                </span>
            </h2>
            <div id="profile_description">
        <?php
        if (empty($user['User']['description'])) {
            __('Tell us something about you!');
        } else {
                echo nl2br($user['User']['description']);
        }
        ?>
            </div>
        </div>
        <div id="pbasic_edit" class="toolbox">
            <div class="t">
    <?php __('Complete some information?'); ?>
                <span class="x" title="<?php __('Close'); ?>">
    <?php echo $html->image('close.png', array('alt' => __('Close', true))); ?>
                </span>
            </div>
            <div class="c">
    <?php
    echo $form->create(
        'profile_basic',
        array(
            'url' => array(
                'controller' => 'user',
                'action' => 'save_basic'
            )
        )
    );
    echo $form->input(
        'name',
        array(
            'label' => 'Name',
            'value' => $userName
        )
    );

    $aBirthday = explode('-', substr($user['User']['birthday'], 0, 10));
    // 0 => YYYY
    // 1 => MM
    // 2 => DD

    $iTimestamp = mktime(0, 0, 0, $aBirthday[1], $aBirthday[2], $aBirthday[0]);

    echo $form->input(
        'birthday',
        array(
            'type' => 'date',
            'dateFormat' => 'MDY',
            'minYear' => date('Y') - 70,
            'maxYear' => date('Y') - 6,
            'label' => 'Birthday',
            'selected' => $iTimestamp
        )
    );
    echo '<label for="profile_basicCountry">' . __('Country', true) . '</label>' ;

        // what does this code ??
        if (!is_null($user['User']['country_id'])) {
            $temp = $user['Country']['id']; // find a better name for this variable
        }

    echo $form->select(
        'country',
        $countries,
        $temp, // TODO : magic stuff spotted
        null,
        false
    );
    echo $form->end(__('Edit', true));
    ?>
            </div>
        </div>
        <div id="pbasic" class="module">
            <h2>
    <?php __('Basic Information'); ?>
                <span id="pbasic_edit_link" class="edit_link">
    <?php echo $html->image('edit.png', array('alt' => __('Edit', true))); ?>
                </span>
            </h2>
            <dl>
                <dt><?php __('Name'); ?></dt>
                <dd>
    <?php
        if (empty($user['User']['name'])) {
            __('Tell us what is your real name to get to know you!');
        } else {
            echo $user['User']['name'];
        }
    ?>
                </dd>
                <dt><?php __('Birthday'); ?></dt>
                <dd>
    <?php
    echo (((integer) $aBirthday[0] == 0) ?
        __('You have not set your birthday yet!', true) :
        date('F j, Y', $iTimestamp));
    ?>
                </dd>
                <dt><?php __('Country'); ?></dt>
                <dd>
    <?php
    echo (is_null($user['User']['country_id']) ?
        __('Tells us where you come from!', true) :
        $user['Country']['name']);
    ?>
                </dd>
            </dl>
        </div>
        <div id="pcontact_edit" class="toolbox">
            <div class="t">
    <?php __('Complete some information?'); ?>
                <span class="x" title="<?php __('Close'); ?>">
    <?php echo $html->image('close.png', array('alt' => __('Close', true))); ?>
                </span>
            </div>
            <div class="c">
    <?php
    echo $form->create(
        'profile_contact', array(
            'url' => array(
                'controller' => 'user',
                'action' => 'save_contact'
            )
        )
    );
    echo $form->input(
        'email', array(
            'label' => 'E-mail',
            'value' => $user['User']['email']
        )
    );
    echo $form->input(
        'url', array(
            'label' => 'Homepage',
            'value'
            => (empty($user['User']['homepage']) ?
                'http://' : $user['User']['homepage'])
        )
    );
    echo $form->end(__('Edit', true));
    ?>
            </div>
        </div>
        <div id="pcontact" class="module">
            <h2>
    <?php __('Contact information'); ?>
                <span id="pcontact_edit_link" class="edit_link">
    <?php echo $html->image('edit.png', array('alt' => __('Edit', true))); ?>
                </span>
            </h2>
            <dl>
                <dt><?php __('E-mail'); ?></dt>
                <dd><?php echo $user['User']['email']; ?></dd>
                <dt><?php __('Homepage'); ?></dt>
                <dd>
    <?php
    echo empty($user['User']['homepage']) ?
        __('Maybe you have a blog to share?', true) :
        '<a href="' . $user['User']['homepage'] .
            '" title="' . $userName . '">' .
            $user['User']['homepage'] . '</a>';
    ?>
                </dd>
            </dl>
        </div>
        <div id="psettings" class="module">
            <h2><?php __('Settings'); ?></h2>
    <?php
    echo $form->create(
        'profile_setting', array(
            'url' => array(
                'controller' => 'user',
                'action' => 'save_settings'
            )
        )
    );
    ?>
            <div>
    <?php
    echo $form->checkbox(
        'send_notifications',
        (($user['User']['send_notifications']) ?
            array('checked' => 'checked') :
            array()
        )
    );
    ?>
                <label for="profile_settingSendNotifications">
    <?php __('Email notifications'); ?>
                </label>
            </div>
            <div>
    <?php
    echo $form->checkbox(
        'public_profile',
        (($user['User']['is_public']) ? array('checked' => 'checked') : array())
    );
    ?>
                <label for="profile_settingPublicProfile">
    <?php __('Set your profile public?'); ?>
                </label>
            </div>
    <?php echo $form->end(__('Save', true)); ?>
        </div>
        <div id="ppassword" class="module">
            <h2><?php __('Change password'); ?></h2>
    <?php
    echo $form->create(
        'User',
        array(
            'url' => array(
                'controller' => 'user',
                'action' => 'save_password'
            )
        )
    );
    echo $form->input(
        'old_password',
        array(
            "label" => __('Old password', true),
            "type" => "password"
        )
    );
    echo $form->input(
        'new_password',
        array(
            "label" => __('New password', true),
            "type" => "password"
        )
    );
    echo $form->input(
        'new_password2',
        array(
            "label" => __('New password again', true),
            "type" => "password"
        )
    );
    echo $form->end(__('Save', true));
    ?>
        </div>
    </div>
    <?php
}
    ?>
