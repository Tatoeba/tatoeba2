<?php
namespace App\Model\Entity;

use App\Auth\VersionedPasswordHasher;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class User extends Entity
{
    const ROLE_ADMIN = 'admin';
    const ROLE_CORPUS_MAINTAINER = 'corpus_maintainer';
    const ROLE_ADV_CONTRIBUTOR = 'advanced_contributor';
    const ROLE_CONTRIBUTOR = 'contributor';
    const ROLE_INACTIVE = 'inactive';
    const ROLE_SPAMMER = 'spammer';

    const ALL_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CORPUS_MAINTAINER,
        self::ROLE_ADV_CONTRIBUTOR,
        self::ROLE_CONTRIBUTOR,
        self::ROLE_INACTIVE,
        self::ROLE_SPAMMER,
    ];

    const ROLE_CONTRIBUTOR_OR_HIGHER = [
        self::ROLE_ADMIN, self::ROLE_CORPUS_MAINTAINER,
        self::ROLE_ADV_CONTRIBUTOR, self::ROLE_CONTRIBUTOR
    ];
    const ROLE_ADV_CONTRIBUTOR_OR_HIGHER = [
        self::ROLE_ADMIN, self::ROLE_CORPUS_MAINTAINER,
        self::ROLE_ADV_CONTRIBUTOR
    ];
    const ROLE_CORPUS_MAINTAINER_OR_HIGHER = [
        self::ROLE_ADMIN, self::ROLE_CORPUS_MAINTAINER
    ];

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
        'can_switch_license' => false,
        'new_terms_of_use' => false,
        'license_switch_list_id' => null,
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
}
