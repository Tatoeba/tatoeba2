<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class UsersControllerTest extends IntegrationTestCase {
	public $fixtures = [
		'app.aros',
		'app.acos',
		'app.aros_acos',
		'app.users',
		'app.groups',
		'app.users_languages',
		'app.last_contributions',
	];

	function setUp() {
		parent::setUp();

		Configure::write('Acl.database', 'test');
		Configure::write('Security.salt', 'ze@9422#5dS?!99xx');
		$this->enableCsrfToken();
		$this->enableSecurityToken();
	}

	function testCheckLogin_correctLoginAndPasswordV0() {
		$this->post('/eng/users/check_login', [
			'username' => 'contributor',
			'password' => '123456',
			'rememberMe' => 0,
		]);
		$this->assertSession('contributor', 'Auth.User.username');
	}

	function testCheckLogin_correctLoginAndincorrectPasswordV0() {
		$this->post('/eng/users/check_login', [
			'username' => 'contributor',
			'password' => 'this_is_incorrect',
			'rememberMe' => 0,
		]);
		$this->assertSession(null, 'Auth.User.username');
	}

	function testCheckLogin_incorrectLoginAndPassword() {
		$this->post('/eng/users/check_login', [
			'username' => 'this_user_does_not_exist',
			'password' => 'this_is_incorrect',
			'rememberMe' => 0,
		]);
		$this->assertSession(null, 'Auth.User.username');
	}

	function testCheckLogin_correctLoginAndPassowrdV1() {
		$this->post('/eng/users/check_login', [
			'username' => 'kazuki',
			'password' => 'myAwesomePassword',
			'rememberMe' => 0,
		]);
		$this->assertSession('kazuki', 'Auth.User.username');
	}

	function testCheckLogin_correctLoginAndIncorrectPassowrdV1() {
		$this->post('/eng/users/check_login', [
			'username' => 'kazuki',
			'password' => 'this_is_incorrect',
			'rememberMe' => 0,
		]);
		$this->assertSession(null, 'Auth.User.username');
	}

	function testCheckLogin_userWithOldStylePasswordCannotLogin() {
		$this->post('/eng/users/check_login', [
			'username' => 'mr_old_style_passwd',
			'password' => '123456',
			'rememberMe' => 0,
		]);
		$this->assertSession(null, 'Auth.User.username');
	}

	function testCheckLogin_canRegister() {
		$this->post('/eng/users/register', [
			'username' => 'polochon',
			'password' => 'very bad password',
			'language' => 'none',
			'acceptation_terms_of_use' => '1',
			'email' => 'polochon@example.net',
			'quiz' => 'poloc',
		]);
		$this->assertSession('polochon', 'Auth.User.username');
	}

	function testCheckLogin_cannotRegisterWithEmptyPassword() {
		$this->post('/eng/users/register', [
			'username' => 'polochon',
			'password' => '',
			'language' => 'none',
			'acceptation_terms_of_use' => '1',
			'email' => 'polochon@example.net',
			'quiz' => 'poloc',
		]);
		$this->assertSession(null, 'Auth.User.username');
	}

	function testCheckLogin_loginUpdatedPasswordVersion() {
		$this->post('/eng/users/check_login', [
			'username' => 'contributor',
			'password' => '123456',
			'rememberMe' => 0,
		]);

		$users = TableRegistry::get('Users');
		$user = $users->find()->where(['username' => 'contributor'])->first();
		list($version, $hash) = explode(' ', $user->password, 2);
		$this->assertEquals(1, $version);
	}

	public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
		$lastPage = 2;
		$newUsers = [];
		for ($i = 1; $i <= 20; $i++) {
			$newUsers[] = [
				'username' => "foobar_$i",
				'password' => "very_bad_password_$i",
				'email' => "foobar_$i@example.com",
			];
		}
		$users = TableRegistry::get('Users');
		$entities = $users->newEntities($newUsers);
		$result = $users->saveMany($entities);

		$this->get('/eng/users/all?page=9999999&sort=User.group_id&direction=asc');

		$this->assertRedirect("/eng/users/all?page=$lastPage&sort=User.group_id&direction=asc");
	}
}
