<div ng-repeat="translation in <?= $translations ?> track by translation.id"
     ng-if="::!translation.isHidden || undefined"
     ng-show="!translation.isHidden"
     class="translation" ng-class="{'not-reliable' : translation.correctness === -1, 'expanded': vm.isMenuExpanded, 'trusted-user': vm.menu.canLink}">
    
    <div layout="row" layout-align="stretch" flex>
    <div ng-click="translation.showActions = !translation.showActions" layout="row" layout-align="start center" role="switch" flex>
    <div style="font-size: 0">
    <icon-with-progress is-loading="vm.iconsInProgress['link' + translation.id]" ng-if="vm.menu.canLink">
        <md-button class="md-icon-button anim-squeeze" ng-show="vm.isMenuExpanded && translation.isDirect" ng-click="vm.saveLink('delete', translation)">
            <md-icon md-svg-src="/img/link_off.svg"></md-icon>
            <md-tooltip><?= __('Unlink this translation') ?></md-tooltip>
        </md-button>
        <md-button class="md-icon-button anim-squeeze" ng-show="vm.isMenuExpanded && !translation.isDirect" ng-click="vm.saveLink('add', translation)">
            <md-icon>link</md-icon>
            <md-tooltip><?= __('Make into direct translation') ?></md-tooltip>
        </md-button>
    </icon-with-progress>
    <md-icon class="chevron anim-squeeze" ng-show="!vm.isMenuExpanded || !vm.menu.canLink">chevron_right</md-icon>
    </div>

    <div class="lang">
        <language-icon lang="translation.lang" title="translation.lang_name"></language-icon>
    </div>

    <div class="text" dir="{{translation.dir}}" lang="{{translation.lang_tag}}">
        <span ng-if="translation.furigana" ng-bind-html="translation.furigana.html">
            <md-tooltip md-direction="top">{{translation.furigana.info_message}}</md-tooltip>
        </span>
        <span ng-if="!translation.furigana">{{translation.text}}</span>
        <md-tooltip ng-if="translation.isDuplicate">
            <?= format(
                __('Existing sentence #{number} has been added as a translation.'),
                ['number' => '{{::translation.id}}']
            ) ?>
        </md-tooltip>
    </div>
    </div>
    
    <md-button class="md-icon-button" ng-if="translation.editable" ng-click="vm.editTranslation(translation)">
        <md-icon>edit</md-icon>
        <md-tooltip>
            <?= __('Edit this translation'); ?>
        </md-tooltip>
    </md-button>

    <?= $this->element('sentences/sentence_icons', ['angularVar' => 'translation']); ?>
    </div>

    <?= $this->element('sentences/transcriptions', ['sentenceVar' => 'translation']); ?>
</div>
