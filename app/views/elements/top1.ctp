<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

// Detecting language for "browse by language"
$currentLanguage = $session->read('random_lang_selected');
if (empty($currentLanguage) || $currentLanguage == 'und') {
    $currentLanguage = $this->params['lang'];
}

// array containing the elements of the menu : $title => $route
$menuElements = array(
    __('Home', true) => array(
        "route" => array(
            "controller" => "pages",
            "action" => "home"
        )
    ),
    __('Browse', true) => array(
        "route" => array(
            "controller" => "sentences",
            "action" => "show",
            "random"
        ),
        "sub-menu" => array(
            __('Browse by language', true) => array(
                "controller" => "sentences",
                "action" => "show_all_in",
                $currentLanguage, "none", "none"
            ),
            __('Browse by list', true) => array(
                "controller" => "sentences_lists",
                "action" => "index"
            ),
            __('Browse by tag', true) => array(
                "controller" => "tags",
                "action" => "view_all"
            )
        )
    ),
    __('Contribute', true) => array(
        "route" => array(
            "controller" => "pages",
            "action" => "contribute"
        )
    ),
    __('Members', true) => array(
        "route" => array(
            "controller" => "users",
            "action" => "all"
        )
    ),
    __('Wall', true) => array(
        "route" => array(
            "controller" => "wall",
            "action" => "index"
        )
    ),
    __('What\'s new', true) => array(
        "route" => array(
            "controller" => "pages",
            "action" => "whats_new"
        )
    )
);

?>

<div id="top_menu_container">
    <div id="top_menu">
        <div id="languageSelectionContainer">
        <?php echo $this->element('interface_language'); ?>
        </div>
        
        <div id="user_menu">
        <?php
        // User menu    
        if (!$session->read('Auth.User.id')) {
            $isOnLoginPage = ($this->params['controller'] == 'users')
            && ($this->params['action'] == 'login');
            
            if (!$isOnLoginPage) {
                echo $this->element('login');
            }
        } else {
            echo $this->element('space');
        }
        ?>
        </div>
        
        
        <ul id="navigation_menu">
        <?php
        // current path param
        $param = '';
        if (isset($this->params['pass'][0])) {
            $param = $this->params['pass'][0];
        };
        
        // current path action
        $action = $this->params['action'];
        if ($action == 'display') {
            $action = $param;
        }
        
        // current path controller
        $controller = $this->params['controller'];
        
        foreach ($menuElements as $title => $data) {
            
            $route = $data['route'];
            $cssClass = '';
            
            // General case
            if ($controller == $route['controller'] && $action == $route['action']) {
                $cssClass .= 'show';
            }
            
            // Special case for homepage
            if ($controller == 'pages' && $action == 'index'
                && $route['action'] == 'home') {
                $cssClass .= 'show';
            }
            
            // displaying <li> element
            ?>
            <li class='menuItem'>
                <?php
                if (!empty($data['sub-menu'])) {
                    $title .= $html->image('arrow_down.png');
                }
                echo $html->link(
                    $title, 
                    $route, 
                    array(
                        "class" => $cssClass,
                        "escape" => false
                    )
                );
                
                // Sub-menu
                if (!empty($data['sub-menu'])) {
                    echo "<ul class='sub-menu'>";
                    foreach ($data['sub-menu'] as $title2 => $route2) {
                        echo '<li>';
                        echo $html->link($title2, $route2);
                        echo '</li>';
                    }
                    echo "</ul>";
                }
                ?>
            </li>
            <?php
        }
        ?>
        </ul>
        
    </div>
</div>
