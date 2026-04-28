<?php $this->set('title_for_layout', $this->Pages->formatTitle(__('Terms of use'))); ?>

<?php if (!$translated) { ?>
    <div class="warning">
        <?= __(
            'These Terms of Use have not been translated yet into your language. '.
            'Below is the original version in French.'
        ); ?>
    </div>
<?php } ?>


<div id="terms-of-use" class="section md-whiteframe-1dp">
    <?= $content ?>
</div>