<?php
/*
 * Array
(
    [SentenceComment] => Array
        (
            [id] => 41850
            [user_id] => 3746
            [text] => test
            [created] => 2016-06-20 23:41:51
            [modified] => 2016-06-20 23:41:51
            [sentence_id] => 9999
            [hidden] => 0
        )

    [Sentence] => Array
        (
            [id] => 9999
            [text] => D'une part je suis occupé, d'autre part, je ne suis pas intéressé.
            [lang] => fra
            [correctness] => 0
            [user_id] => 5
            [User] => Array
                (
                    [username] => TRANG
                )

        )

    [User] => Array
        (
            [id] => 3746
            [username] => trang124
            [image] =>
        )

)
*/
$username = $comment['User']['username'];
$avatarUrl = $members->imageUrl($comment['User']['image']);
$createdDate = $comment['SentenceComment']['created'];
$modifiedDate = $comment['SentenceComment']['modified'];
$commentId = $comment['SentenceComment']['id'];
$commentText = $comment['SentenceComment']['text'];
$sentence = null;
if (isset($comment['Sentence'])) {
    $sentence = $comment['Sentence'];
}
$sentenceId = $comment['SentenceComment']['sentence_id'];
$sentenceText = $sentence['text'];
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
?>
<md-card>
    <md-card-header>
        <md-card-avatar>
            <img class="md-user-avatar" src="<?= $avatarUrl ?>"/>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title"><?= $username ?></span>
            <span class="md-subhead"><?= $createdDate ?>, <?= $modifiedDate ?></span>
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
        <? if ($sentence) { ?>
            <div class="sentence" layout="row" layout-align="start center">
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

        <p><?= $messages->formatedContent($commentText) ?></p>
    </md-card-content>
</md-card>
