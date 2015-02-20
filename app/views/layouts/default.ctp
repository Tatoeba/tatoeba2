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
<html lang="<?php echo $languages->langAttribute($languages->i18nCodeToISO(Configure::read('Config.language'))); ?>">
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
        echo $html->css(CSS_PATH . 'layouts/default.css');
        echo $html->css(CSS_PATH . 'layouts/elements.css');
        
        // Specific
        $controller = $this->params["controller"];
        $action = $this->params["action"];
        
        
        echo $html->css(CSS_PATH . $controller."/".$action .".css"); 
        
        
        // Special case for Chrome and furigana.
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $isChrome = (strpos($browser, "Chrome")) !== false;
        if (!$isChrome) {
            echo $html->css(CSS_PATH . "elements/furigana.css"); 
        }
        
        // Develop site override
        if (Configure::read('Tatoeba.devStylesheet')) { ?>
            <style>
                body {
                    background-image:url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIHdpZHRoPSczMDAnIGhlaWdodD0nMzAwJyB2aWV3Qm94PScwIDAgMzAwIDMwMCc+Cgk8ZGVmcz4KCQk8cGF0dGVybiBpZD0nYmx1ZXN0cmlwZScgcGF0dGVyblVuaXRzPSd1c2VyU3BhY2VPblVzZScgeD0nMCcgeT0nMCcgd2lkdGg9JzIwJyBoZWlnaHQ9JzIwJyB2aWV3Qm94PScwIDAgNDAgNDAnID4KCQk8cmVjdCB3aWR0aD0nMTEwJScgaGVpZ2h0PScxMTAlJyBmaWxsPScjZmZmZmZmJy8+CgkJCTxwYXRoIGQ9J00xLDFoNDB2NDBoLTQwdi00MCcgZmlsbC1vcGFjaXR5PScwJyBzdHJva2Utd2lkdGg9JzEnIHN0cm9rZS1kYXNoYXJyYXk9JzAsMSwxJyBzdHJva2U9JyNjY2NjY2MnLz4KCQk8L3BhdHRlcm4+IAoJCTxmaWx0ZXIgaWQ9J2Z1enonIHg9JzAnIHk9JzAnPgoJCQk8ZmVUdXJidWxlbmNlIHR5cGU9J3R1cmJ1bGVuY2UnIHJlc3VsdD0ndCcgYmFzZUZyZXF1ZW5jeT0nLjIgLjMnIG51bU9jdGF2ZXM9JzUnIHN0aXRjaFRpbGVzPSdzdGl0Y2gnLz4KCQkJPGZlQ29sb3JNYXRyaXggdHlwZT0nc2F0dXJhdGUnIGluPSd0JyB2YWx1ZXM9JzAnLz4KCQk8L2ZpbHRlcj4KCTwvZGVmcz4KCTxyZWN0IHdpZHRoPScxMDAlJyBoZWlnaHQ9JzEwMCUnIGZpbGw9J3VybCgjYmx1ZXN0cmlwZSknLz4KPHJlY3Qgd2lkdGg9JzEwMCUnIGhlaWdodD0nMTAwJScgZmlsdGVyPSd1cmwoI2Z1enopJyBvcGFjaXR5PScwLjEnLz4KPC9zdmc+Cg==');
                }
                #top_menu_container { background-color: #cf0000; }
                div.search_bar:after {
                    content: "<?php __("Warning: this website is for testing purposes. Everything you submit will be definitely lost.")?>";
                    position: absolute;
                    color: #cf0000;
                    margin-left: 92px;
                    font-size: 15px;
                }
            </style>
<?php      }

        // ---------------------- //
        //      Javascript        //
        // ---------------------- //
        echo $javascript->link(JS_PATH . 'jquery-1.4.min.js', true);
        echo $javascript->link(JS_PATH . 'generic_functions.js', true);

        // Enhanced dropdown for language selection
        // It's needed on every page since it's used on the search bar
        $isChosenSelectEnabled = $session->read('jquery_chosen');
        if (CurrentUser::isMember() && $isChosenSelectEnabled)
        {
            echo $javascript->link(JS_PATH . 'chosen.jquery.min.js', true);
            echo $javascript->codeBlock(
                '$(document).ready(function(){'.
                    '$(".language-selector").chosen({'.
                        'inherit_select_classes: true,'.
                        'search_contains: true,'. /* helps languages without spaces */
                        'no_results_text: ' . json_encode(__('No language matches', true)).
                    '});'.
                '});',
                array('allowCache' => false));  
        }

        echo $scripts_for_layout;
        
        echo $this->element('seo_international_targeting');
    ?>
    
    <link rel="search" type="application/opensearchdescription+xml" href="http://tatoeba.org/opensearch.xml" title="Tatoeba project" />
</head>
<body>
    <div id="audioPlayer"></div>
    
    <!--  TOP  -->
    <?php echo $this->element('top_menu'); ?>


    <div id="container">
        <!--  Logo  -->
        <?php echo $this->element('header'); ?>
        
        <!--  SEARCH BAR  -->
        <?php
        echo $this->element('search_bar', array(
            'selectedLanguageFrom' => $session->read('search_from'),
            'selectedLanguageTo' => $session->read('search_to'),
            'searchQuery' => $session->read('search_query'),
            'cache' => array(
                // Only use cache when search fields are not prefilled
                'time' => is_null($session->read('search_from'))
                          && is_null($session->read('search_to'))
                          && is_null($session->read('search_query'))
                          ? '+1 day' : false,
                'key' => Configure::read('Config.language')
            )
        )); ?>
            
        <!--  CONTENT -->
        <div id="content">
            <?php
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


        <!--  FOOT -->
        <?php
        echo $this->element('foot');
        ?>
    </div>
    <?php echo $this->element('sql_dump'); ?>

    <?php
    if (Configure::read('GoogleAnalytics.enabled')) {
        echo $this->element('google_analytics', array('cache' => true));
    }
    ?>
</body>
</html>
