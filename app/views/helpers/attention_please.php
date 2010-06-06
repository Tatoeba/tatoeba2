<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Helper to display important messages for which we need as many people as possible
 * to see it.
 *
 * @category General
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class AttentionPleaseHelper extends AppHelper
{
    public function tatoebaNeedsYou()
    {
        // Note: I don't want to create a CSS for this, it's going to stay only
        // a few days and it's very specific for Drumbeat.
        ?>
        <div class="module">
            <h2><?php __('Tatoeba needs you!'); ?></h2>
            <p>
            If you like what we are doing, then please take 2 minutes.
            </p>
            <a 
                href="https://www.drumbeat.org/project/tatoeba-project"
                style="display:block; background:#C36931; padding: 5px; color:#FFF">
            <img 
                style="float: left; padding: 0px 10px;"
                src="https://wiki.mozilla.org/images/7/7c/Drumbeat_logo_40x50.png" 
                alt="Drumbeat" width="40" height="50"
            /> 
            <span style="font-size: 1.5em;">Vote for us, on Drumbeat!</span>
            </a>
            <p>
            <strong>NOTE:</strong> You will need to 
            <a href="http://www.drumbeat.org/user/register" 
            style="font-weight: bold; color:#C36931;">register on Drumbeat</a> 
            before you can vote.
            </p>
        </div>
        <?php
    }
}
?>