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
    echo $javascript->link(JS_PATH . 'jquery.cookie.js');
    echo $javascript->link(JS_PATH . 'announcement.js');

    $announcementId = 'announcement-id';
    $announcementText = 'Text of the announcement';

    $closeButton = $html->div('close', $images->svgIcon('close'));
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