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
$currentLanguage = $session->read('browse_sentences_in_lang');
$showTranslationsInto = $session->read('show_translations_into_lang');
$notTranslatedInto = $session->read('not_translated_into_lang');
$filterAudioOnly = $session->read('filter_audio_only');

if (empty($currentLanguage)) {
    $currentLanguage = $session->read('random_lang_selected');
}
if (empty($currentLanguage) || $currentLanguage == 'und') {
    $currentLanguage = $this->params['lang'];
}
if (empty($showTranslationsInto)) {
    $showTranslationsInto = 'none';
}
if (empty($notTranslatedInto)) {
    $notTranslatedInto = 'none';
}

// array containing the elements of the menu : $title => $route
$menuElements = array(
    __('Browse', true) => array(
        "route" => array(
            "controller" => null,
            "action" => null
        ),
        "sub-menu" => array(
            __('Random sentence', true) => array(
                "controller" => "sentences",
                "action" => "show",
                "random"
            ),
            __('Browse by language', true) => array(
                "controller" => "sentences",
                "action" => "show_all_in",
                $currentLanguage, $showTranslationsInto, $notTranslatedInto,
                $filterAudioOnly
            ),
            __('Browse by list', true) => array(
                "controller" => "sentences_lists",
                "action" => "index"
            ),
            __('Browse by tag', true) => array(
                "controller" => "tags",
                "action" => "view_all"
            ),
            __('Browse audio', true) => array(
                "controller" => "audios",
                "action" => "index"
            )
        )
    ),
    __('Contribute', true) => array(
        "route" => array(
            "controller" => "pages",
            "action" => "contribute"
        ),
        "sub-menu" => array(
            __('Add sentences', true) => array(
                "controller" => "sentences",
                "action" => "add"
            ),
            __('Translate sentences', true) => array(
                "controller" => "activities",
                "action" => "translate_sentences"
            ),
            __('Adopt sentences', true) => array(
                "controller" => "activities",
                "action" => "adopt_sentences",
                $currentLanguage
            ),
            __('Improve sentences', true) => array(
                "controller" => "activities",
                "action" => "improve_sentences"
            ),
            __('Discuss sentences', true) => array(
                "controller" => "sentence_comments",
                "action" => "index"
            ),
            __('Show activity timeline', true) => array(
                "controller" => "contributions",
                "action" => "activity_timeline"
            )
        )
    ),
    __('Community', true) => array(
        "route" => array(
            "controller" => null,
            "action" => null
        ),
        "sub-menu" => array(
            __('Wall', true) => array(
                "controller" => "wall",
                "action" => "index"
            ),
            __('List of all members', true) => array(
                "controller" => "users",
                "action" => "all"
            ),
            __('Languages of members', true) => array(
                "controller" => "stats",
                "action" => "users_languages"
            ),
            __('Native speakers', true) => array(
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
            $cssClass = 'menuSection ';
            
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
                    $title .= $html->image(
                        IMG_PATH . 'arrow_down.svg',
                        array(
                            "height" => 12,
                            "width" => 12
                        )
                    );
                }
                
                if ($route['controller'] == null) {
                    echo '<a class="'.$cssClass.'">'.$title.'</a>';
                } else {
                    echo $html->link(
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
                        $newTab = null;
                        if (!is_array($route2)) {
                            $newTab = array('onclick' => "window.open(this.href,'_blank');return false;");
                        }
                        echo '<li>';
                        echo $html->link($title2, $route2, $newTab);
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

    </div>
</div>
