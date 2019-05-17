<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\Constraint\Response\HeaderNotSet;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class ExportsControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'app.Exports',
        'app.Users',
        'app.UsersLanguages',
    ];

    private $testExportDir = TMP.'export_tests'.DS;

    public function setUp() {
        parent::setUp();

        Configure::write('Acl.database', 'test');

        $folder = new Folder($this->testExportDir);
        $folder->delete();
        if (!$folder->create($this->testExportDir)) {
            die("Couldn't create test directory '{$this->testExportDir}'");
        }
    }

    public function tearDown() {
        $folder = new Folder($this->testExportDir);
        $folder->delete();
        parent::tearDown();
    }

    private function logInAs($username) {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->session(['Auth' => ['User' => $user->toArray()]]);
        $this->enableCsrfToken();
    }

    public function assertNoHeader($header, $message = '')
    {
        $verboseMessage = $this->extractVerboseMessage($message);
        $this->assertThat(null, new HeaderNotSet($this->_response, $header), $verboseMessage);
    }

    public function testDownload_asOwner()
    {
        $this->logInAs('kazuki');

        $exportedFile = 'kazuki_sentences.zip';
        $file = new File($this->testExportDir.$exportedFile, true);
        $file->write("some zipped content");
        $file->close();

        $this->get("/eng/exports/download/1/A pretty filename.zip");

        $this->assertResponseOk();
        $this->assertContentType('application/zip');
        $this->assertHeader('Content-Length', '19');
        $this->assertHeader('Accept-Ranges', 'bytes');
        $this->assertNoHeader('Content-Disposition');
        $this->assertHeader('X-Accel-Redirect', '/export_tests/kazuki_sentences.zip');
        $this->assertResponseEquals('');
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

    public function testDownload_cannotDownloadUntilReady()
    {
        $this->logInAs('kazuki');

        $export = TableRegistry::get('Exports')->get(2);
        $file = new File($export->filename, true);
        $file->close();

        $this->get("/eng/exports/download/2");

        $this->assertResponseCode(404);
        $this->assertResponseNotContains($export->filename);
    }
}
