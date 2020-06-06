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
 * @link     https://tatoeba.org
 */

use Cake\Core\Configure;

// Detecting language for "browse by language"
$session = $this->request->getSession();
$currentLanguage = $session->read('browse_sentences_in_lang');
$showTranslationsInto = $session->read('show_translations_into_lang');
$filteredLanguage = $session->read('vocabulary_requests_filtered_lang');

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
    /* @translators: menu on the top (verb) */
    __('Browse') => array(
        "route" => array(
            "controller" => false,
            "action" => false
        ),
        "sub-menu" => array(
            /* @translators: menu item on the top (verb) */
            __('Show random sentence') => array(
                "controller" => "sentences",
                "action" => "show",
                "random"
            ),
            /* @translators: menu item on the top (verb) */
            __('Browse by language') => array(
                "controller" => "sentences",
                "action" => "show_all_in",
                $currentLanguage, $showTranslationsInto
            ),
            /* @translators: menu item on the top (verb) */
            __('Browse by list') => array(
                "controller" => "sentences_lists",
                "action" => "index"
            ),
            /* @translators: menu item on the top (verb) */
            __('Browse by tag') => array(
                "controller" => "tags",
                "action" => "view_all"
            ),
            /* @translators: menu item on the top (verb) */
            __('Browse audio') => array(
                "controller" => "audio",
                "action" => "index"
            )
        )
    ),
    /* @translators: menu on the top (verb) */
    __('Contribute') => array(
        "route" => array(
            "controller" => false,
            "action" => false
        ),
        "sub-menu" => array(
            /* @translators: menu item on the top (verb) */
            __('Add sentences') => array(
                "controller" => "sentences",
                "action" => "add"
            ),
            /* @translators: menu item on the top (verb) */
            __('Translate sentences') => array(
                "controller" => "activities",
                "action" => "translate_sentences"
            ),
            /* @translators: menu item on the top (verb) */
            __('Adopt sentences') => array(
                "controller" => "activities",
                "action" => "adopt_sentences",
                $currentLanguage
            ),
            /* @translators: menu item on the top (verb) */
            __('Improve sentences') => array(
                "controller" => "activities",
                "action" => "improve_sentences"
            ),
            /* @translators: menu item on the top (verb) */
            __('Discuss sentences') => array(
                "controller" => "sentence_comments",
                "action" => "index"
            ),
            /* @translators: menu item on the top (verb) */
            __('Add vocabulary request') => array(
                "controller" => "vocabulary",
                "action" => "add_sentences",
                $filteredLanguage
            ),
        )
    ),
    /* @translators: menu on the top (verb) */
    __('Community') => array(
        "route" => array(
            "controller" => false,
            "action" => false
        ),
        "sub-menu" => array(
            /* @translators: menu item on the top (verb) */
            __('Wall') => array(
                "controller" => "wall",
                "action" => "index"
            ),
            /* @translators: menu item on the top (verb) */
            __('List of all members') => array(
                "controller" => "users",
                "action" => "all"
            ),
            /* @translators: menu item on the top (verb) */
            __('Languages of members') => array(
                "controller" => "stats",
                "action" => "users_languages"
            ),
            /* @translators: menu item on the top (verb) */
            __('Native speakers') => array(
                "controller" => "stats",
                "action" => "native_speakers"
            )
        )
    )
);

?>

<div id="top_menu_container" ng-controller="MenuController">
    <div id="top_menu" layout="column" layout-gt-xs="row" layout-align-gt-xs="start center" flex ng-cloak>
        <?= $this->element('header'); ?>
        
        <ul id="navigation_menu" flex>
        <?php
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

        <div id="user_menu" ng-cloak>
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

        <md-button class="ui-lang-select md-icon-button" ng-click="showInterfaceLanguageSelection()">
            <md-icon>language</md-icon>
        </md-button>
    </div>

    <md-sidenav class="md-sidenav-left" md-component-id="menu" md-disable-scroll-target="body" ng-cloak>
        <md-content>
            <?php
            $name = __('Tatoeba');
            
            $logo = $this->Html->image(
                IMG_PATH . 'tatoeba.svg',
                array(
                    'width' => 32,
                    'height' => 32,
                    'title' => $name

                )
            );
            ?>
            <div layout="row" layout-align="start center" layout-padding>
                <md-button class="md-icon-button" ng-click="toggleMenu()">
                    <md-icon>menu</md-icon>
                </md-button>
                <div layout="row" layout-align="center center">
                    <span><?= $logo ?></span>
                    <span class="tatoeba-name"><?= $name ?></span>
                </div>
            </div>

            <md-list>
            <?php
            foreach ($menuElements as $title => $data) {
                $route = $data['route'];
                
                if (!empty($data['sub-menu'])) {
                    ?>
                    <md-subheader><?= $title ?></md-subheader>
                    <?php
                }
                
                // Sub-menu
                if (!empty($data['sub-menu'])) {
                    foreach ($data['sub-menu'] as $title2 => $route2) {
                        ?>
                        <md-list-item href="<?= $this->Url->build($route2) ?>">
                            <p>
                            <md-icon>chevron_right</md-icon>
                            <?= $title2 ?>
                            </p>
                        </md-list-item>
                        <?php
                    }
                    
                }
                
            }
            ?>
            </md-list>
        </md-content>

    </md-sidenav>
</div>


