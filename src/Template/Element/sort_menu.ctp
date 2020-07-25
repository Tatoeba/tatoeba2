<md-menu md-offset="5 50" md-position-mode="target-right target">
    <md-button ng-click="$mdOpenMenu($event)">
        <md-icon>sort</md-icon> Sort by
    </md-button>
    <md-menu-content ng-cloak>

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
                    <span style="padding-right: 12px">
                        <?php echo $this->Paginator->sort($option['param'], $option['label'], ['direction'=>$option['direction'], 'lock'=>true]); ?>
                    </span>
                </md-button>
            </md-menu-item>

        <?php
            } 
        ?>

    </md-menu-content>
</md-menu>