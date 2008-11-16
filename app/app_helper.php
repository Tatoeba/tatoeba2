<?php 
class AppHelper extends Helper { 
    function url($url = null, $full = false) { 
        if (isset($this->params['lang']) && is_array($url)) { 
            $url['lang'] = $this->params['lang']; 
        } 
        return parent::url($url, $full); 
    } 
} 
?>