<md-menu md-offset="5 50" md-position-mode="target-right target" ng-cloak>
    <md-button ng-click="$mdOpenMenu($event)">
        <md-icon>sort</md-icon> <?php echo __('Sort by'); ?>
    </md-button>
    <md-menu-content>

        <?php 
            foreach($options as $option) {
        ?>

            <md-menu-item>
                <md-button>
                    <?php 
                        if ($this->Paginator->sortKey() === $option['param'] && $this->Paginator->sortDir() === $option['direction']){
                    ?>
                        <md-icon>check</md-icon>
                        <span class="sortOption">
                            <?php echo $option['label']; ?>
                        </span>
                    <?php
                        }
                        else {
                    ?>
                        <md-icon>blank</md-icon>
                        <span class="sortOption">
                            <?php echo $this->Paginator->sort($option['param'], $option['label'], ['direction'=>$option['direction']]); ?>
                        </span>

                    <?php
                        }
                    ?>
                </md-button>
            </md-menu-item>

        <?php
            } 
        ?>

    </md-menu-content>
</md-menu>