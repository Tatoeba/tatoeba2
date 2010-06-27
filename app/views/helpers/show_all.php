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
 * @link     http://tatoeba.org
 */


/**
 * Helper for modules in "show_all_in" pages
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class ShowAllHelper extends AppHelper
{

    public $helpers = array(
        'Languages',
        'Form',
        'Html',
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
        $path .= $this->params['lang'] .'/';
        $path .= $this->params['controller'].'/';
        $path .= $this->params['action'];

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

        $params = $this->params['pass'];
        $javascriptUrl = $this->_generateJavascriptUrl($params, $position); 

        return $this->Form->select(
            'filterLanguageSelect',
            $langs,
            $selectedLanguage,
            array(
                "onchange" => "$(location).attr('href', $javascriptUrl);"
            ),
            false
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
        <div class="module">
            <h2><?php __('Sentences in:'); ?></h2>
            <?php
            $langs = $this->Languages->onlyLanguagesArray();
           
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
        <div class="module">
            <h2><?php __('Translated in:'); ?></h2>
            <?php
            $langs = $this->Languages->languagesArrayForLists();
           
            echo $this->_generateSelect(
                $selectedLanguage,
                $langs,
                1
            );
            ?> 
        </div>
    <?php 
    }

    /**
     * Diplsay the module to filter main sentences with no direct translation in
     * the language specified by select
     *
     * @param string $selectedLanguage The default selected language
     *
     * @return void
     */

    public function displayShowNotTranslatedIn($selectedLanguage = 'none')
    {
        ?>
        <div class="module">
            <h2><?php __('Not translated in:'); ?></h2>
            <?php
            $langs = $this->Languages->LanguagesArrayForLists();
           
            echo $this->_generateSelect(
                $selectedLanguage,
                $langs,
                2
            );
            ?> 
        </div>
    <?php 
    }


}
?>
