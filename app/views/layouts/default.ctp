<?php
/* SVN FILE: $Id: default.ctp 7118 2008-06-04 20:49:29Z gwoo $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.view.templates.layouts
 * @since			CakePHP(tm) v 0.10.0.1076
 * @version			$Revision: 7118 $
 * @modifiedby		$LastChangedBy: gwoo $
 * @lastmodified	$Date: 2008-06-04 13:49:29 -0700 (Wed, 04 Jun 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<!DOCTYPE
 html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
		echo $javascript->link(JS_PATH . 'jquery-mini.js', true);
		echo $javascript->link(JS_PATH . 'generic_functions.js?2', true);
		echo $scripts_for_layout;
        
	?>
    
    <link rel="search" type="application/opensearchdescription+xml" href="http://tatoeba.org/opensearch.xml" title="Tatoeba project" />
</head>
<body>
	<?php echo $this->element('google_analytics'); ?>
    <div id="audioPlayer"></div>
    
	<!--  TOP  -->
	<?php echo $this->element('top_menu'); ?>


	<div id="container1">
		<!--  Title/Logo  -->
		<?php echo $this->element('header');	?>


		<div id="container">

			<!--  SEARCH BAR  -->
			<?php echo $this->element('search_bar'); ?>

			<!--  CONTENT -->
			<?php
			if($session->check('Message.flash')){
				$session->flash();
			}

			echo $content_for_layout;
			?>
		</div>


		<!--  FOOT -->
		<?php
		echo $this->element('foot');
		?>
	</div>

	<div id="footer_container">
	</div>
</body>
</html>
