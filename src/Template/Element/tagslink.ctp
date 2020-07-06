<?php
use App\Lib\LanguagesLib;

// $labels, $depth, $parent, $children

$removeUrl = $this->Url->build([
    'controller' => 'tagsLinks',
    'action' => 'remove',
    $root,
    $parent
]);

$cssClass = $depth == 0 ? 'wall-thread' : 'reply'
?>

<md-card class="comment <?= $cssClass ?>" flex>
    <md-card-content>
        <md-card-actions layout="row" layout-align="end center">
            <md-card-icon-actions>
                <span class='tag'><?= $labels[$parent][0] ?></span>
            </md-card-icon-actions>
            <?php
            if ($depth > 0 && count($children) == 0) { ?>
                <md-button class="md-icon-button" aria-label="<?= __('delete') ?>"
                            ng-href="<?= $removeUrl ?>">
                    <md-icon>delete</md-icon>
                    <md-tooltip><?= __('Delete') ?></md-tooltip>
                </md-button>
            <?php
            }
            elseif (count($children) > 0) { ?>
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
        <?php if (count($children) > 0) { ?>
            <div ng-if="!vm.hiddenReplies[<?= $parent ?>]">
                <?php
                    foreach ($children as $new_parent => $new_children) {
                        echo $this->element('tagslink', [
                            'root' => $parent,
                            'parent' => $new_parent,
                            'children' => $new_children,
                            'labels' => $labels,
                            'depth' => $depth + 1
                        ]);
                    }
                ?>
            </div>
        <?php } ?>
    </md-card-content>
</md-card>