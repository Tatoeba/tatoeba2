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
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\Autotranscription;
use Cake\Event\Event;

/**
 * Controller for tools.
 *
 * @category Tools
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

class ToolsController extends AppController
{
    public $name = 'Tools';
    public $helpers = array('Transcriptions', 'Pinyin');
    public $uses = array('Transcription');

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
        // Redirect to romaji_furigana.
        // We don't remove the method to be compatible with previous Google indexing.
        $this->redirect(
            array(
                "action" => "romaji_furigana"
            ),
            301
        );
    }


    /**
     * Japanese to romaji/furigana converter. Powered by MeCab.
     *
     * @return void
     */
    public function romaji_furigana()
    {
        $this->redirect(
            array(
                'action' => 'furigana'
            ),
            301
        );
    }

    /**
     * Furigana autogeneration
     */
    public function furigana()
    {
        $query = '';
        $result = '';

        if (isset($_GET['query'])) {
            $query = $_GET['query'];
        }

        $sentence = array(
            'id' => null,
            'lang' => 'jpn',
            'text' => $query,
        );
        if (!empty($query)) {
            $result = $this->Transcription->generateTranscription($sentence, 'Hrkt');
        }

        $this->set('query', $query);
        $this->set('result', $result);
    }


    /**
     * will convert a sentence in traditional chinese
     * to simplified and vice versa
     *
     * @return void
     */
    public function conversion_simplified_traditional_chinese()
    {
        if (empty($this->request->getData('Tool'))) {
            return;
        }

        $text = $this->request->getData('Tool')['query'];

        if (!empty($text)) {
            $script = $this->Transcription->detectScript('cmn', $text);
            $sentence = array(
                'id' => null,
                'lang' => 'cmn',
                'text' => $text,
                'script' => $script,
            );

            $targetScript = ($script == 'Hant') ? 'Hans' : 'Hant';
            $transcr = $this->Transcription->generateTranscription(
                $sentence,
                $targetScript
            );

            if ($transcr) {
                $this->set('convertedText', $transcr['text']);
                $this->set('script', $targetScript);
            }
        }

        $this->set('lastText', $text);
    }

    /**
     * Convert a text in shanghainese into ipa
     *
     * @param string $text Shanghainese text to convert.
     *
     * @return void
     */
    public function shanghainese_to_ipa($text = null)
    {
        if (empty($text) && !empty($this->request->getData('Tool'))) {
            $text = $this->request->getData('Tool')['query'];
        }

        if (!empty($text)) {
            $autotranscription = new Autotranscription();
            $ipa = $autotranscription->wuu($text);

            $this->set('convertedText', $ipa);
            $this->set('lastText', $text);
        }
    }
}
