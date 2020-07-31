<?php
use App\Lib\LanguagesLib;

// root, depth, parent, children
$detachUrl = $this->Url->build([
    'controller' => 'tagsSuperTags',
    'action' => 'removeTagSuperTag',
    $root,
    $parent,
    'superTag'
]);

$deleteUrl = $this->Url->build([
    'controller' => 'superTags',
    'action' => 'removeSuperTag',
    $parent
]);
?>

<md-card class="comment <?= ($depth == 0) ? 'wall-thread' : 'reply' ?>" flex>
    <md-card-content>
        <md-card-actions layout="row" layout-align="end center">
            <md-card-icon-actions>
                <span>
                <?= $all_super_tags[$parent] ?>
                    <!-- Detach super tag from parent -->
                    <?php
                        if ($root !== -1) {
                    ?>
                        <md-button class="md-icon-button" aria-label="<?= __('detach') ?>" ng-href="<?= $detachUrl ?>">
                            <md-icon>reply</md-icon>
                            <md-tooltip><?= __('Detach') ?></md-tooltip>
                        </md-button>
                    <?php
                        }
                    ?>
                    
                    <!-- Delete super tag -->
                    <?php
                        if (!count($children) && !count($all_tags_links[$parent])) {
                    ?>
                        <md-button class="md-icon-button" aria-label="<?= __('delete') ?>" ng-href="<?= $deleteUrl ?>">
                            <md-icon>delete</md-icon>
                            <md-tooltip><?= __('Delete') ?></md-tooltip>
                        </md-button>
                    <?php
                        }
                    ?>
                </span>
            </md-card-icon-actions>
            <?php
                if (count($children) > 0) {
            ?>
                <md-button ng-click="vm.expandOrCollapse(<?= $parent ?>)" ng-cloak>
                    <md-icon>{{vm.hiddenReplies[<?= $parent ?>] ? 'expand_more' : 'expand_less'}}</md-icon>
                    <span ng-if="!vm.hiddenReplies[<?= $parent ?>]">
                        <?= __('hide branch') ?>
                    </span>
                    <span ng-if="vm.hiddenReplies[<?= $parent ?>]">
                        <?= __('show branch') ?>
                    </span>
                </md-button>
            <?php 
                }
            ?>
        </md-card-actions>

        <!-- Attached tags -->
        <?php
            foreach ($all_tags_links[$parent] as $tag) {
                $unbindUrl = $this->Url->build([
                    'controller' => 'tagsSuperTags',
                    'action' => 'removeTagSuperTag',
                    $parent,
                    $tag,
                    'tag'
                ]);
        ?>
            <span class='tag'>
                <?= $all_tags[$tag]['name'] ?>
                <md-button class="md-icon-button" aria-label="<?= __('detach') ?>" ng-href="<?= $unbindUrl ?>">
                    <md-icon>reply</md-icon>
                    <md-tooltip><?= __('Detach') ?></md-tooltip>
                </md-button>
            </span>
        <?php
            }
        ?>

        <!-- Attached super tags -->
        <?php
            if (count($children) > 0) {
        ?>
            <div ng-if="!vm.hiddenReplies[<?= $parent ?>]">
                <?php
                    foreach ($children as $new_parent => $new_children) {
                        echo $this->element('superTag', [
                            'root' => $parent,
                            'depth' => $depth + 1,
                            'parent' => $new_parent,
                            'children' => $new_children,                 
                        ]);
                    }
                ?>
            </div>
        <?php
            }
        ?>
    </md-card-content>
</md-card>