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

if (Configure::read('Announcement.enabled')) {
    $announcementId = 'looking-for-desingers';
    $announcementText = '<p>Tatoeba needs help to re-design the website to be 
        mobile-friendly! If you have experience in UI/UX design, please contact 
        Trang at trang@tatoeba.org.</p>

        <p>We are also continuously looking for developers. If you are interested to 
        contribute to a non-profit open source project, please read our guide on how 
        to <a href="https://github.com/Tatoeba/tatoeba2/wiki/Joining-the-dev-team">
        join the dev team</a>.</p>

        <p>Thank you!</p>';

    $closeButton = $html->div('close button', $images->svgIcon('close'));
    $content = $html->div('content', $announcementText);

    echo $html->div(
        'announcement',
        $closeButton . $content,
        array(
            'data-announcement-id' => $announcementId
        )
    );
}
?>