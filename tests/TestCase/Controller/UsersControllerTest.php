<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;

class UsersControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.contributions',
        'app.users',
        'app.groups',
        'app.users_languages',
        'app.last_contributions',
        'app.private_messages',
        'app.sentence_comments',
        'app.sentences',
        'app.walls',
    ];

    public function setUp() {
        parent::setUp();

        Configure::write('Acl.database', 'test');
        Security::setSalt('ze@9422#5dS?!99xx');
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    public function redirectsProvider() {
        return [
            // url, user, redirection url
            [ '/eng/users/index', false, '/eng/users/login?redirect=%2Feng%2Fusers%2Findex' ],
            [ '/eng/users/index', 'contributor', '/' ],
            [ '/eng/users/index', 'advanced_contributor', '/' ],
            [ '/eng/users/index', 'corpus_maintainer', '/' ],
            [ '/eng/users/index', 'admin', false ],
            [ '/eng/users/edit/1', false, '/eng/users/login?redirect=%2Feng%2Fusers%2Fedit%2F1' ],
            [ '/eng/users/edit/1', 'contributor', '/' ],
            [ '/eng/users/edit/1', 'advanced_contributor', '/' ],
            [ '/eng/users/edit/1', 'corpus_maintainer', '/' ],
            [ '/eng/users/edit/1', 'admin', false ],
            [ '/eng/users/edit/999999999999999', 'admin', '/eng/users/index' ],
            [ '/eng/users/delete/1', false, '/eng/users/login?redirect=%2Feng%2Fusers%2Fdelete%2F1' ],
            [ '/eng/users/delete/1', 'contributor', '/' ],
            [ '/eng/users/delete/1', 'advanced_contributor', '/' ],
            [ '/eng/users/delete/1', 'corpus_maintainer', '/' ],
            [ '/eng/users/delete/1', 'admin', '/eng/users/index' ],
            [ '/eng/users/delete/999999999999999', 'admin', '/eng/users/index' ],
            [ '/eng/users/login', false, false ],
            [ '/eng/users/login', 'contributor', '/' ],
            [ '/eng/users/check_login', false, '/eng/users/login?redirectTo=%2F' ],
            [ '/eng/users/logout', false, '/eng/users/login' ], // TODO we might want not to redirect to login page when trying to access the logout page as a guest
            [ '/eng/users/logout', 'contributor', '/eng/users/login' ],
            [ '/eng/users/register', false, false ],
            [ '/eng/users/register', 'contributor', '/' ],
            [ '/eng/users/new_password', false, false ],
            [ '/eng/users/new_password', 'contributor', false ],
            [ '/eng/users/show/1', false, false ],
            [ '/eng/users/show/1', 'contributor', false ],
            [ '/eng/users/all', false, false ],
            [ '/eng/users/all', 'contributor', false ],
            [ '/eng/users/check_username/foobar', false, false ],
            [ '/eng/users/check_username/foobar', 'contributor', false ],
            [ '/eng/users/check_email/foobar@example.net', false, false ],
            [ '/eng/users/check_email/foobar@example.net', 'contributor', false ],
            [ '/eng/users/for_language', false, false ],
            [ '/eng/users/for_language', 'contributor', false ],
            [ '/eng/users/for_language/jav', false, false ],
            [ '/eng/users/for_language/jav', 'contributor', false ],
        ];
    }

    /**
     * @dataProvider redirectsProvider
     */
    public function testUsersControllerAccess($url, $user, $redirect) {
        $this->assertRedirectionAs($url, $user, $redirect);
    }

    public function testSearch_found() {
        $this->post('/eng/users/search', [ 'username' => 'contributor' ]);
        $this->assertRedirect('/eng/users/show/4');
    }

    public function testSearch_notFound() {
        $this->post('/eng/users/search', [ 'username' => 'non existent' ]);
        $this->assertRedirect('/eng/users/all/');
    }

    public function testCheckLogin_correctLoginAndPasswordV0() {
        $this->post('/eng/users/check_login', [
            'username' => 'contributor',
            'password' => '123456',
            'rememberMe' => 0,
        ]);
        $this->assertSession('contributor', 'Auth.User.username');
        $this->assertRedirect('/');
    }

    public function testCheckLogin_correctLoginAndincorrectPasswordV0() {
        $this->post('/eng/users/check_login', [
            'username' => 'contributor',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login?redirectTo=%2F');
    }

    public function testCheckLogin_incorrectLoginAndPassword() {
        $this->post('/eng/users/check_login', [
            'username' => 'this_user_does_not_exist',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login?redirectTo=%2F');
    }

    public function testCheckLogin_correctLoginAndPassowrdV1() {
        $this->post('/eng/users/check_login', [
            'username' => 'kazuki',
            'password' => 'myAwesomePassword',
            'rememberMe' => 0,
        ]);
        $this->assertSession('kazuki', 'Auth.User.username');
    }

    public function testCheckLogin_correctLoginAndIncorrectPassowrdV1() {
        $this->post('/eng/users/check_login', [
            'username' => 'kazuki',
            'password' => 'this_is_incorrect',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login?redirectTo=%2F');
    }

    public function testCheckLogin_userWithOldStylePasswordCannotLogin() {
        $this->post('/eng/users/check_login', [
            'username' => 'mr_old_style_passwd',
            'password' => '123456',
            'rememberMe' => 0,
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertRedirect('/eng/users/login?redirectTo=%2F');
    }

    public function testCheckLogin_canRegister() {
        $this->post('/eng/users/register', [
            'username' => 'polochon',
            'password' => 'very bad password',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polochon@example.net',
            'quiz' => 'poloc',
        ]);
        $this->assertSession('polochon', 'Auth.User.username');
        $this->assertRedirect('/eng');
    }

    public function testCheckLogin_cannotRegisterWithEmptyPassword() {
        $this->post('/eng/users/register', [
            'username' => 'polochon',
            'password' => '',
            'language' => 'none',
            'acceptation_terms_of_use' => '1',
            'email' => 'polochon@example.net',
            'quiz' => 'poloc',
        ]);
        $this->assertSession(null, 'Auth.User.username');
        $this->assertResponseOk();
    }

    public function testCheckLogin_loginUpdatedPasswordVersion() {
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
