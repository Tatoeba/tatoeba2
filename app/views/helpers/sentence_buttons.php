<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Helper to display sentences buttons that are not part of the menu. 
 *
 * @category Sentences
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentenceButtonsHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Javascript',
        'Languages',
        'Form'
    );
    
    /** 
     * Display show button for translations. It's the button with the arrow.
     *
     * @param int    $translationId Id of the translation.
     * @param string $type          'direct' or 'indirect'.
     *
     * @return void
     */
    public function translationShowButton($translationId, $type)
    {
        if (!in_array($type, array('direct', 'indirect'))) {
            $type = 'direct';
        }
        $image = $this->Html->image(
            $type.'_translation.png',
            array(
                "alt"=>__('Show', true),
                "title"=>__('Show', true)
            )
        );
        echo $this->Html->link(
            $image,
            array(
                "controller" => "sentences", 
                "action" => "show",
                $translationId
            ),
            array(
                "escape" => false, 
                "class" => "show button",
                "title" => __('Show', true)
            )
        );
    }
    
    
    /** 
     * Display unlink button for translations.
     *
     * @param int $sentenceId    Id of the main sentence.
     * @param int $translationId Id of the translation.
     *
     * @return void
     */
    public function unlinkButton($sentenceId, $translationId)
    {
        $confirmationMessage = __(
            'Do you want to unlink this translation from the main sentence?',
            true
        );
        $image = $this->Html->image(
            'unlink.png',
            array(
                "alt"=>__('Unlink', true),
                "title" => __('Unlink this translation.', true)
            )
        );
        echo $this->Html->link(
            $image, 
            array(
                "controller" => "links", 
                "action" => "delete",
                $sentenceId, 
                $translationId
            ),
            array(
                "escape" => false, 
                "class" => "link button",
            ),
            $confirmationMessage
        );
    }
    
    
    /** 
     * Display link button for translations.
     *
     * @param int $sentenceId    Id of the main sentence.
     * @param int $translationId Id of the translation.
     *
     * @return void
     */
    public function linkButton($sentenceId, $translationId)
    {
        $confirmationMessage = __(
            'Do you want to make this as a direct translation '.
            'of the main sentence?',
            true
        );
        $image = $this->Html->image(
            'link.png',
            array(
                "alt"=>__('Link', true),
                "title" => __('Make as direct translation.', true)
            )
        );
        echo $this->Html->link(
            $image, 
            array(
                "controller" => "links", 
                "action" => "add",
                $sentenceId, 
                $translationId
            ),
            array(
                "escape" => false, 
                "class" => "unlink button",
            ),
            $confirmationMessage
        );
    }
    
    
    /** 
     * Display audio button.
     *
     * @param int $sentenceId   Id of the sentence on which this button is displayed.       
     * @param int $sentenceLang Language of the sentence.
     *
     * @return void
     */
    public function audioButton($sentenceId, $sentenceLang)
    {
        $path = 'http://static.tatoeba.org/audio/sentences/'
            .$sentenceLang.'/'.$sentenceId.'.mp3'; 
        $soundIsAvailable = $this->_validateUrl($path);
        
        $css = 'audioAvailable';
        $title = __('Play audio', true);
        $onClick = 'return false';
        if (!$soundIsAvailable) {
            $css = 'audioUnavailable';
            $path = 'http://blog.tatoeba.org/2010/04/audio-for-tatoeba-sentences-in.html';
            $title = __('Audio unavailable. Click to learn more.', true);
            $onClick = 'window.open(this.href); return false;';
        }
        echo $this->Html->Link(
            null, $path,
            array(
                'title' => $title,
                'class' => "audioButton $css",
                'onclick' => $onClick
            )
        );
    }
    
    /** 
     * Check if a file exists on remove server. Inspired from this:
     * http://www.php.net/manual/en/function.fsockopen.php#39948
     *
     * TODO Move this to a more general helper someday.
     * 
     * @param string $url URL of the file.
     *
     * @return void
     */
    private function _validateUrl($url)
    {       
        $urlParts = @parse_url($url);
        
        if (empty($urlParts["host"])) {
            return false;
        }

        if (!empty($urlParts["path"])) {
            $filePath = $urlParts["path"];
        } else {
            $filePath = "/";
        }

        if (!empty( $url_parts["query"])) {
            $filePath .= "?" . $url_parts["query"];
        }

        $host = $urlParts["host"];
        $port = "80";
        
        $socket = @fsockopen($host, $port, $errno, $errstr, 30);
        if (!$socket) {
            return false;
        } else {
            fwrite($socket, "HEAD ".$filePath." HTTP/1.0\r\nHost: $host\r\n\r\n");
            $httpResponse = fgets($socket, 22);
           
            if (preg_match("/200 OK/", $httpResponse)) {
                fclose($socket);
                return true;
            } else {
                return false;
            }
        }
    }
    
    
    /**
     * Language flag.
     *
     * @param int    $id       Id of the sentence.
     * @param string $lang     Language of the sentence.
     * @param bool   $editable Set to true of flag can be changed.
     *
     * @return void
     */
    public function displayLanguageFlag($id, $lang, $editable = false)
    {
        if ($lang == '') {
            $lang = 'unknown_lang';
        }
        
        $class = '';
        if ($editable) {
            $this->Javascript->link('sentences.change_language.js', false);
            $class = 'editableFlag';
            
            // language select
            $langArray = $this->Languages->onlyLanguagesArray();
            echo $this->Form->select(
                'selectLang_'.$id,
                $langArray,
                'und',
                array(
                    "class"=>"selectLang", 
                    "title"=> $this->Languages->codeToName($lang)
                ),
                false
            );
            
            // setting data for sentences.change_language.js
            echo "<script type='text/javascript'>
            $(document).ready(function() {
                $('#flag_$id').data('sentenceId', $id);
                $('#flag_$id').data('currentLang', '$lang');
            });
            </script>";
        }
        
        echo $this->Html->image(
            'flags/'.$lang.'.png',
            array(
                "id" => "flag_".$id,
                "class" => "languageFlag ".$class,
                "title"=> $this->Languages->codeToName($lang)
            )
        );
        
    }
}
?>