<?php
use Cake\Core\Configure;

$sentenceId = $sentence->id;
$sentenceLang = $sentence->lang;
$sentenceAudios = $sentence->audios;

if (count($sentenceAudios)) {
    $path = Configure::read('Recordings.url').$sentenceLang.'/'.$sentenceId.'.mp3';
    $audio = isset($sentenceAudios[0]) ? $sentenceAudios[0] : $sentenceAudios;
    $author = isset($audio->user['username']) ? $audio->user['username'] : $audio['external']['username'];
    if (empty($author)) {
        $tooltip = __('Play audio');
    } else {
        $tooltip = format(
            __('Play audio recorded by {author}', true),
            ['author' => $author]
        );
    }
    ?>
    <md-button class="md-icon-button audioAvailable" ng-click="vm.playAudio('<?= $path ?>')">
        <md-icon>volume_up</md-icon>
        <md-tooltip md-direction="top">
            <?= $tooltip ?>
        </md-tooltip>
    </md-button>
    <?php
} else {
    ?>
    <md-button class="md-icon-button audioUnavailable" target="_blank" 
               href="https://en.wiki.tatoeba.org/articles/show/contribute-audio">
        <md-icon>volume_off</md-icon>
        <md-tooltip md-direction="top">
            <?= __('No audio for this sentence. Click to learn how to contribute.') ?>
        </md-tooltip>
    </md-button>
    <?php
}
?>