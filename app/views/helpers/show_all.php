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
                "id" => null,
                "onchange" => "$(location).attr('href', $javascriptUrl);",
                "class" => count($langs) > 2 ? 'language-selector' : null,
                "empty" => false
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
        <div class="module">
            <h2><?php __('Show translations in:'); ?></h2>
            <?php
            $langs = $this->Languages->languagesArrayForPositiveLists();
           
            echo $this->_generateSelect(
                $selectedLanguage,
                $langs,
                1
            );
            ?>
            <p>
            <?php
            __('NOTE: Both direct and indirect translations will be shown.');
            ?>
            </p>
        </div>
    <?php
    }

    /**
     * Diplsay the module to filter main sentences with no direct translation in
     * the language specified by select
     *
     * @todo This should be completely removed at some point.
     *
     * @param string $selectedLanguage The default selected language
     *
     * @return void
     */

    public function displayShowNotTranslatedInto($selectedLanguage = 'none')
    {
        ?>
        <div class="module">
            <h2><?php __('Not directly translated into:'); ?></h2>
            <?php
            echo format(
                __(
                    'This option has been removed. '.
                    'Please use the <a href={}>Translate sentences</a> page '.
                    'to find untranslated sentences.', true
                ),
                $this->Html->url(array(
                    'controller' => 'activities',
                    'action' => 'translate_sentences'
                ))
            );
            ?>
            </p>
        </div>
    <?php
    }

    /**
     * Display the module to filter (or not) only main sentences with audio
     *
     * @param string $selectedOption The default selected option
     *
     * @return void
     */

    public function displayFilterOrNotAudioOnly($selectedOption = 'indifferent')
    {
        ?>
        <div class="module">
            <h2><?php __('Only sentences with audio:'); ?></h2>
            <?php
            $options = array(
                'indifferent' => __('no', true),
                'only-with-audio' =>  __('yes', true),
            );

            echo $this->_generateSelect(
                $selectedOption,
                $options,
                3
            );
            ?>
            <p>
            <?php
            __('NOTE: Not all languages have audio at the moment.');
            ?>
            </p>
        </div>
    <?php
    }



}
?>
