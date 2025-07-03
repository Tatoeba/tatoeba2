<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

/**
 * General view for the wall. Here are displayed all the messages.
 *
 * @category Wall
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

/* @translators: title of the Wall page */
$this->set('title_for_layout', $this->Pages->formatTitle(__('Wall')));

echo $this->Html->script('wall/wall.ctrl.js', ['block' => 'scriptBottom']);

?>

<h2>
<?php
$threadsCount = $this->Paginator->param('count');
echo format(__n('Wall (one thread)', 'Wall ({n}&nbsp;threads)', $threadsCount),
            array('n' => $this->Number->format($threadsCount)));
?>
</h2>

<div id="annexe_content" >
    <div class="section md-whiteframe-1dp">
        <?php /* @translators: title of the sidebar text on the Wall page */ ?>
        <h2><?php echo __('Tips'); ?></h2>
        <p>
        <?php
        echo format(
            __(
                'Before asking a question, '.
                'make sure to read the <a href="{}">FAQ</a>.', true
            ),
            $this->Url->build(array('controller' => 'pages', 'action' => 'faq'))
        );
        ?>
        </p>
        <p>
        <?php
        echo format(
            __(
                'We aim to maintain a healthy atmosphere for civilized discussions. '.
                'Please read our '.
                '<a href="{}">rules against bad behavior</a>.', true
            ),
            $this->Pages->getWikiLink('rules-against-bad-behavior')
        );
        ?>
        </p>
    </div>
    
    <div id="wall-language-banner" class="md-whiteframe-1dp">
    <?php
    echo $this->Html->link(
        __(
            'You may write in any language you want. '.
            'On Tatoeba, all languages are equal.', true
        ),
        array(
            "controller" => "sentences",
            "action" => "show",
            785667
        )
    );
    ?>
    </div>

    <div class="md-whiteframe-1dp">
        <md-subheader><?php echo __('Latest messages'); ?></md-subheader>
        <md-list class="annexe-menu" ng-cloak>
        <?php
        $mesg = count($tenLastMessages);

        foreach ($tenLastMessages as $currentMessage) {
            $url = $this->Url->build([
                'controller' => 'wall',
                'action' => 'index',
                '#' => 'message_'.$currentMessage->id
            ]);

            $css = $currentMessage->parent_id == null ? 'initial-post' : '';
            $icon = $currentMessage->parent_id == null ? 'feedback' : 'subdirectory_arrow_right';
            ?>
            <md-list-item class="md-2-line <?= $css ?>" href="<?= $url ?>">
                <md-icon><?= $icon ?></md-icon>
                <div class="md-list-item-text">
                    <h3><?= $currentMessage->user->username ?? $this->Html->tag('i', __('Former member')) ?></h3>
                    <p><?= $this->Date->ago($currentMessage->date) ?></p>
                </div>
            </md-list-item>
            <?php
        };
        ?>
        </md-list>
    </div>
</div>

<div id="main_content" ng-app="app" ng-controller="WallController as vm">
    <?php
    // leave a comment part
    if ($isAuthenticated) {
        echo $this->element('wall/add_form');

        echo '<div style="display:none">'."\n";
        echo $this->element('wall/add_form', ['isReply' => true]);
        echo '</div>'."\n";
    }
    ?>

    <?php
    $this->Pagination->display();
    ?>

    <div class="wall">
    <?php
    foreach ($allMessages as $message) {
        echo $this->element('wall/message', [
            'message' => $message,
            'isRoot' => true
        ]);
    }
    ?>
    </div>

    <?php
    $this->Pagination->display();
    ?>
</div>
