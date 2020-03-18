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

use Cake\Core\Configure;
use App\Model\CurrentUser;

$isDisplayingAnnouncement = false;

if (!CurrentUser::hasAcceptedNewTermsOfUse()) {
    $isDisplayingAnnouncement = true;
    $termsOfUseUrl = $this->Url->build([
        'controller' => 'pages', 
        'action' => 'terms_of_use'
    ]);
    $contactUrl = $this->Url->build([
        'controller' => 'pages', 
        'action' => 'contact'
    ]);
    echo $this->Form->create('Users', [
        'class' => 'announcement',
        'data-announcement-id' => 'new-terms-of-use',
        'url' => ['controller' => 'user', 'action' => 'accept_new_terms_of_use']
    ]);
    echo $this->Form->hidden('settings.new_terms_of_use', ['value' => true]);
    echo $this->Form->button($this->Images->svgIcon('close'), [
        'class' => 'close button'
    ]);
    echo $this->Html->div('terms-of-use-info', format(
        __('We have updated our <a href="{termsOfUse}">Terms of Use</a>.
        By closing this announcement, you agree with the new Terms of Use.
        If you have any question, feel free to <a href="{contact}">contact us</a>.'),
        ['termsOfUse' => $termsOfUseUrl, 'contact' => $contactUrl]
    ));
    echo $this->Form->end();
}


if (Configure::read('Announcement.enabled')) {
    $isDisplayingAnnouncement = true;
    $announcementId = 'coding-event-2020';
    $announcementText = $this->Html->tag('strong', __('Tatoeba coding event'));
    $announcementText .= $this->Html->tag('p', format(__(
        'In order to get more developers involved in Tatoeba and have some fun at the same time, we are organizing a coding event. '.
        'If this sounds interesting, please fill up <a href="{}">our survey</a>.'
    ), 'https://forms.gle/wyLqhcyLZxkiqn1WA'));
    $announcementText .= $this->Html->tag('p', format(__(
        'Until then, if you wish to get involved, please read our <a href="{}">guide for contributing as a developer</a> '.
        'or just <a href="{}">contact us</a>. We are an open source project and we welcome everyone!'
    ), 'https://github.com/Tatoeba/tatoeba2/wiki/Contributing-as-a-developer', $this->Url->build(['controller' => 'pages', 'action' => 'contact'])));

    $closeButton = $this->Html->div('close button', $this->Images->svgIcon('close'));
    $content = $this->Html->div('content', $announcementText);

    echo $this->Html->div(
        'announcement',
        $closeButton . $content,
        array(
            'data-announcement-id' => $announcementId
        )
    );
}

if (Configure::read('Tatoeba.devStylesheet')) {
    $isDisplayingAnnouncement = true;
    $content = __(
        'Warning: this website is for testing purposes. '.
        'Everything you submit will be definitely lost.', true
    );
    $closeButton = $this->Html->div('close button', $this->Images->svgIcon('close'));
    echo $this->Html->div(
        'announcement',
        $closeButton . $content,
        array(
            'data-announcement-id' => 'dev-warning5'
        )
    );
}

if ($isDisplayingAnnouncement) {
    $this->Html->script(JS_PATH . 'jquery.cookie.js', ['block' => 'scriptBottom']);
    $this->Html->script(JS_PATH . 'announcement.js',  ['block' => 'scriptBottom']);
}
?>
