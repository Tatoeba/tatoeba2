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

class Transcription extends Entity
{
    protected $_virtual = ['readonly', 'type'];

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
}
