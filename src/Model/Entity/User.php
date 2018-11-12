<?php
namespace App\Model\Entity;

use App\Auth\VersionedPasswordHasher;
use Cake\ORM\Entity;

class User extends Entity
{
    // contributor vs. advanced contributor vs. corpus maintainer vs. admin
    const LOWEST_TRUST_GROUP_ID = 4;

    // trustworthy vs. untrustworthy
    const MIN_LEVEL = -1; // trustworthy
    const MAX_LEVEL = 0; // untrustworthy (submits bad or copyrighted sentences)

    public static $defaultSettings = array(
        'is_public' => false,
        'lang' => null,
        'use_most_recent_list' => false,
        'collapsible_translations' => false,
        'show_transcriptions' => false,
        'sentences_per_page' => 10,
        'users_collections_ratings' => false,
        'native_indicator' => false,
        'copy_button' => false,
        'hide_random_sentence' => false,
        'use_new_design' => false,
        'default_license' => 'CC BY 2.0 FR',
    );

    private $settingsValidation = array(
        'sentences_per_page' => array(10, 20, 50, 100),
    );

    protected function _setPassword($password) {
        $passwordHasher = new VersionedPasswordHasher();
        return $passwordHasher->hash($password);
    }

    public function afterFind($results, $primary = false) {
        foreach ($results as &$result) {
            if (isset($result['User']) && array_key_exists('settings', $result['User'])) {
                $result['User']['settings'] = (array)json_decode(
                    $result['User']['settings']
                );
            }
        }
        return $results;
    }

    public function beforeSave($options = array()) {
        if (array_key_exists('settings', $this->data['User'])
            && is_array($this->data['User']['settings'])) {
            $this->data['User']['settings'] = json_encode($settings);
        }
        return true;
    }

    protected function _getSettings($settings) {
        $settings = array_merge(self::$defaultSettings, (array)$settings);
        $this->validateSettings($settings);
        return $settings;
    }

    protected function _setSettings($settings) {
        $existingSettings = (array)$this->settings;
        $settings = array_merge($existingSettings, $settings);
        $settings = array_intersect_key($settings, self::$defaultSettings);
        return $settings;
    }

    private function validateSettings(&$settings) {
        foreach ($this->settingsValidation as $setting => $values) {
            if (!in_array($settings[$setting], $values)) {
                $settings[$setting] = self::$defaultSettings[$setting];
            }
        }
    }

    /**
     * ?
     *
     * @return array
     */
    public function parentNode()
    {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
            $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
            return null;
        } else {
            return array('Group' => array('id' => $groupId));
        }
    }
}
