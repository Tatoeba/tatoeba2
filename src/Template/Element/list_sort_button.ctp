<md-menu-item>
    <md-button>
        <md-icon>
            <?php 
                if (strcmp($this->Paginator->sortKey(),$param) == 0 && strcmp($this->Paginator->sortDir(),$direction) == 0) {
                    echo 'check'; 
                }
                else {
                    echo 'blank';
                }
            ?>
        </md-icon>
        <span style="padding-right: 12px">
            <?php echo $this->Paginator->sort($param, __($label), ['direction'=>$direction, 'lock'=>true]); ?>
        </span>
    </md-button>
</md-menu-item>