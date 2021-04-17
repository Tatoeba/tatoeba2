<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Etienne Deparis <etienne.deparis@umaneti.net>
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
 * @author   Etienne Deparis <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

use Cake\Controller\Component\AuthComponent;

$this->Html->script('/js/elements/login-dialog.ctrl.js', ['block' => 'scriptBottom']);

$registerUrl = $this->Url->build([
    'controller' => 'users',
    'action' => 'register'
]);
?>

<div ng-controller="LoginDialogController as vm" layout="row" layout-align="center center" flex hide-xs hide-sm>
    <md-button href="<?= $registerUrl ?>">
    <?php
    /* @translators: link to the Register page in the top bar (verb) */
    echo __('Register');
    ?>
    </md-button>

    <md-button ng-click="vm.showDialog('<?= $this->Pages->currentPageUrl() ?>')">
        <?php 
        /* @translators: link to open the Login box in the top bar (verb) */
        echo __('Log in');
        ?>
    </md-button>
</div>