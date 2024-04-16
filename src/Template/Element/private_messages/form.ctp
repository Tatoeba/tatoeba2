<?php
if (!isset($isReply)) {
    $isReply = false;
}

if ($isReply) {
    /* @translators: title of section containing a reply
       form to a private message (noun) */
    $headerTitle = __x('header', 'Reply');
} else if (!$pm->id) {
    /* @translators: title of section containing a form for
       a new private message (noun) */
    $headerTitle = __('New message');
} else {
    /* @translators: title of section containing a form for
       a private message previously saved as draft (noun) */
    $headerTitle = __('Message');
}
?>

<md-toolbar class="md-hue-1">
    <div class="md-toolbar-tools">
        <h2 flex>
            <?= $headerTitle ?>
        </h2>
    </div>
</md-toolbar>

<div id="private-message-form" class="section">
    <div>
        <?php
        echo $this->Form->create($pm, [
            'url' => ['action' => 'send'],
            'class' => 'message form',
            'layout' => 'column'
        ]);
        
        if (!$isReply) {
            echo $this->Form->hidden('messageId', array('value' => $pm->id));
        }
        echo $this->Form->hidden('submitType', array('value' => ''));
        $this->Form->unlockField('submitType');
        ?>
        
        <md-input-container>
        <?php
        echo $this->Form->control('recipients', [
            /* @translators: recipient field label in private message form */
            'label' => __x('message', 'To'),
            'value' => $this->safeForAngular($recipients),
            'maxlength' => 250,
            'class' => 'pmTo',
            'lang' => '',
            'dir' => 'ltr',
        ]);
        ?>
        </md-input-container>

        <md-input-container>
        <?php
        echo $this->Form->control('title', [
            /* @translators: title field label in private message form */
            'label' => __('Title'),
            'value' => $this->safeForAngular($pm->title),
            'class' => 'pmTitle',
            'lang' => '',
            'dir' => 'auto',
        ]);
        ?>
        </md-input-container>

        <div class="textarea">
        <?php
        $content = $pm->content;
        if ($isReply) {
            $content = $this->PrivateMessages->formatReplyMessage($pm->content, $recipients);
        }
        echo $this->Form->textarea('content', [
            'lang' => '',
            'dir' => 'auto',
            'value' => $this->safeForAngular($content),
        ]);
        ?>
        </div>

        <div ng-cloak layout="row" layout-align="end center" layout-padding>
            <md-button type="submit" name="submitType" value="saveDraft" class="md-raised">
                <?php /* @translators: button to save a private message as draft */ ?>
                <?php echo __('Save as draft'); ?>
            </md-button>
            <md-button type="submit" name="submitType" value="send" class="md-raised md-primary">
                <?php /* @translators: button to send a private message (verb) */ ?>
                <?php echo __('Send'); ?>
            </md-button>
        </div>
        
        <?php
        echo $this->Form->end();
        ?>
    </div>
</div>
