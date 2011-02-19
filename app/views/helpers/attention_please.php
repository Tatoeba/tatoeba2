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
        ?>
        <div class="module">
        <h2>Tatoeba day #3</h2>
        <div style="border: 1px solid #CCC; padding: 5px 10px; background: #F1F1F1">
            <strong>It's right now!</strong> Join us on <a href="http://webchat.freenode.net/">IRC</a> :)<br/>
            <ul>
                <li>=&gt; channel #tatoeba</li>
            </ul>
        </div>
        </div>
        <?php
    }
}
?>