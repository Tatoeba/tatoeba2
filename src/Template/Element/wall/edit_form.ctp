<?php
$user = $message->user;
$username = $user['username'];
$createdDate = $message->date;
$modifiedDate = $message->modified;

$labelText = __x('wall message', '{createdDate}, edited {modifiedDate}');
$dateLabel = $this->Date->getDateLabel($labelText, $createdDate, $modifiedDate);
$dateTooltip = $this->Date->getDateLabel($labelText, $createdDate, $modifiedDate, true);

$cancelUrl = $this->Url->build([
    'action' => 'show_message',
    $message->id,
    "#" => "message_".$message->id
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
        $message->content = $this->safeForAngular($message->content);
        echo $this->Form->create($message);
        echo $this->Form->textarea('content');

        echo $this->element('validation/confirm_outbound_links', [
            'label' => __('I confirm the links in my wall post are legitimate '.
                          'and not included for SEO purposes.'),
        ]);
        ?>

        <div layout="row" layout-align="end center" layout-padding>
            <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                <?php /* @translators: cancel button of wall post edition form (verb) */ ?>
                <?php echo __('Cancel'); ?>
            </md-button>

            <md-button type="submit" class="md-raised md-primary">
                <?php /* @translators: save button of wall post edition form (verb) */ ?>
                <?php echo __('Save changes'); ?>
            </md-button>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </md-card-content>
</md-card>
