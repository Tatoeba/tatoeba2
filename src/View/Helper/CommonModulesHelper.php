<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\View\Helper\AppHelper;
use Cake\Utility\Inflector;


/**
 * Helper for modules and part of module
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

class CommonModulesHelper extends AppHelper
{

    public $helpers = array(
        'Languages',
        'Form',
        'Html',
    );

    /**
     * Create a module for filtering a page by language
     *
     * @param int $maxNumberOfParams The number of parameters used in the controller
     *                               for the view which uses this module
     *                               NOTE: the language must be the last parameter
     *
     * @return void
     */
    public function createFilterByLangMod($maxNumberOfParams = 1)
    {
        ?>
        <div class="section md-whiteframe-1dp" layout="column">
            <h2><?php echo __('Filter by language'); ?></h2>
            <?php
            // In order to stay on the same page we reconstruct the path
            // without the language parameter
            $path ='/';
            // language of the interface
            $path .= $this->request->getParam('lang') .'/';
            $path .= Inflector::delimit($this->request->getParam('controller')).'/';
            $path .= $this->request->getParam('action');

            $params = $this->request->getParam('pass');
            $numberOfParams = count($params);

            $paramsWithoutLang = $numberOfParams;
            if ($numberOfParams === $maxNumberOfParams) {
                $paramsWithoutLang--;
            }

            for ($i = 0; $i < $paramsWithoutLang; $i++) {
                $path .= '/'.$params[$i];
            }

            $lang = 'und' ;
            if (isset($params[$maxNumberOfParams-1]) && $this->Languages->languageExists($lang)) {
                $lang  = $params[$maxNumberOfParams-1];
            }

            $langs = $this->Languages->languagesArrayAlone();

            echo $this->_View->element(
                'language_dropdown',
                array(
                    'name' => 'filterLanguageSelect',
                    'languages' => $langs,
                    'initialSelection' => $lang,
                    'forceItemSelection' => true,
                    'onSelectedLanguageChange' => "
                        window.location.pathname =
                        '$path'
                        + (language.code == 'und' ? '' : '/'+language.code)",
                    // the check for 'und' is to avoid a duplicate page (with and without it)
                )
            );
            ?>
        </div>
    <?php
    }

    /**
     * Display a module content which indicate the user does not exist
     *
     * @param string $userName The username which doesn't exist.
     *
     * @return void
     */
    public function displayNoSuchUser($username)
    {
        echo '<h2>';
        echo format(
            __("There's no user called {username}"),
            ['username' => $this->_View->safeForAngular($username)]
        );
        echo '</h2>';

        echo $this->Html->link(__('Go back to previous page'), 'javascript:history.back()');
    }
}
?>
