<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2018 Gilles Bedel
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
 */

$this->set('title_for_layout', __("Switch my sentences' license"));
echo $this->Html->script('licensing/switch-license.ctrl.js', ['block' => 'scriptBottom']);

?>
<div ng-controller="switchLicenseCtrl" ng-init="init(<?= ($isRefreshing ? 'true' : 'false') ?>)">
<?php
echo $this->Html->tag('h2', __('Switch my sentences to CC0'));

if ($isSwitching) {
    echo $this->Html->tag('p', __('The license for your sentences is being switched. You will receive a private message when the operation is complete.'));
} else {
    echo $this->Html->tag('p', format(
        __('This page allows you to massively switch the license of your sentences to {CC0-link}. Among all your sentences, only the ones that meet the following conditions will be affected.'),
       array('CC0-link' => $this->SentenceLicense->getLicenseName('CC0 1.0'))
    ));
    echo $this->Html->nestedList(array(
        __('The current license of the sentence must be CC BY 2.0 FR.'),
        __('You must be the original creator of the sentence.'),
        __('The sentence must be original and not derived from translation.'),
    ));

?>
    <div id="switchList" ng-show="!isRefreshing">
<?php
    if (!$list->isEmpty()) {
        echo $this->element('licensing/list', compact($list));
    }
?>
    </div>
    <md-progress-circular ng-show="isRefreshing" md-mode="indeterminate" class="block-loader">
    </md-progress-circular>
<?php
    echo $this->Html->tag('p', __(
        'Press the following button to start or restart an analysis of which of your sentences can be switched to CC0.'
    ));
    echo $this->Html->tag('md-button', __('Start analysis'), [
        'type' => 'submit',
        'class' => 'md-raised md-primary',
        'ng-click' => 'refreshList()',
        'ng-disabled' => 'isRefreshing',
    ]);

    echo $this->Html->tag('h3', __('Switch license'));
    echo $this->Html->tag('p', __(
        'Press the following button to initiate the license switch of the above-listed sentences.'
    ));
    echo $this->Html->tag(
        'p',
        __('Be careful, this operation can not be undone. Switching from CC BY to CC0 is possible, but not the opposite.'),
        array('class' => 'warning')
    );
    echo $this->Form->create();
    echo $this->Html->tag('md-button', __('Switch license to CC0 1.0'), [
        'type' => 'submit',
        'class' => 'md-raised md-warn',
        'ng-disabled' => 'isRefreshing || '.($isSwitching ? 'true' : 'false'),
    ]);
    echo $this->Form->end();
}
?>
</div>
