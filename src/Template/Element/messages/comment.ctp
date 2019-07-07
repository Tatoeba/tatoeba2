<?php
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;

$username = $comment->user->username;
$avatar = $comment->user->image;
$createdDate = $comment->created;
$modifiedDate = $comment->modified;
$commentId = $comment->id;
$authorId = $comment->user_id;
$commentText = $comment->text;
$commentHidden = $comment->hidden;
$sentence = null;
$sentenceOwnerLink = null;
if (isset($comment->sentence)) {
    $sentence = $comment->sentence;
}
if ($sentence && isset($sentence->user->username)) {
    $sentenceOwner = $sentence->user->username;
    $sentenceOwnerLink = $this->Html->link(
        $sentenceOwner,
        array(
            'controller' => 'user',
            'action' => 'profile',
            $sentenceOwner
        )
    );
}
$sentenceId = $comment->sentence_id;
$sentenceLink = $this->Html->link(
    '#'.$sentenceId,
    array(
        'controller' => 'sentences',
        'action' => 'show',
        $sentenceId
    )
);
$sentenceText = '<em>'.__('sentence deleted').'</em>';
if (isset($sentence['text'])) {
    $sentenceText = h($sentence->text);
}
$sentenceLang = $sentence ? $sentence->lang : null;
$sentenceOwner = null;
if ($sentence && $sentence->user) {
    $sentenceOwner = $sentence->user->username;
}
$langDir = LanguagesLib::getLanguageDirection($sentenceLang);

$editUrl = $this->Url->build(array(
    'controller' => 'sentence_comments',
    'action' => 'edit',
    $commentId
));
$sentenceUrl = $this->Url->build(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentenceId
));
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
$canViewContent = CurrentUser::isAdmin() || CurrentUser::get('id') == $authorId;
$userProfileUrl = $this->Url->build(array(
    'controller' => 'user',
    'action' => 'profile',
    $username
));
if ($sentenceOwnerLink) {
    $sentenceInfoLabel = __('Sentence {number} — belongs to {username}');
} else {
    $sentenceInfoLabel = __('Sentence {number}');
}

?>

<?php if (!isset($hideSentence) || !$hideSentence) { ?>
<div class="comment sentence md-whiteframe-1dp">
    <div class="info">
        <?= format(
            $sentenceInfoLabel,
            array(
                'number' => $sentenceLink,
                'username' => $sentenceOwnerLink
            )
        ); ?>
    </div>
    <div layout="row" layout-align="start center">
        <div class="text" dir="<?= $langDir ?>" flex>
            <?= $sentenceText ?>
        </div>
        <?php
        echo $this->Languages->icon(
            $sentenceLang,
            array(
                'ng-cloak' => true,
                'class' => 'lang'
            )
        );
        ?>
        <md-button ng-cloak class="md-icon-button" href="<?= $sentenceUrl ?>">
            <md-icon>info</md-icon>
        </md-button>
    </div>
</div>
<?php } ?>

<md-card class="comment <?= $commentHidden ? 'inappropriate' : '' ?>">
    <md-card-header>
        <md-card-avatar>
            <?= $this->Members->image($username, $avatar, array('class' => 'md-user-avatar')); ?>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title">
                <a href="<?= $userProfileUrl ?>"><?= $username ?></a>
            </span>
            <span class="md-subhead ellipsis" title="<?= $fullDateLabel ?>">
                <?= $dateLabel ?>
            </span>
        </md-card-header-text>

        <?php foreach ($menu as $menuItem) {
            if ($menuItem['text'] == '#') {
                $itemLabel = $replyIcon ? __('reply') : __('permalink');
            } else {
                $itemLabel = $menuItem['text'];
            }
            $confirmation = '';
            if (isset($menuItem['confirm'])) {
                $msg = $menuItem['confirm'];
                $confirmation = 'onclick="return confirm(\''.$msg.'\');"';
            }
            ?>
            <md-button ng-cloak
                       class="md-icon-button" <?= $confirmation ?>
                       href="<?= $this->Url->build($menuItem['url']) ?>"
                       aria-label="<?= $itemLabel ?>">
                <md-icon><?= $menuItem['icon'] ?></md-icon>
                <md-tooltip><?= $itemLabel ?></md-tooltip>
            </md-button>
        <?php } ?>
    </md-card-header>

    <md-divider></md-divider>

    <md-card-content>
        <?php if ($commentHidden) { ?>
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
        <?php } ?>

        <?php if (!$commentHidden || $canViewContent) { ?>
            <p class="content" dir="auto">
                <?= $this->Messages->formatContent($commentText) ?>
            </p>
        <?php } ?>
    </md-card-content>
</md-card>
