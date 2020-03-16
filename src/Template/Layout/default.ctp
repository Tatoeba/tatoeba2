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
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use Cake\Core\Configure;
?>
<!DOCTYPE html>
<html lang="<?php echo LanguagesLib::languageTag(Configure::read('Config.language')); ?>">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?= isset($title_for_layout) ? $title_for_layout : $this->fetch('title'); ?>
    </title>
    <?php
        echo $this->Html->meta('icon');

        // ---------------------- //
        //          CSS           //
        // ---------------------- //
        // Only two CSS files are loaded. One that is generic, and one that is
        // specific to the view. The specific CSS file is auto-loaded. It must be
        // named with name of the view it is linked to, and put it in a folder with
        // the name of the controller.

        $this->loadHelper('AssetCompress.AssetCompress');

        // Generic
        // layout.css is defined in config/asset_compress.ini
        echo $this->AssetCompress->css('layout.css');

        // Specific
        $controller = $this->request->getParam("controller");
        $controller = Cake\Utility\Inflector::delimit($controller);
        $action = $this->request->getParam("action");
        $specificCSS = "$controller/$action.css";
        if (file_exists(Configure::read('App.cssBaseUrl') . $specificCSS)) {
            echo $this->Html->css($specificCSS);
        }

        echo $this->element('seo_international_targeting');
    ?>

    <link rel="search" type="application/opensearchdescription+xml"
          href="/opensearch.xml" title="Tatoeba" />
</head>
<body ng-app="app">
    <div id="audioPlayer"></div>

    <!--  TOP  -->
    <?php echo $this->element('top_menu'); ?>

    <!--  SEARCH BAR  -->
    <?php
    $isHomepage = $controller == 'pages' && $action == 'index';
    if (CurrentUser::isMember() || !$isHomepage) {
        $session = $this->request->getSession();
        $selectedLanguageFrom = $session->read('search_from') ?? 'und';
        $selectedLanguageTo = $session->read('search_to') ?? 'und';
        $searchQuery = isset($query) ? $query : '';
        if ($selectedLanguageFrom == 'und'
            && $selectedLanguageTo == 'und'
            && empty($query)
            && !$this->Languages->preferredLanguageFilter()) {
            $cache = [ 'key' => 'search_bar_'.Configure::read('Config.language') ];
        } else {
            $cache = null;
        }
        echo $this->element('search_bar',
            compact('selectedLanguageFrom', 'selectedLanguageTo', 'searchQuery'),
            compact('cache')
        );
    } else {
        echo $this->element('short_description', [], [
            'cache' => [ 'key' => 'short_description_'.Configure::read('Config.language') ]
        ]);
    }
    ?>

    <!--  CONTENT -->
    <div id="content">
        <div class="container">
        <?php
        echo $this->element('announcement');
        echo $this->Flash->render('flash', array('element' => 'flash_message'));

        echo $this->fetch('content');
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

    // layout.js is defined in config/asset_compress.ini
    echo $this->AssetCompress->script('layout.js');

    echo $this->fetch('scriptBottom');

    if (Configure::read('Announcement.enabled') || Configure::read('Tatoeba.devStylesheet')) {
        echo $this->Html->script(JS_PATH . 'jquery.cookie.js');
        echo $this->Html->script(JS_PATH . 'announcement.js');
    }

    if (Configure::read('GoogleAnalytics.enabled')) {
        echo $this->element('google_analytics', [], [ 'cache' => true ]);
    }
    ?>
</body>
</html>
