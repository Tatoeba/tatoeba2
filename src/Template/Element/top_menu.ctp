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

use Cake\Core\Configure;

// Detecting language for "browse by language"
$session = $this->request->getSession();
$currentLanguage = $session->read('browse_sentences_in_lang');
$showTranslationsInto = $session->read('show_translations_into_lang');

if (empty($currentLanguage)) {
    $currentLanguage = $session->read('random_lang_selected');
}
if (empty($currentLanguage) || $currentLanguage == 'und') {
    $currentLanguage = Configure::read('Config.language');
}
if (empty($showTranslationsInto)) {
    $showTranslationsInto = 'none';
}

// array containing the elements of the menu : $title => $route
$menuElements = array(
    __('Browse') => array(
        "route" => array(
            "controller" => false,
            "action" => false
        ),
        "sub-menu" => array(
            __('Random sentence') => array(
                "controller" => "sentences",
                "action" => "show",
                "random"
            ),
            __('Browse by language') => array(
                "controller" => "sentences",
                "action" => "show_all_in",
                $currentLanguage, $showTranslationsInto
            ),
            __('Browse by list') => array(
                "controller" => "sentences_lists",
                "action" => "index"
            ),
            __('Browse by tag') => array(
                "controller" => "tags",
                "action" => "view_all"
            ),
            __('Browse audio') => array(
                "controller" => "audio",
                "action" => "index"
            )
        )
    ),
    __('Contribute') => array(
        "route" => array(
            "controller" => false,
            "action" => false
        ),
        "sub-menu" => array(
            __('Add sentences') => array(
                "controller" => "sentences",
                "action" => "add"
            ),
            __('Translate sentences') => array(
                "controller" => "activities",
                "action" => "translate_sentences"
            ),
            __('Adopt sentences') => array(
                "controller" => "activities",
                "action" => "adopt_sentences",
                $currentLanguage
            ),
            __('Improve sentences') => array(
                "controller" => "activities",
                "action" => "improve_sentences"
            ),
            __('Discuss sentences') => array(
                "controller" => "sentence_comments",
                "action" => "index"
            ),
            __('Show activity timeline') => array(
                "controller" => "contributions",
                "action" => "activity_timeline"
            )
        )
    ),
    __('Community') => array(
        "route" => array(
            "controller" => false,
            "action" => false
        ),
        "sub-menu" => array(
            __('Wall') => array(
                "controller" => "wall",
                "action" => "index"
            ),
            __('List of all members') => array(
                "controller" => "users",
                "action" => "all"
            ),
            __('Languages of members') => array(
                "controller" => "stats",
                "action" => "users_languages"
            ),
            __('Native speakers') => array(
                "controller" => "stats",
                "action" => "native_speakers"
            )
        )
    )
);

?>

<div id="top_menu_container">
    <div id="top_menu">
        <ul id="navigation_menu">
        <?php
        echo $this->element('header');

        // currenot path param
        $pass = $this->request->getParam('pass');
        $param = '';
        if (!empty($pass)) {
            $param = $pass[0];
        };
        
        // current path action
        $action = $this->request->getParam('action');
        if ($action == 'display') {
            $action = $param;
        }
        
        // current path controller
        $controller = $this->request->getParam('controller');
        
        foreach ($menuElements as $title => $data) {
            
            $route = $data['route'];
            $cssClass = 'menuSection ';
            
            // General case
            if ($controller === $route['controller'] && $action === $route['action']) {
                $cssClass .= 'show';
            }
            
            // displaying <li> element
            ?>
            <li class='menuItem'>
                <?php
                if (!empty($data['sub-menu'])) {
                    $title .= $this->Html->image(
                        IMG_PATH . 'arrow_down.svg',
                        array(
                            "height" => 12,
                            "width" => 12
                        )
                    );
                }
                
                if ($route['controller'] === false) {
                    echo '<a class="'.$cssClass.'">'.$title.'</a>';
                } else {
                    echo $this->Html->link(
                        $title, 
                        $route, 
                        array(
                            "class" => $cssClass,
                            "escape" => false
                        )
                    );
                }
                
                // Sub-menu
                if (!empty($data['sub-menu'])) {
                    echo "<ul class='sub-menu'>";
                    foreach ($data['sub-menu'] as $title2 => $route2) {
                        $newTab = array();
                        if (!is_array($route2)) {
                            $newTab = array('onclick' => "window.open(this.href,'_blank');return false;");
                        }
                        echo '<li>';
                        echo $this->Html->link($title2, $route2, $newTab);
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

        <div id="languageSelectionContainer">
            <?php echo $this->element('interface_language'); ?>
        </div>

        <div id="user_menu">
            <?php
            // User menu
            if (!$session->read('Auth.User.id')) {
                $isOnLoginPage = ($controller == 'Users' && $action == 'login');

                if (!$isOnLoginPage) {
                    echo $this->element('login');
                }
            } else {
                echo $this->element('space');
            }
            ?>
        </div>

    </div>
</div>
