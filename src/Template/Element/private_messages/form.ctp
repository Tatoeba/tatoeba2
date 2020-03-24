<?php
if (!isset($isReply)) {
    $isReply = false;
}

if ($isReply) {
    $headerTitle = __('Reply');
} else if (!$pm->id) {
    $headerTitle = __('New message');
} else {
    $headerTitle = __('Message');
}
?>

<?php if (isset($headerTitle)) { ?>
    <md-toolbar class="md-hue-1">
        <div class="md-toolbar-tools">
            <h2 flex>
                <?= $headerTitle ?>
            </h2>
        </div>
    </md-toolbar>
<?php } ?>

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
            'label' => __x('message', 'To'),
            'ng-model' => 'recipients',
            'ng-init' => format(
                'recipients = "{}"',
                addcslashes($recipients, '"\'{')
            ),
            'maxlength' => 250,
            'class' => 'pmTo',
            'lang' => '',
            'dir' => 'ltr',
        ]);
        ?>
        </md-input-container>

        <md-input-container>
        <?php
        echo $this->Form->input('title', [
            'label' => __('Title'),
            'ng-model' => 'title',
            'ng-init' => format(
                'title = "{}"',
                addcslashes($pm->title, '"\'{')
            ),
            'class' => 'pmTitle',
            'lang' => '',
            'dir' => 'auto',
            'value' => '',
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
            'value' => $content,
            'ng-non-bindable' => '',
        ]);
        ?>
        </div>

        <div ng-cloak layout="row" layout-align="end center" layout-padding>
            <md-button type="submit" name="submitType" value="saveDraft" class="md-raised">
                <?php echo __('Save as draft'); ?>
            </md-button>
            <md-button type="submit" name="submitType" value="send" class="md-raised md-primary">
                <?php echo __('Send'); ?>
            </md-button>
        </div>
        
        <?php
        echo $this->Form->end();
        ?>
    </div>
</div>
