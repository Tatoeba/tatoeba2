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

if ($currentJob) {
    if (isset($currentJob['completed'])) {
        $message = __('The license switch of your sentences is completed. A report has been sent to you by private message.');
    } elseif (isset($currentJob['fetched'])) {
        $message = __('The license switch of your sentences is in progress. You will receive a private message when it will be completed.');
    } elseif (isset($currentJob['created'])) {
        $message = __('The license switch of your sentences will be started soon. You will receive a private message when it will be completed.');
    } else {
        $message = __('A problem occured while switching the license of your sentences.');
    }
    echo $this->Html->tag('p', $message);
} else {
    echo $this->Html->tag('p', format(
        __('This page allows you to massively switch the license of your sentences to {CC0-link}. Among all your sentences, only the ones that meet the following conditions will be affected.'),
       array('CC0-link' => $this->Sentences->License->licenseLink('CC0 1.0'))
    ));
    echo $this->Html->nestedList(array(
        __('The current license of the sentence must be CC BY 2.0 FR.'),
        __('You must be the original creator of the sentence.'),
        __('The sentence must be original and not derived from translation.'),
    ));

?>
<div ng-controller="switchLicenseCtrl">
<?
    echo $this->Html->tag('md-button', __('Refresh list'), [
        'type' => 'submit',
        'class' => 'md-raised md-primary',
        'ng-click' => 'refreshList()',
        'ng-disabled' => 'isRefreshing',
        'ng-init' => 'isRefreshing = '.($isRefreshing ? 'true' : 'false'),
    ]);
?>
</div>
<?
    echo $this->Html->tag('p', __(
        'Press the following button to initiate the license switch.'
    ));
    echo $this->Html->tag(
        'p',
        __('Be careful, this operation can not be undone. Switching from CC BY to CC0 is possible, but not the opposite.'),
        array('class' => 'warning')
    );
    echo $this->Form->create();
    echo $this->Form->submit(__('Switch license to CC0 1.0'));
    echo $this->Form->end();
}

