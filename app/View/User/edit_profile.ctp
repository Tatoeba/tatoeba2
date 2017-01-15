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
 * @link     http://tatoeba.org
 */

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
            'profile_image',
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
        if (!empty($this->request->data['User']['image'])) {
            $image = Sanitize::html($this->request->data['User']['image']);
        }
        echo $this->Html->image(
            IMG_PATH . 'profiles_128/'.$image
        );

        if (!empty($this->request->data['User']['image'])) {
            ?>
            <md-button type="submit" class="md-raised md-warn">
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
            'profile_image',
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
            <?php echo __('Upload'); ?>
        </md-button>
        <?php
        echo $this->Form->end();
        ?>
    </div>

    <?php 
    $dateOptions = array(
        'minYear' => date('Y') - 100,
        'maxYear' => date('Y') - 3,
        'type' => 'date',
        'selected' => $this->request->data['User']['birthday'],
        'empty' => true,
        'label' => __('Birthday')
    );
    $selectedCountryId = $this->request->data['User']['country_id'];

    echo $this->Form->create(
        false, 
        array(
            'action' => 'save_basic'
        )
    );
    
    echo $this->Form->input(
        'User.name',
        array(
            'label' => __p('user', 'Name'),
            'lang' => '',
            'dir' => 'auto',
        )
    );
    
    echo '<div class="input">';
    echo '<label for="UserCountryId">';
     __('Country');
    echo '</label>';
    echo $this->Form->select(
        'User.country_id', 
        $countries, 
        $selectedCountryId
    );
    echo '</div>';
    
    echo $this->Form->input(
        'User.birthday', 
        $dateOptions
    );
    
    echo $this->Form->input(
        'User.homepage',
        array(
            'label' => __('Homepage'),
            'lang' => '',
            'dir' => 'ltr',
        )
    );

    echo $this->Html->tag(
        'label',
        __('Description'),
        array('for' => 'UserDescription')
    );
    echo $this->Form->textarea(
        'User.description',
        array(
            'lang' => '',
            'dir' => 'auto',
        )
    );
    ?>
    <div layout="row" layout-align="end center" layout-padding>
        <md-button type="submit" class="md-raised md-primary">
            <?php echo __('Save'); ?>
        </md-button>
    </div>

    <?php
    echo $this->Form->end()
    ?>
    </div>
</div>
