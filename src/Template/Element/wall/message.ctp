<?php
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;

$username = $message->user->username;
$avatar = $message->user->image;
$createdDate = $message->date;
$modifiedDate = $message->modified;
$messageId = $message->id;
$authorId = $message->user_id;
$messageText = $message->content;
$messageHidden = $message->hidden;
$sentence = null;
$sentenceOwnerLink = null;

$editUrl = $this->Url->build(array(
    'controller' => 'sentence_comments',
    'action' => 'edit',
    $messageId
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

$menu = $this->Wall->getMenuFromPermissions(
    $message,
    $message['Permissions']
);

$children = $message->children;
$cssClass = isset($isRoot) ? 'wall-thread' : 'reply';
?>

<md-card class="<?= $cssClass ?> <?= $messageHidden ? 'inappropriate' : '' ?>">
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
            $itemLabel = $menuItem['text'];
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

    <md-card-content>
        <?php if ($messageHidden) { ?>
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

        <?php if (!$messageHidden || $canViewContent) { ?>
            <p class="content" dir="auto">
                <?= $this->Messages->formatContent($messageText) ?>
            </p>
        <?php } ?>
    </md-card-content>

    <?php
    foreach ($children as $child) {
        echo $this->element('wall/message', ['message' => $child]);
    }
    ?>
</md-card>