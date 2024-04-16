<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
use App\Model\CurrentUser;

$this->set('title_for_layout', $this->Pages->formatTitle(__('Edit profile')));
$countries = $this->Countries->getAllCountries();
$this->Languages->localizedAsort($countries);
?>
<div id="annexe_content">
    <?php
    echo $this->element(
        'users_menu',
        array('username' => CurrentUser::get('username'))
    );
    ?>
</div>

<div id="main_content">
    <div class="module form">
    <h2><?php echo __('Edit profile'); ?></h2>
    <div class="currentPicture">
        <?php
        echo $this->Form->create(
            null,
            array(
                'url' => array(
                    'controller' => 'user',
                    'action' => 'remove_image'
                )
            )
        );
        ?>
        <div class="title"><?php echo __('Current picture'); ?></div>
        <?php
        $image = 'unknown-avatar.png';
        if (!empty($user->image)) {
            $image = h($user->image);
        }
        echo $this->Html->image(
            IMG_PATH . 'profiles_128/'.$image
        );

        if (!empty($user->image)) {
            ?>
            <md-button type="submit" class="md-raised md-warn">
                <?php /* @translators: button to remove profile picture */ ?>
                <?php echo __('Remove'); ?>
            </md-button>
            <?php
        }

        echo $this->Form->end();
        ?>
    </div>

    <div class="newPicture">
        <div class="title"><?php echo __('New picture'); ?></div>
        <?php
        echo $this->Form->create(
            null,
            array(
                'url' => array(
                    'controller' => 'user',
                    'action' => 'save_image'
                ),
                'type' => 'file'
            )
        );
        echo $this->Form->file('image');
        ?>
        <md-button type="submit" class="md-raised md-primary">
                <?php /* @translators: button to upload a new profile picture */ ?>
            <?php echo __('Upload'); ?>
        </md-button>
        <?php
        echo $this->Form->end();
        ?>
    </div>

    <?php
    $user->name = $this->safeForAngular($user->name);
    $user->homepage = $this->safeForAngular($user->homepage);
    $user->description = $this->safeForAngular($user->description);
    echo $this->Form->create($user, [
        'id' => 'profile-form',
        'url' => ['controller' => 'user', 'action' => 'save_basic']
    ]);

    echo $this->Form->control('name', [
        /* @translators: label for user name in profile page */
        'label' => __x('user', 'Name'),
        'lang' => '',
        'dir' => 'auto'
    ]);

    echo $this->Form->control('country_id', [
        /* @translators: label for user's country in profile page */
        'label' => __('Country'),
        'options' => $countries,
        'empty' => true
    ]);

    
    $birthday = explode('-', $user->birthday);
    $year = !isset($birthday[0]) || $birthday[0] == '0000' ? '' : $birthday[0];
    $month = !isset($birthday[1]) || $birthday[1] == '00' ? '' : $birthday[1];
    $day = !isset($birthday[2]) || $birthday[2] == '00' ? '' : $birthday[2];
    /* @translators: label for user's birthday in profile page */
    echo $this->Form->label('birthday', __('Birthday'));
    echo $this->Form->year('birthday', [
        'empty' => true,
        'value' => $year,
        'minYear' => date('Y') - 100,
        'maxYear' => date('Y') - 3
    ]);
    echo $this->Form->select('birthday[month]', $this->Date->months(), ['empty' => true, 'value' => $month]);
    echo $this->Form->day('birthday', [
        'empty' => true,
        'value' => $day
    ]);

    echo $this->Form->control('homepage', [
        /* @translators: label for user's homepage in profile page */
        'label' => __('Homepage'),
        'lang' => '',
        'dir' => 'ltr'
    ]);

    /* @translators: label for user's description in profile page */
    echo $this->Form->label('description', __('Description'));
    echo $this->Form->textarea('description', [
        'lang' => '',
        'dir' => 'auto',
    ]);
    ?>
    <div layout="row" layout-align="end center" layout-padding>
        <md-button type="submit" class="md-raised md-primary">
            <?php /* @translators: submit button of profile edition form (verb) */ ?>
            <?php echo __('Save'); ?>
        </md-button>
    </div>

    <?php
    echo $this->Form->end()
    ?>
    </div>
</div>
