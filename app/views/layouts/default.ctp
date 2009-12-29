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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
        // TIPS : to avoid css to be joined even when not needed
        // you can use if ($this->params etc... to restrict css
        // to some pages

		echo $html->meta('icon');
		echo $html->css('tatoeba.newui');
		echo $html->css('tatoeba.newgeneric');
		echo $html->css('tatoeba.sentences');
		echo $html->css('tatoeba.logs');
		echo $html->css('tatoeba.comments');
		echo $html->css('tatoeba.statistics');
		echo $html->css('tatoeba.users');
        if($this->params['controller'] == 'user' || $this->params['controller'] == 'followers'){
	        echo $html->css('tatoeba.profile', false);
        }

        if($this->params['controller'] == 'wall'){
            echo $html->css('tatoeba.wall');
        }
		echo $html->css('tatoeba.navigation');
		echo $html->css('tatoeba.popup');
//		echo $html->css('tatoeba.conversations');
		echo $html->css('tatoeba.private_messages');
		echo $html->css('tatoeba.sentences_lists');
		echo $html->css('tatoeba.tools');
        if($this->params['controller'] == 'sinograms'){
            echo $html->css('tatoeba.sinograms');
        }
//      echo $html->css('jquery-ui-1.7.2.custom');

//      echo $html->css('autocompletion.jquery.autocomplete');
//      echo $html->css('autocompletion.thickbox');

		echo $javascript->link('jquery-mini.js', true);
//		echo $javascript->link('jqueryui/jquery-1.3.2.min.js', true);
//		echo $javascript->link('jqueryui/jquery-ui-1.7.2.custom.min.js', true);
		echo $javascript->link('sentences.show_another.js', true);
		//echo $javascript->link('general.init.js', true);
		echo $scripts_for_layout;
	?>
</head>
<body>

	<!--  TOP  -->
	<?php echo $this->element('top1'); ?>


	<div id="container1">
		<!--  Title/Logo  -->
		<?php echo $this->element('header');	?>


		<div id="container">

			<?php //echo $this->element('top2');	?>

			<!--  MENU  -->
			<?php //echo $this->element('menu'); ?>

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
	<?php echo $cakeDebug ?>
	<?php echo $this->element('google_analytics'); ?>
</body>
</html>
