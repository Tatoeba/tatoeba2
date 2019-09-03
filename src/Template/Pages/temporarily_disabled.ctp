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
 * This view is not used anywhere by default. It is meant to be used to temporarily
 * disable a page that causes the website to be slow, or a page where there is a bug
 * that is too dangerous to be left accessible for users.
 *
 * To disable a page, one has to add the following code in the corresponding
 * action method of a controller:
 *
 * ```
 * $this->render('/Pages/temporarily_disabled');
 * return;
 * ```
 */
?>
<div class="section md-whiteframe-1dp">
    <h2><?= __('Page temporarily disabled') ?></h2>

    <p>
        <?= __(
            'This page is currently disabled due to technical issues: '.
            'it may be slowing down the website or is dysfunctional.'
        ) ?>
    </p>

    <p><?= __('We are sorry for the inconvenience.') ?></p>
</div>
