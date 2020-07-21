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
            <?php  
                $options = array('defaultOrders' => array($param => $direction));
                /* @translators: sort option in the "List of lists" page */
                echo $this->Pagination->sortDefaultOrder($label, $param, $options);
            ?>
        </span>
    </md-button>
</md-menu-item>