<?php
$username = $user->username ?? null;
$dateLabel = $this->Date->ago($message->date);
$fullDateLabel = $message->date;
$menu = $this->PrivateMessages->getMenu($message->folder, $message->id, $message->type);
$messageContent = $this->safeForAngular(
    $this->Messages->formatContent($message->content)
);
?>

<md-card class="comment">
    <md-card-header>
        <md-card-avatar>
            <?= $this->Members->image($user, array('class' => 'md-user-avatar')); ?>
        </md-card-avatar>
        <md-card-header-text>
            <span class="md-title">
                <?php
                    if ($message->type == 'human') {
                        echo $this->Html->link($username, [
                            'controller' => 'user',
                            'action' => 'profile',
                            $username
                        ]);
                    } else {
                        echo __('notification from Tatoeba');
                    }
                ?>
            </span>
            <span class="md-subhead ellipsis">
                <?= $dateLabel ?>
                <md-tooltip ng-cloak><?= $fullDateLabel ?></md-tooltip>
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
        <p class="content" dir="auto"><?= $messageContent ?></p>
    </md-card-content>
</md-card>
