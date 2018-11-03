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
 * TODO all rules are have a with and without :lang ,
 * maybe we can have a rule to handle both
 */

// Array that lists all the languages into which the Tatoeba interface has been translated
$configUiLanguages = Configure::read('UI.languages');
$iso3LangArray = array();
foreach ($configUiLanguages as $lang) {
    $iso3LangArray[] = $lang[0];
    if (isset($lang[3]) && is_array($lang[3])) {
        foreach ($lang[3] as $alias) {
            $iso3LangArray[] = $alias;
        }
    }
}
$interfaceLanguages = array(
    'lang' => join('|', $iso3LangArray)
);

/**
 * To route tools, in order to still have tools in the URL, which is
 * clearer for users IMHO
 * this rule appears first, that way /fre/tools/search_sinograms  is
 * not catch by the general rule for controllers
 */
Router::connect(
    '/tools/search_hanzi_kanji',
    array(
        'controller' => 'sinograms',
        'action' =>'index'
    )
);
Router::connect(
    '/tools/search_hanzi_kanji/:action',
    array(
        'controller' => 'sinograms',
    )
);

Router::connect(
    '/:lang/tools/search_hanzi_kanji',
    array(
        'lang'=>'eng',
        'controller' => 'sinograms',
        'action' =>'index'
    ),
    $interfaceLanguages
);
Router::connect(
    '/:lang/tools/search_hanzi_kanji/:action',
    array(
        'lang'=>'eng',
        'controller' => 'sinograms',
    ),
    $interfaceLanguages
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
        'action' => 'index',
    )
);
Router::connect(
    '/:lang',
    array(
        'lang' => ':lang',
        'controller' => 'pages',
        'action' => 'index',
    ),
    $interfaceLanguages
);
// TODO : can we use directly "home" action instead of display ?

Router::connect(
    '/:action',
    array(
        'controller' => 'pages',
    )
);
Router::connect(
    '/:lang/:action',
    array(
        'lang' => ':lang',
        'controller' => 'pages',
    ),
    $interfaceLanguages
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
    $interfaceLanguages
);
