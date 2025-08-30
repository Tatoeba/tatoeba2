<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 HO Ngoc Phuong Trang
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
 */
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\PinyinTrait;
use App\Model\Entity\FuriganaTrait;
use App\Lib\LanguagesLib;
use Cake\I18n\Time;
use App\Model\CurrentUser;


class Transcription extends Entity
{
    use PinyinTrait;
    use FuriganaTrait;

    protected $_virtual = [
        'readonly',
        'type',
        'html',
        'lang_tag',
        'markup',
        'editor',
        'info_message'
    ];

    protected $_hidden = [
        'created',
        'sentence',
    ];

    private $scriptsByLang = array( /* ISO 15924 */
        'jpn' => array('Jpan'),
        'uzb' => array('Cyrl', 'Latn'),
        'cmn' => array('Hans', 'Hant', 'Latn'),
        'yue' => array('Hans', 'Hant', 'Latn'),
    );
    private $availableTranscriptions = array(
        'jpn-Jpan' => array(
            'Hrkt' => array(
                'type' => 'altscript',
            ),
        ),
        'cmn-Hans' => array(
            'Hant' => array(
                'type' => 'altscript',
                'readonly' => true,
            ),
            'Latn' => array(
            ),
        ),
        'cmn-Hant' => array(
            'Hans' => array(
                'type' => 'altscript',
                'readonly' => true,
            ),
            'Latn' => array(
            ),
        ),
        'yue-Hans' => array(
            'Latn' => array(
                'readonly' => true,
            ),
        ),
        'yue-Hant' => array(
            'Latn' => array(
                'readonly' => true,
            ),
        ),
        'uzb-Latn' => array(
            'Cyrl' => array(
                'type' => 'altscript',
                'readonly' => true,
            ),
        ),
        'uzb-Cyrl' => array(
            'Latn' => array(
                'type' => 'altscript',
                'readonly' => true,
            ),
        ),
    );
    private $defaultFlags = array(
        'readonly' => false,
        'type' => 'transcription',
    );
    private $settings;

    protected function _getReadonly()
    {
        $settings = $this->getSettings();

        if (isset($settings['readonly'])) {
            return $settings['readonly'];
        } else {
            return false;
        }
    }

    protected function _getType()
    {
        $settings = $this->getSettings();
        
        if (isset($settings['type'])) {
            return $settings['type'];
        } else {
            return null;
        }
    }

    private function getSettings()
    {
        if (!isset($this->settings)) {
            $this->settings = [];
            if ($this->sentence) {
                $sourceScript = $this->sentence->script;
                $sourceLang = $this->sentence->lang;
                if (!$sourceScript) {
                    $sourceScript = $this->getSourceScript($sourceLang);
                }
                $langScript = $sourceLang . '-' . $sourceScript;
                if (isset($this->availableTranscriptions[$langScript])) {
                    $transcription = $this->availableTranscriptions[$langScript];
                    if (isset($transcription[$this->script])) {
                        $this->settings = $transcription[$this->script];
                    }
                }
            }
            $this->settings = array_intersect_key($this->settings, $this->defaultFlags);
            $this->settings = array_merge($this->defaultFlags, $this->settings);
        }
        return $this->settings;
    }

    private function getSourceScript($sourceLang) {
        if (isset($this->scriptsByLang[$sourceLang])) {
            if (count($this->scriptsByLang[$sourceLang]) == 1) {
                return $this->scriptsByLang[$sourceLang][0];
            }
        }
        return false;
    }

    protected function _getHtml() {
        if (!$this->sentence || !$this->sentence->lang || !$this->script || !$this->text) {
            return;
        }

        $lang = $this->sentence->lang;

        if ($this->script == 'Hrkt') {
            $text = $this->rubify($this->text);
        } elseif ($lang == 'cmn' && $this->script == 'Latn') {
            $text = htmlentities($this->numeric2diacritic($this->text));
        } else {
            $text = htmlentities($this->text);
        }

        return $text;
    }

    protected function _getLangTag()
    {
        if ($this->sentence && $this->sentence->lang) {
            return LanguagesLib::languageTag($this->sentence->lang, $this->script);
        }
    }

    protected function _getMarkup() {
        if (!$this->sentence || !$this->sentence->lang || !$this->script || !$this->text) {
            return;
        }

        $text = null;
        $editable = !$this->readonly && CurrentUser::canEditTranscription($this->user_id, $this->sentence->user_id);
        if ($editable) {
            if ($this->script == 'Hrkt') {
                $text = $this->bracketify($this->text);
            } else {
                $text = $this->text;
            }
        }

        return $text;
    }

    protected function _getInfoMessage() {
        if (!isset($this->type) || !isset($this->needsReview)) {
            return;
        }

        if ($this->needsReview) {
            if ($this->type == 'altscript') {
            $message = __(
                'This alternative script was generated automatically '.
                'and has not been reviewed.'
            );
            } else {
                $message = __(
                    'This transcription was generated automatically '.
                    'and has not been reviewed.'
                );
            }
        } else {
            if (isset($this->user->username)) {
                if ($this->type == 'altscript' && isset($this->sentence->lang) && $this->sentence->lang == 'jpn') {
                    $message = __('The furigana was last edited by {author} on {date}.');
                } else {
                    /* @translators: refers to a transcription */
                    $message = __('Last edited by {author} on {date}');
                }
                $message = format($message, [
                    'author' => $this->user->username,
                    'date' => $this->modified->nice(),
                ]);
            } else {
                if ($this->type == 'altscript') {
                    $message = __('This alternative script was generated automatically.');
                } else {
                    $message = __('This transcription was generated automatically.');
                }
            }
        }

        return $message;
    }

    protected function _getEditor()
    {
        if ($this->user && $this->user->username) {
            return $this->user->username;
        }
    }
}
