<?php
namespace App\Model\Entity;

use App\Auth\VersionedPasswordHasher;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

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
        'can_switch_license' => false
    );

    private $settingsValidation = array(
        'sentences_per_page' => array(10, 20, 50, 100),
    );

    protected function _setPassword($password) {
        $passwordHasher = new VersionedPasswordHasher();
        return $passwordHasher->hash($password);
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
	if (!$this->id) {
		return null;
	}
	if (isset($this->group_id)) {
		$groupId = $this->group_id;
	} else {
		$Users = TableRegistry::get('Users');
		$user = $Users->find('all', ['fields' => ['group_id']])->where(['id' => $this->id])->first();
		$groupId = $user->group_id;
	}
	if (!$groupId) {
		return null;
	}
	return ['Groups' => ['id' => $groupId]];
    }
}
