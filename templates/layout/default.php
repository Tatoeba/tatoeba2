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
use Cake\I18n\I18n;

$lang = I18n::getLocale();
$htmlDir = LanguagesLib::getLanguageDirection($lang);

$controller = $this->request->getParam("controller");
$controller = Cake\Utility\Inflector::delimit($controller);
$action = $this->request->getParam("action");

$isHomepage = $controller == 'pages' && $action == 'index';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $htmlDir ?>">
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
        foreach (["$controller.css", "$controller/$action.css"] as $specificCSS) {
            if (file_exists(Configure::read('App.cssBaseUrl') . $specificCSS)) {
                echo $this->Html->css($specificCSS);
            }
        }

        echo $this->element('seo_international_targeting');
    ?>

    <link rel="search" type="application/opensearchdescription+xml"
          href="/opensearch.xml" title="Tatoeba" />
    
    <?php if (isset($isResponsive) && $isResponsive) { ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php } ?>
</head>
<body ng-app="app"
    <?php if (isset($isResponsive) && $isResponsive) { ?>
        class="responsive"
    <?php } ?>
>
    <!--  TOP  -->
    <?php echo $this->element('top_menu', ['htmlDir' => $htmlDir]); ?>

    <!--  SEARCH BAR  -->
    <?php
    
    if (CurrentUser::isMember() || !$isHomepage) {
        $session = $this->request->getSession();
        $selectedLanguageFrom = $session->read('search_from') ?? '';
        $selectedLanguageTo = $session->read('search_to') ?? '';
        $searchQuery = isset($query) ? $query : '';
        if ($selectedLanguageFrom == ''
            && $selectedLanguageTo == ''
            && empty($query)
            && !$this->Languages->preferredLanguageFilter()) {
            $cache = [ 'key' => 'search_bar_'.$lang ];
            // When we use the cached version of the search bar we need to add
            // the template for the 'language-dropdown' directive
            $this->AngularTemplate->addTemplate(
                $this->element('language_dropdown_angular'),
                'language-dropdown-template'
            );
        } else {
            $cache = null;
        }
        echo $this->element('search_bar',
            compact('selectedLanguageFrom', 'selectedLanguageTo', 'searchQuery'),
            compact('cache')
        );
    } else {
        echo $this->element('short_description', [], [
            'cache' => [ 'key' => 'short_description_'.$lang ]
        ]);
    }
    ?>

    <div class="announcement-container">
        <?= $this->element('announcement'); ?>
    </div>

    <!--  CONTENT -->
    <div id="content">
        <div class="container">
        <?php
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
    ?>
</body>
</html>
