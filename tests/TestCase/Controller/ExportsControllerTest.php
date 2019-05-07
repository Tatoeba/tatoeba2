<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class ExportsControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'app.Exports',
        'app.Users',
        'app.UsersLanguages',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    private function logInAs($username) {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->session(['Auth' => ['User' => $user->toArray()]]);
        $this->enableCsrfToken();
    }

    public function testDownload_asOwner()
    {
        $this->logInAs('kazuki');
        $exportUrl = 'https://downloads.tatoeba.org/exports/kazuki_sentences.zip';

        $this->get("/eng/exports/download/1");

        $this->assertRedirect($exportUrl);
    }

    public function testDownload_asOtherMember()
    {
        $this->logInAs('contributor');

        $this->get("/eng/exports/download/1");

        $this->assertResponseError();
    }

    public function testDownload_asGuest()
    {
        $this->get("/eng/exports/download/1");

        $this->assertRedirectContains('/eng/users/login');
    }

    public function testDownload_invalidId()
    {
        $this->logInAs('kazuki');

        $this->get("/eng/exports/download/9999999");

        $this->assertResponseCode(404);
    }
}
