<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Controller for tools.
 *
 * @category Tools
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ToolsController extends AppController
{
    public $name = 'Tools';
    public $helpers = array('Kakasi', 'Javascript');
    public $components = array('Pinyin');
    
    /**
     * Before filter.
     * 
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        
        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array('*');
    }
    
    /**
     * Index of tools.
     * 
     * @return void
     */
    public function index()
    {
    }
    
    /**
     * Japanese to romaji/furigana converter. Powered by KAKASI.
     * 
     * @return void
     */
    public function kakasi()
    {
    }

    /**
     * will convert a sentence in traditional chinese
     * to simplified and vice versa
     *
     * @return void
     */
    public function switch_script()
    {
        $text = $this->data['Tool']['query'];    
        // very important escapeshellarg return the escaped string 
        // not directly by refere,ce
        $text = escapeshellarg($text); 
        $convertedText =  exec("adso.sh --switch-script -cn -i '$text'");
     
        $this->set('convertedText', $convertedText); 
        $this->set('lastText', $text);
    }


    /**
     * Different converters chinese/pinyin
     *
     * @return void
     */
    public function pinyin_converter()
    {
        $text = $this->data['Tool']['query'];
        $from = $this->data['Tool']['from'];
        $to = $this->data['Tool']['to'];
        
        if (!empty($text)) {
            // we don't need to do nothing if we have choose the same output
            // than input
            if ($from === $to) {
                $this->set('pinyin', $text);
                $this->set('lastText', $text);
                return;
            }


            if ($from === 'chinese') {
                // then we need to call the adso function
                $Sentence = ClassRegistry::init('Sentence');
                $pinyin = $Sentence->getRomanization($text, 'cmn');
                
                if ($to === 'diacPinyin') {
                    $pinyin = $this->Pinyin->numeric2diacritic($pinyin);
                }
                
                $this->set('convertedText', $pinyin);
                $this->set('lastText', $text);
                return;
            }

            if ($from == 'numPinyin') {
                $pinyin = $this->Pinyin->numeric2diacritic($text);
                $this->set('convertedText', $pinyin);
                $this->set('lastText', $text);
                return;
            }

        }

    }
}
?>
