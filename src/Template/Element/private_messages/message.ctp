<?php
$username = $user->username;
$avatar = $user->image;
$userProfileUrl = $this->Url->build(array(
    'controller' => 'user',
    'action' => 'profile',
    $username
));
$dateLabel = $this->Date->ago($message->date);
$fullDateLabel = $message->date;
$menu = $this->PrivateMessages->getMenu($message->folder, $message->id, $message->type);
?>

<md-card class="comment">
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
                <md-tooltip ng-cloak><?= $fullDateLabel ?></md-tooltip>
            </span>
        </md-card-header-text>

        <?php foreach ($menu as $menuItem) {
            if ($menuItem['text'] == '#') {
                $itemLabel = $replyIcon ? __('Reply') : __('Permalink');
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

    <md-card-content>
        <p class="content" dir="auto" ng-non-bindable>
            <?= $this->Messages->formatContent($message->content) ?>
        </p>
    </md-card-content>
</md-card>
