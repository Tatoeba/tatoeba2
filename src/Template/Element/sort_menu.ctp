<md-menu md-offset="5 50" md-position-mode="target-right target" ng-cloak>
    <md-button ng-click="$mdOpenMenu($event)">
        <?php /* @translators: button to open top-right sort menu on various pages (verb) */ ?>
        <md-icon>sort</md-icon> <?php echo __('Sort by'); ?>
    </md-button>
    <md-menu-content>

        <?php 
            foreach($options as $option) {
                $icon = 'blank';
                $url = $this->Paginator->generateUrl(['sort'=>$option['param'], 'direction'=>$option['direction']]);
                if ($this->Paginator->sortKey() === $option['param'] && $this->Paginator->sortDir() === $option['direction']){
                    $icon = 'check';
                    $url = '#'; 
                }
        ?>

            <md-menu-item>
                <md-button href="<?php echo $url; ?>">
                        <md-icon>
                            <?php echo $icon; ?>
                        </md-icon>
                        <span class="sortOption">
                            <?php echo $option['label']; ?>
                        </span>
                </md-button>
            </md-menu-item>

        <?php
            } 
        ?>

    </md-menu-content>
</md-menu>
