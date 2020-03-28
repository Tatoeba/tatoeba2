<?php
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;

$username = isset($message->user) ? $message->user->username : CurrentUser::get('username');
$avatar = isset($message->user) ? $message->user->image : CurrentUser::get('image');
$createdDate = $message->date;
$modifiedDate = $message->modified;
$messageId = $message->id;
$authorId = $message->owner;
$messageText = $this->App->safeForAngular($message->content);
$messageHidden = $message->hidden;
$sentence = null;
$sentenceOwnerLink = null;

$editUrl = $this->Url->build(array(
    'controller' => 'sentence_comments',
    'action' => 'edit',
    $messageId
));

$labelText = __x('wall message', '{createdDate}, edited {modifiedDate}');
$dateLabel = $this->Date->getDateLabel($labelText, $createdDate, $modifiedDate);
$dateTooltip = $this->Date->getDateLabel($labelText, $createdDate, $modifiedDate, true);
$canViewContent = CurrentUser::isAdmin() || CurrentUser::get('id') == $authorId;
$userProfileUrl = $this->Url->build(array(
    'controller' => 'user',
    'action' => 'profile',
    $username
));

if (isset($message['Permissions'])) {
    $menu = $this->Wall->getMenuFromPermissions(
        $message,
        $message['Permissions']
    );
} else {
    $menu = [];
}
$menu[] = [
    'text' => 'permalink',
    'icon' => 'link',
    'url' => [
        'controller' => 'wall',
        'action' => 'show_message',
        $messageId,
        '#' => "message_" .$messageId
    ]
];

$children = $message->children;
$cssClass = isset($isRoot) ? 'wall-thread' : 'reply';
$canReply = false;
?>

<md-card id="message_<?= $messageId ?>" class="comment <?= $cssClass ?> <?= $messageHidden ? 'inappropriate' : '' ?>">
    <md-card-header>
        <md-card-avatar>
            <?= $this->Members->image($username, $avatar, array('class' => 'md-user-avatar')); ?>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title">
                <a href="<?= $userProfileUrl ?>"><?= $username ?></a>
            </span>
            <span class="md-subhead ellipsis">
                <?= $dateLabel ?>
                <md-tooltip ng-cloak><?= $dateTooltip ?></md-tooltip>
            </span>
        </md-card-header-text>

        <?php foreach ($menu as $menuItem) {
            $itemLabel = $menuItem['text'];
            $confirmation = '';
            if (isset($menuItem['confirm'])) {
                $msg = $menuItem['confirm'];
                $confirmation = 'onclick="return confirm(\''.$msg.'\');"';
            }
            if ($menuItem['icon'] === 'reply') {
                $canReply = true;
            }
            ?>
            <md-button ng-cloak
                       class="md-icon-button" <?= $confirmation ?>
                       aria-label="<?= $itemLabel ?>"
                       <?php if (isset($menuItem['url'])) { ?>
                            href="<?= $this->Url->build($menuItem['url']) ?>"
                       <?php } else if ($menuItem['icon'] === 'reply') { ?>
                            ng-click="vm.showForm(<?= $message->id ?>)"
                       <?php } ?>>
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

    <?php if (count($children) > 0) { ?>
        <md-button ng-click="vm.expandOrCollapse(<?= $message->id ?>)" ng-cloak>
            <md-icon>{{vm.hiddenReplies[<?= $message->id ?>] ? 'expand_more' : 'expand_less'}}</md-icon>
            <span ng-if="!vm.hiddenReplies[<?= $message->id ?>]">
                <?= __('hide replies') ?>
            </span>
            <span ng-if="vm.hiddenReplies[<?= $message->id ?>]">
                <?= __('show replies') ?>
            </span>
        </md-button>

        <div ng-if="!vm.hiddenReplies[<?= $message->id ?>]">
        <?php
        foreach ($children as $child) {
            echo $this->element('wall/message', ['message' => $child]);
        }
        ?>
        </div>
    <?php } ?>
    
    <?php
    if ($canReply) {
        echo $this->element('wall/reply_form', ['parentId' => $message->id]);
    }
    ?>
</md-card>
