<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Routing\Router;

class Audio extends Entity
{
    private $__wantedEnabled;

    protected $_virtual = [
        'author',
        'attribution_url',
        'license',
    ];

    public static $defaultExternal = array(
        'username' => '',
        'license' => '',
        'attribution_url' => '',
    );

    protected function _getExternal($external) {
        if (is_array($external)) {
            $external = array_merge(self::$defaultExternal, $external);
            $external = array_intersect_key($external, self::$defaultExternal);
        }
        return $external;
    }

    protected function _setExternal($external) {
        $existingExternal = $this->external;
        if (is_array($this->external) && is_array($existingExternal)) {
            $external = array_merge($existingExternal, $external);
        }
        return $external;
    }

    protected function _getEnabled() {
        return $this->__wantedEnabled ?? $this->getSource() != 'DisabledAudios';
    }

    protected function _setEnabled($enabled) {
        return $this->__wantedEnabled = $enabled;
    }

    protected function _getAuthor() {
        if ($this->user && $this->user->username) {
            return $this->user->username;
        } else {
            return $this->external['username'];
        }
    }

    private function __getUserProfileUrl($username) {
        $url = [
            'controller' => 'user',
            'action' => 'profile',
            $username
        ];
        $request = Router::getRequest();
        if ($request) {
            $isApiCall = $request->getParam('prefix') == 'VHosts/Api';
            if ($isApiCall) {
                $host = explode('.', $request->host());
                $host = array_slice($host, 1);
                $host = implode('.', $host);
                $url['_host'] = $host;
                $url['_full'] = true;
                $url['prefix'] = false;
            }
        }
        return Router::url($url);
    }

    protected function _getAttributionUrl() {
        if ($this->user) {
            if (array_key_exists('audio_attribution_url', $this->user->_properties)) {
                if (!empty($this->user->audio_attribution_url)) {
                    return $this->user->audio_attribution_url;
                } elseif ($this->user->username) {
                    return $this->__getUserProfileUrl($this->user->username);
                }
            } else {
                return null;
            }
        } else {
            return $this->external['attribution_url'];
        }
    }

    protected function _getLicense() {
        if ($this->user) {
            return $this->user->audio_license;
        } else {
            return $this->external['license'];
        }
    }

    private function __balancedTreePath($id) {
        $id = substr(sprintf('%04d', $id), -4);
        $pieces = [];
        do {
            $pieces[] = substr($id, 0, 2);
            $id = substr($id, 2);
        } while ($id != '');
        return implode(DS, $pieces);
    }

    protected function _getFilePath() {
       if ($this->id) {
           $path = $this->__balancedTreePath($this->id);
           $audioBasePath = Configure::read('Recordings.path');
           return $audioBasePath.DS.$path.DS.$this->id.'.mp3';
       } else {
           return null;
       }
    }

    protected function _getPrettyFilename() {
       if ($this->id && $this->sentence_id) {
           return $this->sentence_id.'-'.$this->id.'.mp3';
       } else {
           return null;
       }
    }
}
