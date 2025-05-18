<?php
use App\Model\CurrentUser;

$user = CurrentUser::get('User');
$username = $user['username'];
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
                <?= __('Add a message: '); ?>
            </span>
        </md-card-header-text>
    </md-card-header>

    <md-card-content class="content">
        <?php
        echo $this->Form->create('', [
            'url' => ['controller' => 'wall', 'action' => 'save']
        ]);
        ?>

        <div class="hidden">
        <?= $this->Form->input('replyTo', array('value' => '')); ?>
        </div>

        <?php
        echo $this->Form->textarea('content', [
            'label'=> '',
            'lang' => '',
            'dir' => 'auto',
        ]);
        ?>

        <div ng-cloak layout="row" layout-align="end center">
            <md-button type="submit" class="md-raised md-primary submit">
                <?php /* @translators: button to post a new message on the Wall (verb) */ ?>
                <?php echo __('Send'); ?>
            </md-button>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </md-card-content>
</md-card>