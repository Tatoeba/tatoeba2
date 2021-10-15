<md-button class="md-icon-button audioAvailable" ng-click="vm.playAudio(audios)" ng-if="audios && audios.length > 0">
    <md-icon>volume_up</md-icon>
    <md-tooltip md-direction="top" ng-if="!vm.getAudioAuthor(audios)">
        <?= __('Play audio'); ?>
    </md-tooltip>
    <md-tooltip md-direction="top" ng-if="vm.getAudioAuthor(audios)">
        <?= format(
            __('Play audio recorded by {author}', true),
            ['author' => '{{vm.getAudioAuthor(audios)}}']
        ); ?>
    </md-tooltip>
</md-button>

<md-button class="md-icon-button audioUnavailable" target="_blank" ng-if="!audios || audios.length === 0"
            href="<?= h($this->cell('WikiLink', ['contribute-audio'])) ?>">
    <md-icon>volume_off</md-icon>
    <md-tooltip md-direction="top">
        <?= __('No audio for this sentence. Click to learn how to contribute.') ?>
    </md-tooltip>
</md-button>