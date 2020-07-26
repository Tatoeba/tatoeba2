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
                    <md-icon>
                        <?php 
                            //'modified' + 'desc' parameter is not detected in show_angular and sentences of user
                            if ($this->Paginator->sortKey() === $option['param'] && $this->Paginator->sortDir() === $option['direction']){
                                echo 'check'; 
                            }
                            else {
                                echo 'blank';
                            }
                        ?>
                    </md-icon>
                    <span class="sortOption">
                        <?php echo $this->Paginator->sort($option['param'], $option['label'], ['direction'=>$option['direction'], 'lock'=>true]); ?>
                    </span>
                </md-button>
            </md-menu-item>

        <?php
            } 
        ?>

    </md-menu-content>
</md-menu>