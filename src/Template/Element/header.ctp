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
?>

<div layout="row" layout-align="start center" flex-xs flex-sm layout-padding>
    <md-button hide-gt-sm class="hamburger-menu md-icon-button" ng-click="toggleMenu()">
        <md-icon>menu</md-icon>
    </md-button>

    <?php
    if (Configure::read('Tatoeba.devStylesheet')) {
        $name = 'TatoDev';
    } else {
        /* @translators: top-left site name written in big.
           You shouldn't translate it unless speakers of your
           language cannot read the Latin script. */
        $name = __('Tatoeba');
    }

    $path = array(
        'controller' => 'pages',
        'action' => 'index'
    );
    $logo = $this->Html->image(
        IMG_PATH . 'tatoeba.svg',
        array(
            'width' => 32,
            'height' => 32,
            'title' => $name
        )
    );
    echo $this->Html->link(
        $logo . $this->Html->div('tatoeba-name', $name),
        $path,
        [
            'escape' => false,
            'layout' => 'row',
            'flex-xs' => '',
            'layout-align' => 'center center'
        ]
    );
    ?>

    <?= $this->element('ui_language_button', [
        'class' => 'md-icon-button',
        'displayOption' => 'hide-gt-xs',
        'label' => ''
    ]); ?>
</div>