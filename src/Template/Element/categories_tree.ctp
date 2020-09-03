<?php
use App\Lib\LanguagesLib;

// root, depth, category
$categoryId = $category['id'];
$categoryName = $category['name'];
$categoryChildren = $category['children'];
$categoryTags = (array_key_exists($categoryId, $tags)) ? $tags[$categoryId] : [];

$deleteUrl = $this->Url->build([
    'controller' => 'categories_tree',
    'action' => 'removeCategory',
    $categoryId
]);

?>

<md-card class="comment <?= ($depth == 0) ? 'wall-thread' : 'reply' ?>" flex>
    <md-card-content>
        <md-card-actions layout="row" layout-align="end center">
            <md-card-icon-actions>
                <span>
                <?= $this->safeForAngular($categoryName) ?>                   
                    <!-- Delete tags category -->
                    <?php
                        if (!count($categoryChildren) && !count($categoryTags)) {
                    ?>
                        <md-button class="md-icon-button" aria-label="<?= __d('beta', 'delete') ?>" ng-href="<?= $deleteUrl ?>">
                            <md-icon>delete</md-icon>
                            <md-tooltip><?= __d('beta', 'Delete') ?></md-tooltip>
                        </md-button>
                    <?php
                        }
                    ?>
                </span>
            </md-card-icon-actions>
            <?php
                if (count($categoryChildren) > 0) {
            ?>
                <md-button ng-click="vm.expandOrCollapse(<?= $categoryId ?>)" ng-cloak>
                    <md-icon>{{vm.displayedBranches[<?= $categoryId ?>] ? 'expand_less' : 'expand_more'}}</md-icon>
                    <span ng-if="vm.displayedBranches[<?= $categoryId ?>]">
                        <?= __d('beta', 'hide branch') ?>
                    </span>
                    <span ng-if="!vm.displayedBranches[<?= $categoryId ?>]">
                        <?= __d('beta', 'show branch') ?>
                    </span>
                </md-button>
            <?php 
                }
            ?>
        </md-card-actions>

        <!-- Attached tags -->
        <?php
            foreach ($categoryTags as $tag) {
                $unbindUrl = $this->Url->build([
                    'controller' => 'categories_tree',
                    'action' => 'detachTagFromCategory',
                    $tag['id']
                ]);
        ?>
            <span class='tag'>
                <?= $this->safeForAngular($tag['name']).' ('.$tag['nbrOfSentences'].')' ?>
                <md-button class="md-icon-button" aria-label="<?= __d('beta', 'detach') ?>" ng-href="<?= $unbindUrl ?>">
                    <md-icon>reply</md-icon>
                    <md-tooltip><?= __d('beta', 'Detach') ?></md-tooltip>
                </md-button>
            </span>
        <?php
            }
        ?>

        <!-- Attached tags categories -->
        <?php
            if (count($categoryChildren) > 0) {
        ?>
            <div ng-if="vm.displayedBranches[<?= $categoryId ?>]">
                <?php
                    foreach ($categoryChildren as $subcategory) {
                        echo $this->element('categories_tree', [
                            'root' => $categoryId,
                            'depth' => $depth + 1,
                            'category' => $subcategory            
                        ]);
                    }
                ?>
            </div>
        <?php
            }
        ?>
    </md-card-content>
</md-card>
