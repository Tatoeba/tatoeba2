<?php
use App\Model\CurrentUser;

$sentenceId = $comment->sentence_id;
$user = $comment->user;
$username = $user['username'];
$avatar = $user['image'];
$text = $comment->text;
$createdDate = $comment->created;
$modifiedDate = $comment->modified;

if (empty($modifiedDate) || $createdDate == $modifiedDate) {
    $dateLabel = $this->Date->ago($createdDate);
    $fullDateLabel = $createdDate;
} else {
    $dateLabel = format(
        __('{createdDate}, edited {modifiedDate}'),
        array(
            'createdDate' => $this->Date->ago($createdDate),
            'modifiedDate' => $this->Date->ago($modifiedDate)
        )
    );
    $fullDateLabel = format(
        __('{createdDate}, edited {modifiedDate}'),
        array(
            'createdDate' => $createdDate,
            'modifiedDate' => $modifiedDate
        )
    );
}

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
            <?= $this->Members->image($username, $avatar, array('class' => 'md-user-avatar')); ?>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title">
                <?= $username ?>
            </span>
            <span class="md-subhead ellipsis">
                <?= $dateLabel ?>
                <md-tooltip ng-cloak><?= $fullDateLabel ?></md-tooltip>
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
            'value' => $text,
            'label'=> '',
            'lang' => '',
            'dir' => 'auto',
        ]);
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