<?php
/* SVN FILE: $Id: default.ctp 7118 2008-06-04 20:49:29Z gwoo $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                1785 E. Sahara Avenue, Suite 490-204
 *                                Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright        Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link                http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package            cake
 * @subpackage        cake.cake.libs.view.templates.layouts
 * @since            CakePHP(tm) v 0.10.0.1076
 * @version            $Revision: 7118 $
 * @modifiedby        $LastChangedBy: gwoo $
 * @lastmodified    $Date: 2008-06-04 13:49:29 -0700 (Wed, 04 Jun 2008) $
 * @license            http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<!DOCTYPE html>
<html lang="<?php echo LanguagesLib::languageTag(Configure::read('Config.language')); ?>">
<head>
    <?php echo $html->charset(); ?>
    <title>
        <?php echo $title_for_layout; ?>
    </title>
    <?php
        echo $html->meta('icon');
        
        // ---------------------- //
        //          CSS           //
        // ---------------------- //
        // Only two CSS files are loaded. One that is generic, and one that is
        // specific to the view. The specific CSS file is auto-loaded. It must be 
        // named with name of the view it is linked to, and put it in a folder with 
        // the name of the controller.
        
        // Generic
        echo $html->css(CSS_PATH . 'angular-material.min.css');
        echo $html->css(CSS_PATH . 'layouts/default.css');
        echo $html->css(CSS_PATH . 'layouts/elements.css');
        
        // Specific
        $controller = $this->params["controller"];
        $action = $this->params["action"];
        echo $html->css(CSS_PATH . $controller."/".$action .".css");

        echo $this->element('seo_international_targeting');
    ?>

    <link rel="search" type="application/opensearchdescription+xml"
          href="/opensearch.xml" title="Tatoeba" />
</head>
<body ng-app="app" ng-cloak>
    <div id="audioPlayer"></div>
    
    <!--  TOP  -->
    <?php echo $this->element('top_menu'); ?>

    <!--  SEARCH BAR  -->
    <?php
    $isHomepage = $controller == 'pages' && $action == 'index';
    if (CurrentUser::isMember() || !$isHomepage) {
        echo $this->element('search_bar', array(
            'selectedLanguageFrom' => $session->read('search_from'),
            'selectedLanguageTo' => $session->read('search_to'),
            'searchQuery' => $session->read('search_query'),
            'cache' => array(
                // Only use cache when search fields are not prefilled
                'time' => is_null($session->read('search_from'))
                && is_null($session->read('search_to'))
                && is_null($session->read('search_query'))
                && !$languages->preferredLanguageFilter()
                    ? '+1 day' : false,
                'key' => Configure::read('Config.language')
            )
        ));
    } else {
        echo $this->element('short_description', array(
            'cache' => array(
                'time' => '+1 day',
                'key' => Configure::read('Config.language')
            )
        ));
    }
    ?>
            
    <!--  CONTENT -->
    <div id="content">
        <div class="container">
        <?php
        echo $this->element('announcement');
        if($session->check('Message.flash')){
            echo $session->flash();
        }

        echo $content_for_layout;
        ?>

        <!--
            Quick fix to readjust the size of the container when
            the main content is smaller than the annexe content.
        -->
        <div style="clear:both"></div>
        </div>
    </div>

    <!--  FOOT -->
    <?php
    echo $this->element('foot');
    echo $this->element('sql_dump');

    echo $javascript->link(JS_PATH . 'jquery-1.11.3.min.js');
    echo $javascript->link(JS_PATH . 'angular/angular.min.js');
    echo $javascript->link(JS_PATH . 'angular/angular-animate.min.js');
    echo $javascript->link(JS_PATH . 'angular/angular-aria.min.js');
    echo $javascript->link(JS_PATH . 'angular/angular-material.min.js');
    echo $javascript->link(JS_PATH . 'angular/angular-messages.min.js');
    echo $javascript->link(JS_PATH . 'responsive/app.module.js');


    echo $javascript->link(JS_PATH . 'watch.js', true);
    echo $javascript->link(JS_PATH . 'generic_functions.js');
    // Source: https://github.com/jonathantneal/svg4everybody
    // This is needed to make "fill: currentColor" work on every browser.
    echo $javascript->link(JS_PATH . 'svg4everybody.min.js');

    if (CurrentUser::getSetting('copy_button')) {
        echo $javascript->link(JS_PATH . 'clipboard.min.js');
        echo $javascript->link(JS_PATH . 'sentences.copy.js');
    }

    if (Configure::read('Announcement.enabled') || Configure::read('Tatoeba.devStylesheet')) {
        echo $javascript->link(JS_PATH . 'jquery.cookie.js');
        echo $javascript->link(JS_PATH . 'announcement.js');
    }

    echo $scripts_for_layout;

    if (Configure::read('GoogleAnalytics.enabled')) {
        echo $this->element('google_analytics', array('cache' => true));
    }
    ?>
</body>
</html>
