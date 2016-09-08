<?php
$username = $comment['User']['username'];
$avatarUrl = $members->imageUrl($comment['User']['image']);
$createdDate = $comment['SentenceComment']['created'];
$modifiedDate = $comment['SentenceComment']['modified'];
$commentId = $comment['SentenceComment']['id'];
$authorId = $comment['SentenceComment']['user_id'];
$commentText = $comment['SentenceComment']['text'];
$commentHidden = $comment['SentenceComment']['hidden'];
$sentence = null;
if (isset($comment['Sentence'])) {
    $sentence = $comment['Sentence'];
}
$sentenceId = $comment['SentenceComment']['sentence_id'];
$sentenceText = '<em>'.__('sentence deleted', true).'</em>';
if (isset($sentence['text'])) {
    $sentenceText = $sentence['text'];
}
$sentenceLang = $sentence['lang'];
$sentenceOwner = null;
if (!empty($sentence['User'])) {
    $sentenceOwner = $sentence['User']['username'];
}
$langDir = LanguagesLib::getLanguageDirection($sentenceLang);

$editUrl = $html->url(array(
    'controller' => 'sentence_comments',
    'action' => 'edit',
    $commentId
));
$sentenceUrl = $html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentenceId
));
$replyUrl = $html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentenceId.'#comment-'.$commentId
));
$dateLabel = format(
    __('{createdDate}, edited {modifiedDate}', true),
    array(
        'createdDate' => $date->ago($createdDate),
        'modifiedDate' => $date->ago($modifiedDate)
    )
);
$fullDateLabel = format(
    __('{createdDate}, edited {modifiedDate}', true),
    array(
        'createdDate' => $createdDate,
        'modifiedDate' => $modifiedDate
    )
);
$canViewContent = CurrentUser::isAdmin() || CurrentUser::get('id') == $authorId;
?>
<? if ($sentence) { ?>
    <div class="comment sentence" md-whiteframe="2"
         layout="row" layout-align="start center">
        <div class="text" flex>
            <?= $sentenceText ?>
        </div>
        <?php
        echo $languages->icon(
            $sentenceLang,
            array(
                'width' => 30,
                'height' => 20,
                'class' => 'lang'
            )
        );
        ?>
        <md-button class="md-icon-button" href="<?= $sentenceUrl ?>">
            <md-icon>info</md-icon>
        </md-button>
    </div>
<? } ?>
<md-card class="comment <?= $commentHidden ? 'inappropriate' : '' ?>">
    <md-card-header>
        <md-card-avatar>
            <img class="md-user-avatar" src="<?= $avatarUrl ?>"/>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title"><?= $username ?></span>
            <span class="md-subhead ellipsis" title="<?= $fullDateLabel ?>">
                <?= $dateLabel ?>
            </span>
        </md-card-header-text>

        <? foreach ($menu as $menuItem) {
            $confirmation = '';
            if (isset($menuItem['confirm'])) {
                $msg = $menuItem['confirm'];
                $confirmation = 'onclick="return confirm(\''.$msg.'\');"';
            }
            ?>
            <md-button class="md-icon-button"
                <?= $confirmation ?>
                       href="<?= $html->url($menuItem['url']) ?>">
                <md-icon><?= $menuItem['icon'] ?></md-icon>
            </md-button>
        <? } ?>
    </md-card-header>

    <md-divider></md-divider>

    <md-card-content>
        <? if ($commentHidden) { ?>
            <div class="warning-info" layout="row" layout-align="start center">
                <md-icon>warning</md-icon>
                <p>
                    <?= format(
                        __(
                            'The content of this message goes against '.
                            '<a href="{}">our rules</a> and was therefore hidden. '.
                            'It is displayed only to admins '.
                            'and to the author of the message.',
                            true
                        ),
                        'http://en.wiki.tatoeba.org/articles/show/rules-against-bad-behavior'
                    ); ?>
                </p>
            </div>
        <? } ?>

        <? if ($canViewContent) { ?>
            <p class="content"><?= $messages->formatedContent($commentText) ?></p>
        <? } ?>
    </md-card-content>
</md-card>
