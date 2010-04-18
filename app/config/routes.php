<?php
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @copyright  Copyright 2005-2008, Cake Software Foundation, Inc.
 * @package      Cake
 * @subpackage   cake.app.config
 * @since        CakePHP(tm) v 0.2.9
 * @version      $Revision: 7296 $
 * @link         http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @modifiedby   $LastChangedBy: gwoo $
 * @lastmodified $Date: 2008-06-27 02:09:03 -0700 (Fri, 27 Jun 2008) $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/*  /!\ WARNING /!\ 
**
**  order of lines  is important here !
**  router::connect works like this
**  rules are ordered, the first is the first declared
**  and we stop search once we have found a matching rule 
*/

/**
 * To route tools, in order to still have tools in the URL, which is
 * clearer for users IMHO
 * this rule appears first, that way /fre/tools/search_sinograms  is 
 * not catch by the general rule for controllers
 */
    
    Router::connect(
        '/tools/search_hanzi_kanji/:action',
        array(
            'controller' => 'sinograms',
            'action' =>'index'
        )
    );

    Router::connect(
        '/:lang/tools/search_hanzi_kanji/:action ',
        array(
            'lang'=>'eng',
            'controller' => 'sinograms',
            'action' =>'index'
        ),
        array('lang'=>'fre|eng|deu|spa|ita|jpn|chi|pol|pt_BR')
    ); 

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */
    Router::connect(
        '/',
        array(
            'controller' => 'pages',
            'action' => 'display',
            'index'
        )
    );
    Router::connect(
        '/:lang',
        array(
            'lang' => ':lang',
            'controller' => 'pages',
            'action' => 'display',
            'index'
        ),
        array(
            'lang'=>'fre|eng|deu|spa|ita|jpn|chi|pol|pt_BR'
        )
    );
    // TODO : can we use directly "home" action instead of display ?

    Router::connect(
        '/home',
        array(
            'controller' => 'pages',
            'action' => 'display',
            'home'
        )
    );
    Router::connect(
        '/:lang/home',
        array(
            'lang' => ':lang',
            'controller' => 'pages',
            'action' => 'display',
            'home'
        ),
        array(
            'lang'=> 'fre|eng|deu|spa|ita|jpn|chi'
        )
    );
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
    Router::connect(
        '/pages/*',
        array(
            'controller' => 'pages',
            'action' => 'display'
        )
    );
    Router::connect(
        '/:lang/pages/*',
        array(
            'lang' => ':lang',
            'controller' => 'pages',
            'action' => 'display'
        ),
        array(
            'lang'=>'fre|eng|deu|spa|ita|jpn|chi|pol|pt_BR'
        )
    );
/**
 * Then we connect url '/test' to our test controller. This is helpful in
 * developement.
 */
    Router::connect(
        '/tests',
        array(
            'controller' => 'tests',
            'action' => 'index'
        )
    );
/**
 * La langue choisie sera maintenant disponible dans les contrôleurs
 * par la variable $this->params['lang'].
 */     
    Router::connect(
        '/:lang/:controller/:action/*',
        array(
            'lang'=>'eng'
        ),
        array(
            'lang'=>'fre|eng|deu|spa|ita|jpn|chi|pol|pt_BR'
        )
    ); 

?>
