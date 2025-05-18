<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'username' => ['type' => 'string', 'length' => 20, 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 62, 'null' => false, 'default' => '', 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'email' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => '', 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'role' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'since' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '', 'precision' => null],
        'last_time_active' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'level' => ['type' => 'tinyinteger', 'length' => 2, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'send_notifications' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => '', 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'birthday' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'description' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'settings' => ['type' => 'json', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'homepage' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => '', 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'image' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => '', 'collate' => 'utf8_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'country_id' => ['type' => 'string', 'length' => 2, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'audio_license' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => '', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'audio_attribution_url' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => '', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'login' => ['type' => 'unique', 'columns' => ['username'], 'length' => []],
            'email' => ['type' => 'unique', 'columns' => ['email'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => '1',
                'username' => 'admin',
                'password' => '0 $2a$10$C9HUn1u98XMMV/J2DP9F.eSPrJY0UiX7Z1PsDiWoGakXsSzwpUQ/e', // blowfish(md5('ze@9422#5dS?!99xx' . '123456'))
                'email' => 'admin@example.com',
                'role' => 'admin',
                'since' => '2013-04-07 12:15:16',
                'last_time_active' => '1397515014',
                'level' => '1',
                'send_notifications' => 1,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => null,
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => '',
                'audio_attribution_url' => '',
            ],
            [
                'id' => '2',
                'username' => 'corpus_maintainer',
                'password' => '0 $2a$10$si98GpL3psq5k1EKh/koVup8GfGoB1.hjdRCbgKlzofvUbRkRBwjC', // blowfish(md5('ze@9422#5dS?!99xx' . '123456'))
                'email' => 'corpus_maintainer@example.com',
                'role' => 'corpus_maintainer',
                'since' => '2013-04-07 12:15:50',
                'last_time_active' => '0',
                'level' => '1',
                'send_notifications' => 1,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => null,
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => '',
                'audio_attribution_url' => '',
            ],
            [
                'id' => '3',
                'username' => 'advanced_contributor',
                'password' => '0 $2a$10$YeMYysi2Wkiu1LPdm2SAEeD7tfYsXKoAKDGKutQvOPcKKcdpte.3K', // blowfish(md5('ze@9422#5dS?!99xx' . '123456'))
                'email' => 'advanced_contributor@example.com',
                'role' => 'advanced_contributor',
                'since' => '2013-04-07 12:16:37',
                'last_time_active' => '0',
                'level' => '1',
                'send_notifications' => 1,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => null,
                    'can_switch_license' => true,
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => '',
                'audio_attribution_url' => '',
            ],
            [
                'id' => '4',
                'username' => 'contributor',
                'password' => '0 $2a$10$Dn8/JT1xViULUEBCR5HiquLCXXB4/K3N2Nzc0PRZ.bfbmoApO55l6', // blowfish(md5('ze@9422#5dS?!99xx' . '123456'))
                'email' => 'contributor@example.com',
                'role' => 'contributor',
                'since' => '2013-04-07 12:17:02',
                'last_time_active' => '0',
                'level' => '1',
                'send_notifications' => 1,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => 'fra,deu',
                    'can_switch_license' => true,
                    'license_switch_list_id' => 4,
                ],
                'homepage' => '',
                'image' => '93986962b3472786d9aea008f6160bfd.png',
                'country_id' => NULL,
                'audio_license' => 'CC BY 4.0',
                'audio_attribution_url' => 'https://example.com/my-audios',
            ],
            [
                'id' => '5',
                'username' => 'inactive',
                'password' => '0 $2a$10$k4WEM.b.68FriHgL3TbOpOrEWb35kMSwzkjvrLd5bzLExnFpVxAQa', // blowfish(md5('ze@9422#5dS?!99xx' . '123456'))
                'email' => 'inactive@example.com',
                'role' => 'inactive',
                'since' => '2013-04-07 12:17:29',
                'last_time_active' => '0',
                'level' => '1',
                'send_notifications' => 1,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => null,
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => '',
                'audio_attribution_url' => '',
            ],
            [
                'id' => '6',
                'username' => 'spammer',
                'password' => '0 $2y$10$rIQeJc3yxAAfFmPeL/spj.hVJQqoA6yzHfh/kxRNBjuHhxCUiqXqO', // blowfish(md5('ze@9422#5dS?!99xx' . '123456'))
                'email' => 'spammer@example.com',
                'role' => 'spammer',
                'since' => '2013-04-07 12:17:54',
                'last_time_active' => '0',
                'level' => '1',
                'send_notifications' => 1,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => null,
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => '',
                'audio_attribution_url' => '',
            ],
            [
                'id' => '7',
                'username' => 'kazuki',
                'password' => '1 $2a$10$eSfvBiKsuMQsKq0B2sGyuukXNBNPwXXqSZJfzFUu/6b4vZYnehn/2', // blowfish('myAwesomePassword')
                'email' => 'kazuki@example.net',
                'role' => 'contributor',
                'since' => '2013-04-22 19:20:11',
                'last_time_active' => '1397514924',
                'level' => '1',
                'send_notifications' => 0,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'sentences_per_page' => 20,
                    'is_public' => false,
                    'lang' => null,
                    'default_license' => 'CC0 1.0',
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => 'CC BY 4.0',
                'audio_attribution_url' => '',
            ],
            [
                'id' => '8',
                'username' => 'mr_old_style_passwd',
                'password' => 'dc59e60a5353bf329d0c961185055226',
                'email' => 'mr_old_style_passwd@example.net',
                'role' => 'contributor',
                'since' => '2013-04-22 19:20:11',
                'last_time_active' => '1397514924',
                'level' => '1',
                'send_notifications' => 1,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => null,
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => '',
                'audio_attribution_url' => '',
            ],
            [
                'id' => '9',
                'username' => 'new_member',
                'password' => '0 $2a$10$C9HUn1u98XMMV/J2DP9F.eSPrJY0UiX7Z1PsDiWoGakXsSzwpUQ/e',
                'email' => 'new_member@example.net',
                'role' => 'contributor',
                'since' => date('Y-m-d H:i:s'),
                'last_time_active' => '0',
                'level' => '1',
                'send_notifications' => 1,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => null,
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => '',
                'audio_attribution_url' => '',
            ],
            [
                'id' => '10',
                'username' => 'FixHashesCommand',
                'password' => '0 $2a$10$C9HUn1u98XMMV/J2DP9F.eSPrJY0UiX7Z1PsDiWoGakXsSzwpUQ/e',
                'email' => 'admin@example.net',
                'role' => 'contributor',
                'since' => '2015-04-12 12:34:56',
                'last_time_active' => '0',
                'level' => '1',
                'send_notifications' => 0,
                'name' => '',
                'birthday' => NULL,
                'description' => '',
                'settings' => [
                    'is_public' => false,
                    'lang' => null,
                ],
                'homepage' => '',
                'image' => '',
                'country_id' => NULL,
                'audio_license' => '',
                'audio_attribution_url' => '',
            ],
        ];
        parent::init();
    }
}
