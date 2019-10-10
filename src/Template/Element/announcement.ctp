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

if (!CurrentUser::hasAcceptedNewTermsOfUse()) {
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

/*
if (Configure::read('Announcement.enabled')) {
    $announcementId = 'announcement-123';
    $announcementText = 'Announcement text here.';

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
*/

if (Configure::read('Tatoeba.devStylesheet')) {
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

?>