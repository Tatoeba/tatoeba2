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



/**
 * Helper for modules in "show_all_in" pages
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

class ShowAllHelper extends AppHelper
{

    public $helpers = array(
        'Languages',
        'Form',
    );

    /**
     * Generate the base url (entire url except arguments)
     *
     * @return string
     */
    private function _generateBaseUrl()
    {
        /*to stay on the same page except language filter option*/
        // we reconstruct the path
        $path ='/';
        // language of the interface
        $path .= $this->request->getParam('lang') .'/';
        $path .= $this->request->getParam('controller').'/';
        $path .= $this->request->getParam('action');
        return $path;
    }

    /**
     * generate the javascript code which will be used by the select
     * in order to redirect correctly to the good page
     *
     * @param array $params   List of parameters
     * @param int   $position Position of the parameter to replace by
     *                        The select value
     *
     * @return string
     */
    private function _generateJavascriptUrl($params, $position)
    {
        $baseUrl = $this->_generateBaseUrl();
        $params[$position] = 'this.value' ;

        $paramString = '';
        foreach ($params as $param) {
            if ($param == 'this.value') {
                $paramString .= "+'/'+ this.value";
            } else {
                $paramString .= ("+'/'+'$param'");
            }
        }
        return "'$baseUrl' $paramString";
    }

    /**
     * Generate the html select
     *
     * @param string $selectedLanguage The default selected item
     * @param array  $langs            The list of items
     * @param int    $position         Position of the params the select
     *                                 will change
     *
     * @return string The generated html
     */
    private function _generateSelect($selectedLanguage, $langs, $position)
    {

        $params = $this->request->getParam('pass');
        $javascriptUrl = $this->_generateJavascriptUrl($params, $position);

        return $this->Form->select(
            'filterLanguageSelect',
            $langs,
            array(
                "value" => $selectedLanguage,
                "id" => "",
                "onchange" => "window.location.href = $javascriptUrl",
                "class" => count($langs) > 2 ? 'language-selector' : null,
                "empty" => false
            )
        );

    }

    /**
     * Diplsay the module to show all sentences in
     * the language specified by select
     *
     * @param string $selectedLanguage The default selected language
     *
     * @return void
     */

    public function displayShowAllInSelect($selectedLanguage)
    {
        ?>
        <div class="section md-whiteframe-1dp">
            <h2><?php echo __('Sentences in:'); ?></h2>
            <?php
            $langs = $this->Languages->unknownLanguagesArray();

            echo $this->_generateSelect(
                $selectedLanguage,
                $langs,
                0
            );
            ?>
        </div>
    <?php
    }

    /**
     * Diplsay the module to filter only direct and indirect translations in
     * the language specified by select
     *
     * @param string $selectedLanguage The default selected language
     *
     * @return void
     */
    public function displayShowOnlyTranslationInSelect($selectedLanguage = 'none')
    {
        ?>
        <div class="section md-whiteframe-1dp">
            <h2><?php echo __('Show translations in:'); ?></h2>
            <?php
            $langs = $this->Languages->languagesArrayShowTranslationsIn();

            echo $this->_generateSelect(
                $selectedLanguage,
                $langs,
                1
            );
            ?>
            <p>
            <?php echo __('NOTE: Both direct and indirect translations will be shown.');
            ?>
            </p>
        </div>
    <?php
    }
}
?>
