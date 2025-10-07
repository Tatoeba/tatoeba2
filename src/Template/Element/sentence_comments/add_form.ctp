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
                <?= __('Add a comment'); ?>
            </span>
        </md-card-header-text>
    </md-card-header>

    <md-card-content class="content">
        <?php
        echo $this->Form->create('', [
            'url' => ['controller' => 'sentence_comments', 'action' => 'save']
        ]);
        echo $this->Form->hidden('sentence_id', ['value' => $sentenceId]);
        ?>
        
        <?php
        echo $this->Form->textarea('text', [
            'value' => $this->safeForAngular($text ?? ''),
            'label'=> '',
            'lang' => '',
            'dir' => 'auto',
        ]);

        echo $this->element('validation/confirm_outbound_links', [
            'label' => __('I confirm the links in my comment are legitimate '.
                          'and not included for SEO purposes.')
        ]);
        ?>

        <div ng-cloak layout="row" layout-align="end center">
            <md-button type="submit" class="md-raised md-primary">
                <?php echo __('Submit comment'); ?>
            </md-button>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </md-card-content>

    <md-divider></md-divider>

    <md-card-content>
        <div class="hint">
            <strong><?php echo __('Good practices'); ?></strong>
            <ul>
                <li><?= __('Say "welcome" to new users.'); ?></li>
                <li><?= __('Use private messages to discuss things unrelated to the sentence.'); ?>                
            </ul>
        </div>
    </md-card-content>
</md-card>