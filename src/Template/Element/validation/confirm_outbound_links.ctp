<?php if ($confirmOutboundLinks ?? false): ?>
    <md-input-container class="md-block">
        <md-checkbox ng-model="outboundLinksConfirmed" class="md-primary">
            <?= $label ?>
        </md-checkbox>
    </md-input-container>
    <?= $this->Form->checkbox('outboundLinksConfirmed', [
            'class' => 'ng-hide',
            'ng-model' => 'outboundLinksConfirmed',
    ]) ?>
<?php endif; ?>
