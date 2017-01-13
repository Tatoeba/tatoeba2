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

$title = format(
    __('Audio contributed by {username}', true),
    array('username' => $username)
);
$this->set('title_for_layout', $pages->formatTitle($title));
?>

<?php
if (isset($sentencesWithAudio)) {
?>
    <div id="annexe_content">
    <?php
        echo $this->element(
        'users_menu',
        array('username' => $username)
    );

    if (CurrentUser::get('username') == $username): ?>
        <div class="section" md-whiteframe="1">
            <h2><?php __('My audio'); ?></h2>
            <?php
               echo $form->create('Audio', array(
                   'url' => array('controller' => 'audio', 'action' => 'save_settings'),
                   'type' => 'post',
               ));
               echo $form->input('audio_license', array(
                   'label' => __('License:', true),
                   'options' => $audio->getLicenseOptions(),
                   'value' => $audioSettings['User']['audio_license'],
               ));
            ?>
            <md-input-container class="md-block">
            <?php
               $tip = __('Leave this field empty to use your profile page.', true);
               echo $form->input('audio_attribution_url', array(
                   'label' => __('Attribution URL:', true),
                   'value' => $audioSettings['User']['audio_attribution_url'],
                   'after' => '<div class="hint">'.$tip.'</div>',
               ));
            ?>
            </md-input-container>
            <div layout="row" layout-align="center center">
                <md-button type="submit" class="md-raised md-primary">
                    <?php __('Save'); ?>
                </md-button>
            </div>
            <?php $form->end(); ?>
        </div>
    <?php endif; ?>
    </div>
<?php
}
?>

<div id="main_content">
<div class="module">
<?php
if (isset($sentencesWithAudio)) {
    if (count($sentencesWithAudio) == 0) {
        echo $html->tag('h2', format(
            __('{username} does not have contributed any audio', true),
            array('username' => $username)
        ));
    } else {
        $title = $paginator->counter(
            array(
                'format' => $title . ' ' . __("(total %count%)", true)
            )
        );
        echo $html->tag('h2', $title);

        $licenceMessage = $this->Audio->formatLicenceMessage(
            $audioSettings['User'], $username
        );
        echo $html->tag('p', $licenceMessage);

        $paginationUrl = array($username);
        $pagination->display($paginationUrl);

        $type = 'mainSentence';
        $parentId = null;
        $withAudio = true;
        foreach ($sentencesWithAudio as $sentence) {
            $sentences->displayGenericSentence(
                $sentence,
                $type,
                $withAudio,
                $parentId
            );
        }

        $pagination->display($paginationUrl);
    }
} else {
    echo $html->tag('h2', format(
        __("There's no user called {username}", true),
        array('username' => $username)
    ));
}
?>
</div>
</div>
