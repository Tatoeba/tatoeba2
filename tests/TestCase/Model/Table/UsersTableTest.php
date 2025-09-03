<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class UsersTableTest extends TestCase
{
    public $Users;

    public $fixtures = [
        'app.users',
        'app.sentences',
        'app.contributions',
        'app.sentence_comments',
        'app.walls',
    ];

    public function setUp()
    {
        parent::setUp();

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

    public function testSave_birthday_ok_valid_day() {
        $user = $this->Users->get(1);

        $user = $this->Users->patchEntity($user, ['birthday' => '2000-02-29']);

        $this->assertEmpty($user->getError('birthday'));
    }

    public function testSave_birthday_ok_valid_day_leap_year_without_year() {
        $user = $this->Users->get(1);

        $user = $this->Users->patchEntity($user, ['birthday' => '0000-02-29']);

        $this->assertEmpty($user->getError('birthday'));
    }

    public function testSave_birthday_fail_invalid_day() {
        $user = $this->Users->get(1);

        $user = $this->Users->patchEntity($user, ['birthday' => '2000-10-42']);

        $this->assertNotEmpty($user->getError('birthday'));
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
}
