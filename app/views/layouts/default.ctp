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
<html>
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
        
        // ---------------------- //
        //      Javascript        //
        // ---------------------- //
        echo $javascript->link(JS_PATH . 'jquery-1.4.min.js', true);
        echo $javascript->link(JS_PATH . 'generic_functions.js?2', true);

        // Enhanced dropdown for language selection
        // It's needed on every page since it's used on the search bar
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

        echo $scripts_for_layout;
        
    ?>
    
    <link rel="search" type="application/opensearchdescription+xml" href="http://tatoeba.org/opensearch.xml" title="Tatoeba project" />
</head>
<body>
    <?php
    if (Configure::read('GoogleAnalytics.enabled')) {
        echo $this->element('google_analytics', array('cache' => true));
    }
    ?>
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
                $session->flash();
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
    <?php echo $cakeDebug ?>
</body>
</html>
