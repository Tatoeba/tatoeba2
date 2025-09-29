<?php
use App\Model\CurrentUser;

$sentenceId = $comment->sentence_id;
$user = $comment->user;
$username = $user['username'];
$text = $this->request->getData('text') ?? $comment->text;
$createdDate = $comment->created;
$modifiedDate = $comment->modified;

$labelText = __x('sentence comment', '{createdDate}, edited {modifiedDate}');
$dateLabel = $this->Date->getDateLabel($labelText, $createdDate, $modifiedDate);
$dateTooltip = $this->Date->getDateLabel($labelText, $createdDate, $modifiedDate, true); 

$cancelUrl = $this->Url->build([
    "controller" => "sentences",
    "action" => "show",
    $sentenceId,
    "#" => "comment-".$comment->id,
]);
?>

<md-card class="comment form">

    <md-card-header>
        <md-card-avatar>
            <?= $this->Members->image($user, array('class' => 'md-user-avatar')); ?>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title">
                <?= $username ?>
            </span>
            <span class="md-subhead ellipsis">
                <?= $dateLabel ?>
                <md-tooltip ng-cloak><?= $dateTooltip ?></md-tooltip>
            </span>
        </md-card-header-text>
    </md-card-header>

    <md-card-content class="content">
        <?php
        echo $this->Form->create($comment);
        echo $this->Form->hidden('sentence_id', ['value' => $sentenceId]);
        ?>
        
        <?php
        echo $this->Form->textarea('text', [
            'value' => $this->safeForAngular($text),
            'label'=> '',
            'lang' => '',
            'dir' => 'auto',
        ]);
        ?>

        <?php if ($confirmOutboundLinks ?? false): ?>
            <md-input-container class="md-block">
                <md-checkbox ng-model="outboundLinksConfirmed" class="md-primary">
                    <?= __('I confirm the links in my comment are legitimate '.
                           'and not included for SEO purposes.') ?>
                </md-checkbox>
                <?= $this->Form->checkbox('outboundLinksConfirmed', [
                        'class' => 'ng-hide',
                        'ng-model' => 'outboundLinksConfirmed',
                ]) ?>
            </md-input-container>
        <?php endif; ?>

        <div layout="row" layout-align="end center" layout-padding>
            <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                <?php /* @translators: cancel button of sentence comment edition form (verb) */ ?>
                <?php echo __('Cancel'); ?>
            </md-button>

            <md-button type="submit" class="md-raised md-primary">
                <?php /* @translators: save button of sentence comment edition form (verb) */ ?>
                <?php echo __('Save changes'); ?>
            </md-button>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </md-card-content>
</md-card>
