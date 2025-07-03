<?php

use App\Model\Entity\Sentence;
use App\Model\Entity\SentenceComment;
use App\Model\Entity\Wall;

$this->set('title_for_layout', $this->Pages->formatTitle(__('Report content')));

$introText = format(
    __('Use this form to let website admins know about content that goes against '.
       '<a href="{}">our rules of community participation</a>.'),
    $this->Pages->getWikiLink('rules-against-bad-behavior')
);
if ($entity instanceof SentenceComment || $entity instanceof Sentence) {
    $introText .= " ".__(
        'If you simply want to report a problem with the sentence, '.
        'leave a comment on it instead.'
    );
}
?>

<?php /* @translators: title of the content reporting page */ ?>
<?= $this->Html->tag('h2', __('Report inappropriate content')) ?>

<div class="report">
    <?= $this->Html->tag('p', $introText) ?>

    <?php if ($entity instanceof Wall): ?>
        <?= $this->Html->tag('h3', __('Report the following Wall message')) ?>
        <div class="wall-message">
            <?= $this->element('wall/message', ['message' => $entity]) ?>
        </div>
    <?php elseif ($entity instanceof SentenceComment): ?>
        <?= $this->Html->tag('h3', __('Report the following sentence comment')) ?>
        <div class="">
            <?= $this->element(
                'sentence_comments/comment',
                [
                    'comment' => $entity,
                    'menu' => $this->Comments->getMenuForComment(
                        $entity,
                        [
                            'canDelete' => false,
                            'canEdit' => false,
                            'canHide' => false,
                            'canPM' => false,
                        ],
                        false
                    ),
                    'replyIcon' => false,
                    'hideSentence' => true,
                ]
            ) ?>

        </div>
    <?php endif; ?>
    
    <?= $this->Html->tag('h3', __('What is the problem?')) ?>
    <?= $this->Form->create() ?>
    <?= $this->Form->textarea('details', [
        'value' => $this->safeForAngular($details),
        'lang' => '',
        'dir' => 'auto',
    ]) ?>
    <?= $this->Form->hidden('origin', ['value' => $this->safeForAngular($origin)]) ?>
    <div layout="row" layout-align="start center">
        <md-button class="md-raised" onclick="history.back();">
            <?php /* @translators: cancel button of content reporting form (verb) */ ?>
            <?= __('Go back') ?>
        </md-button>
    
        <md-button type="submit" class="md-raised md-primary">
            <?php /* @translators: submit button of content reporting form (verb) */ ?>
            <?= __('Report content') ?>
        </md-button>
    </div>
    <?= $this->Form->end(); ?>
</div>
