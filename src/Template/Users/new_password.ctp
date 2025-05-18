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
 * Page to get a new password.
 *
 * @category Users
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('Send new password')));

$this->Security->enableCSRFProtection();
?>

<div id="reset-form" class="md-whiteframe-1dp">
    <h2><?= __('Send new password'); ?></h2>
    <?= $this->Form->create('User', array(
            "ng-cloak" => true,
            "url" => array("action" => "new_password")
        ));
    ?>
    <md-input-container class="md-block">
        <?= $this->Form->input('email', [
                'label' => __('Email'),
            ]) ?>
     </md-input-container>

    <div layout="column">
        <md-button type="submit" class="md-raised md-primary">
            <?php /* @translators: button to send a new password by email
                     because the password was forgotten (verb) */ ?>
            <?php echo __('Send'); ?>
        </md-button>
    </div>
    <?= $this->Form->end(); ?>
</div>

<?php
$this->Security->disableCSRFProtection();
?>
