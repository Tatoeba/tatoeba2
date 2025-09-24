<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\CurrentUser;
use App\Model\Table\UsersTable;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

class UsersTableTest extends TestCase
{
    public $Users;

    public $fixtures = [
        'app.users',
        'app.users_languages',
        'app.sentences',
        'app.contributions',
        'app.sentence_comments',
        'app.walls',
    ];

    public function setUp()
    {
        parent::setUp();

        Router::pushRequest(new ServerRequest([
            'environment' => [
                'HTTP_HOST' => 'tatoeba.org',
                'HTTPS' => 'on',
            ],
        ]));

        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = TableRegistry::getTableLocator()->get('Users', $config);
    }

    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

    public function testSave_birthday_marshal_ok() {
        $user = $this->Users->get(1);
        $newData = ['birthday' => ['day' => '01', 'month' => '01', 'year' => '2000']];

        $result = $this->Users->patchEntity($user, $newData);

        $this->assertEquals('2000-01-01', $user->birthday);
    }

    public function testSave_birthday_marshal_no_year() {
        $user = $this->Users->get(1);
        $newData = ['birthday' => ['day' => '02', 'month' => '10', 'year' => '']];

        $result = $this->Users->patchEntity($user, $newData);

        $this->assertEquals('0000-10-02', $user->birthday);
    }

    public function testSave_birthday_marshal_no_year_but_leap_year() {
        $user = $this->Users->get(1);
        $newData = ['birthday' => ['month' => '02', 'day' => '29', 'year' => '']];

        $result = $this->Users->patchEntity($user, $newData);

        $this->assertEquals('1904-02-29', $user->birthday);
    }

    public function birthdayDateProvider() {
        return [
            // testname => [is valid, date]
            'valid day'              => [true,  '2000-02-29'],
            'leap year without year' => [true,  '0000-02-29'],
            'invalid day'            => [false, '2000-10-42'],
            'nothing'                => [true,  '0000-00-00'],
            'month and day'          => [true,  '0000-01-01'],
            'year only'              => [true,  '2000-00-00'],
            'year and month'         => [true,  '2000-01-00'],
            'day only'               => [false, '0000-00-01'],
            'month only'             => [false, '0000-01-00'],
            'year and day'           => [false, '2000-00-01'],
        ];
    }

    /**
     * @dataProvider birthdayDateProvider()
     */
    public function testSave_birthday_check_format(bool $exceptValid, string $birthdate) {
        $user = $this->Users->get(1);

        $user = $this->Users->patchEntity($user, ['birthday' => $birthdate]);

        if ($exceptValid) {
            $this->assertEmpty($user->getError('birthday'));
        } else {
            $this->assertNotEmpty($user->getError('birthday'));
        }
    }

    public function testSave_onCreate_defaults() {
        $user = $this->Users->newEntity([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'testuser',
        ]);

        $result = $this->Users->save($user);

        $this->assertNotFalse($result);
        $this->assertTrue($result->is_spamdexing);
    }

    public function testSettingsParsedAsJSON()
    {
        $user = $this->Users->get(7, ['fields' => ['settings']]);

        $this->assertEquals('CC0 1.0', $user->settings['default_license']);
    }

    public function testUpdatePasswordVersion_doesUpdate()
    {
        $password = '123456';
        $userId = 1;
        $oldPassword = $this->Users->get($userId)->password;

        $this->Users->updatePasswordVersion($userId, $password);

        $newPassword = $this->Users->get($userId)->password;
        $this->assertNotEquals($oldPassword, $newPassword);
    }

    public function testUpdatePasswordVersion_doesNotUpdate()
    {
        $password = '123456';
        $userId = 7;
        $oldPassword = $this->Users->get($userId)->password;

        $this->Users->updatePasswordVersion($userId, $password);

        $newPassword = $this->Users->get($userId)->password;
        $this->assertEquals($oldPassword, $newPassword);
    }

    public function testGetSettings()
    {
        $userSettings = $this->Users->getSettings(4);
        $expected = ['send_notifications', 'settings', 'email'];
        $result = array_keys($userSettings->toArray());
        $this->assertEquals($expected, $result);
    }

    public function testSaveSettings_withNotificationsDisabled()
    {
        $userSettings = $this->Users->getSettings(4);
        $userSettings->send_notifications = null;
        $user = $this->Users->get(1);
        $this->Users->patchEntity($user, $userSettings->toArray(), [
            'fields' => ['send_notifications', 'settings']
        ]);
        $savedUser = $this->Users->save($user);
        $this->assertNotFalse($savedUser);
    }

    public function testGetUserByIdWithExtraInfo_sentencesOrderedDesc()
    {
        $data = $this->Users->getUserByIdWithExtraInfo(4);
        $firstSentence = $data->sentences[0];
        $secondSentence = $data->sentences[1];
        $this->assertGreaterThanOrEqual(
            $secondSentence->modified, $firstSentence->modified
        );
    }

    public function testGetUserByIdWithExtraInfo_commentsContainSentence()
    {
        $data = $this->Users->getUserByIdWithExtraInfo(7);
        $comment = $data->sentence_comments[0];
        $this->assertNotEmpty($comment->sentence);
    }

    public function addProfileLinksProvider() {
        return [
            // user id, field, value for field, should be able to save
            'legacy user, inbound link'    => [1, 'homepage', 'https://tatoeba.org/en/sentences_lists/show/1234', true],
            'legacy user, outbound link'   => [1, 'homepage', 'https://example.com', true],
            'verified user, inbound link'  => [7, 'homepage', 'https://tatoeba.org/en/sentences_lists/show/1234', true],
            'verified user, outbound link' => [7, 'homepage', 'https://example.com', true],
            'new user, inbound link'       => [9, 'homepage', 'https://tatoeba.org/en/sentences_lists/show/1234', true],
            'new user, outbound link'      => [9, 'homepage', 'https://example.com', false],
        ];
    }

    /**
     * @dataProvider addProfileLinksProvider()
     */
    public function testAddProfileLinks($userId, $field, $value, $expectedToSave)
    {
        $user = $this->Users->get($userId);
        CurrentUser::store($user);
        $this->Users->patchEntity($user, [$field => $value]);

        $savedUser = $this->Users->save($user);

        if ($expectedToSave) {
            $this->assertNotFalse($savedUser);
            $this->assertEquals($value, $savedUser->{$field});
        } else {
            $this->assertFalse($savedUser);
        }
    }
}
