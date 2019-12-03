<?php
$user = $message->user;
$username = $user['username'];
$avatar = $user['image'];
$createdDate = $message->date;
$modifiedDate = $message->modified;

if (empty($modifiedDate) || $createdDate == $modifiedDate) {
    $dateLabel = $this->Date->ago($createdDate);
} else {
    $dateLabel = format(
        __x('wall message', '{createdDate}, edited {modifiedDate}'),
        array(
            'createdDate' => $this->Date->ago($createdDate),
            'modifiedDate' => $this->Date->ago($modifiedDate, false)
        )
    );
}

$cancelUrl = $this->Url->build([
    'action' => 'show_message',
    $message->id,
    "#" => "message_".$message->id
]);
?>

<md-card class="comment form">

    <md-card-header>
        <md-card-avatar>
            <?= $this->Members->image($username, $avatar, array('class' => 'md-user-avatar')); ?>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title">
                <?= $username ?>
            </span>
            <span class="md-subhead ellipsis">
                <?= $dateLabel ?>
                <md-tooltip ng-cloak><?= $dateLabel ?></md-tooltip>
            </span>
        </md-card-header-text>
    </md-card-header>

    <md-card-content class="content">
        <?php
        echo $this->Form->create($message);

        echo $this->Form->textarea('content');
        ?>

        <div layout="row" layout-align="end center" layout-padding>
            <md-button class="md-raised" href="<?= $cancelUrl; ?>">
                <?php echo __('Cancel'); ?>
            </md-button>

            <md-button type="submit" class="md-raised md-primary">
                <?php echo __('Save changes'); ?>
            </md-button>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </md-card-content>
</md-card>
