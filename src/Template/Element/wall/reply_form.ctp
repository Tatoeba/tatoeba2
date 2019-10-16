<?php
use App\Model\CurrentUser;

$user = CurrentUser::get('User');
$username = $user['username'];
$avatar = $user['image'];
$editUrl = $this->Url->build([
    'controller' => 'wall',
    'action' => 'edit'
]);
?>

<a id="reply-form-<?= $parentId ?>"></a>
<md-card id="form-<?= $parentId ?>" ng-hide="true"
         class="wall comment form" ng-cloak>

    <md-card-header>
        <md-card-avatar>
            <?= $this->Members->image($username, $avatar, array('class' => 'md-user-avatar')); ?>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title">
                <?= $username ?>
            </span>
            <span class="md-subhead ellipsis">
                <?= __('Add a message: '); ?>
            </span>
        </md-card-header-text>

        <md-button class="md-icon-button" aria-label="<?= __('edit') ?>" 
                   ng-if="vm.savedReplies[<?= $parentId ?>].id"
                   ng-href="<?= $editUrl ?>/{{vm.savedReplies[<?= $parentId ?>].id}}">
            <md-icon>edit</md-icon>
            <md-tooltip><?= __('edit') ?></md-tooltip>
        </md-button>
    </md-card-header>

    <md-divider></md-divider>

    <md-progress-linear ng-if="vm.isSaving[<?= $parentId ?>]" 
                        md-mode="indeterminate"></md-progress-linear>

    <md-card-content class="content">
        <?php
        echo $this->Form->create('', [
            'ng-submit' => 'vm.saveReply('.$parentId.')',
            'onsubmit' => 'event.preventDefault()'
        ]);
        ?>

        <div class="hidden">
        <?= $this->Form->input('replyTo', array('value' => '')); ?>
        </div>

        <?php
        echo $this->Form->textarea('content', [
            'id' => 'reply-input-'.$parentId,
            'label'=> '',
            'lang' => '',
            'dir' => 'auto',
            'ng-model' => 'vm.replies['.$parentId.']'
        ]);
        ?>

        <div ng-cloak layout="row" layout-align="end center">
            <md-button class="md-raised" ng-click="vm.hideForm(<?= $parentId ?>)">
                <?php echo __('Cancel'); ?>
            </md-button>

            <md-button type="submit" class="md-raised md-primary submit"
                       ng-disabled="vm.isSaving[<?= $parentId ?>]">
                <?php echo __('Send'); ?>
            </md-button>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </md-card-content>

    <md-card-content class="reply-saved" ng-hide="true">
        {{vm.savedReplies[<?= $parentId ?>].content}}
    </md-card-content>
</md-card>