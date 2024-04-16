<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016 Gilles Bedel
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
use App\Model\CurrentUser;

$title = format(
    __('Audio contributed by {username}'),
    array('username' => $username)
);
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<?php
if (isset($sentencesWithAudio)) {
?>
    <div id="annexe_content" ng-cloak>
    <?php
        echo $this->element(
        'users_menu',
        array('username' => $username)
    );

    if (CurrentUser::get('username') == $username): ?>
        <div class="section md-whiteframe-1dp">
            <h2><?php echo __('My audio'); ?></h2>
            <?php
               $audioSettings->audio_attribution_url =
                   $this->safeForAngular($audioSettings->audio_attribution_url);
               echo $this->Form->create($audioSettings, array(
                   'url' => array('controller' => 'audio', 'action' => 'save_settings'),
                   'type' => 'post',
               ));
               echo $this->Form->control('audio_license', array(
                   'label' => __('License:'),
                   'options' => $this->AudioLicense->getLicenseOptions()
               ));
            ?>
            <md-input-container class="md-block">
                <?= $this->Form->control('audio_attribution_url', array(
                    'label' => __('Attribution URL:'),
                )) ?>
                <div class='hint'>
                    <?=__('Leave this field empty to use your profile page.') ?>
                </div>
            </md-input-container>
            <div layout="row" layout-align="center center">
                <md-button type="submit" class="md-raised md-primary">
                    <?php /* @translators: submit button for saving audio contribution settings (verb) */ ?>
                    <?php echo __('Save'); ?>
                </md-button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    <?php endif; ?>
    </div>
<?php
}
?>

<div id="main_content">
<div class="section md-whiteframe-1dp">
<?php
if (isset($sentencesWithAudio)) {
    if (count($sentencesWithAudio) == 0) {
        echo $this->Html->tag('h2', format(
            __('{username} has not contributed any audio'),
            array('username' => $username)
        ));
    } else {
        $title .= ' ' . format(__("(total {totalAudio})"), compact('totalAudio'));
        echo $this->Html->tag('h2', $title);

        $licenceMessage = $this->Audio->formatLicenceMessage(
            $audioSettings, $username
        );
        echo $this->Html->tag(
            'p',
            $this->safeForAngular($licenceMessage)
        );

        $this->Pagination->display();

        $type = 'mainSentence';
        $parentId = null;
        $withAudio = true;
        foreach ($sentencesWithAudio as $sentence) {
            $this->Sentences->displayGenericSentence(
                $sentence,
                $type,
                $withAudio,
                $parentId
            );
        }

        $this->Pagination->display();
    }
} else {
    echo $this->Html->tag(
        'h2',
        format(
            __("There's no user called {username}"),
            array('username' => $this->safeForAngular($username))
        )
    );
}
?>
</div>
</div>
