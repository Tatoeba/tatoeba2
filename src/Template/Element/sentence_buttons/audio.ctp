<md-button class="md-icon-button" ng-href="{{vm.getAudioUrl(audios)}}" ng-click="vm.playAudio(audios); $event.preventDefault()" ng-if="audios.length > 0">
    <md-icon ng-if="!audios[0].hasOwnProperty('enabled') || audios[0].enabled">volume_up</md-icon>
    <md-icon ng-if="audios[0].hasOwnProperty('enabled') && !audios[0].enabled">volume_off</md-icon>
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

<md-button class="md-icon-button audioUnavailable" target="_blank" ng-if="!(audios.length > 0)"
            href="<?= h($this->Pages->getWikiLink('contribute-audio')) ?>">
    <md-icon>volume_off</md-icon>
    <md-tooltip md-direction="top">
        <?= __('No audio for this sentence. Click to learn how to contribute.') ?>
    </md-tooltip>
</md-button>
