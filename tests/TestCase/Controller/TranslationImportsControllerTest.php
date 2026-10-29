<?php
namespace App\Test\TestCase\Controller;

use App\Model\CurrentUser;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;
use Laminas\Diactoros\UploadedFile;

class TranslationImportsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.TranslationImports',
        'app.TranslationImportItems',
        'app.QueuedJobs',
        'app.Users',
        'app.UsersLanguages',
        'app.Sentences',
        'app.Links',
        'app.Contributions',
        'app.Languages',
        'app.ReindexFlags',
        'app.Transcriptions',
        'app.Audios',
        'app.DisabledAudios',
        'app.SentencesLists',
        'app.SentencesSentencesLists',
        'app.UsersSentences',
        'app.FavoritesUsers',
        'app.Tags',
        'app.TagsSentences',
        'app.WikiArticles',
        'app.PrivateMessages',
    ];

    private $previousAllowedIds;
    private $previousWorkingDirectory;

    public function setUp(): void
    {
        parent::setUp();
        // The layout discovers controller CSS relative to the webroot.
        $this->previousWorkingDirectory = getcwd();
        chdir(WWW_ROOT);
        $this->previousAllowedIds = Configure::read(
            'TranslationImports.allowedUserIds'
        );
        Configure::write('TranslationImports.allowedUserIds', [4]);
    }

    public function tearDown(): void
    {
        CurrentUser::store(null);
        chdir($this->previousWorkingDirectory);
        Configure::write(
            'TranslationImports.allowedUserIds',
            $this->previousAllowedIds
        );
        parent::tearDown();
    }

    private function preview($sentenceId = 1, $filename = 'controller-test.csv')
    {
        return $this->fetchTable('TranslationImports')->createPreview(4, [[
            'record_number' => 2,
            'sentence_id' => $sentenceId,
            'source_text' => $this->fetchTable('Sentences')->get(1)->text,
            'translation_text' => 'Eine Übersetzung {{7*7}} <b>Test</b>.',
        ]], [
            'source_lang' => 'eng',
            'target_lang' => 'deu',
            'context' => 'Project {{7*7}}',
            'filename' => $filename,
        ]);
    }

    public function testGuestIsRedirectedToLogin()
    {
        $this->get('/en/translation_imports/index');
        $this->assertRedirect(
            '/en/users/login?redirect=%2Fen%2Ftranslation_imports%2Findex'
        );
    }

    public function testUnlistedUserIsDenied()
    {
        $this->logInAs('admin');
        $this->get('/en/translation_imports/index');
        $this->assertResponseCode(403);
    }

    public function testEmptyHistoryAndResponsiveStyles()
    {
        $this->logInAs('contributor');
        $this->get('/en/translation_imports/index');
        $this->assertResponseOk();
        $this->assertResponseContains('You have no translation imports yet.');
        $this->assertResponseContains('translation_imports.css');
        $this->assertResponseContains('name="viewport"');
    }

    public function testPreviewEscapesTextAndDisplaysCounts()
    {
        $import = $this->preview(1, '{{7*7}}<b>test</b>.csv');
        $this->logInAs('contributor');
        $this->get('/en/translation_imports/view/' . $import->id);

        $this->assertResponseOk();
        $this->assertResponseContains('English');
        $this->assertResponseContains('German');
        $this->assertResponseContains('<tbody ng-non-bindable>');
        $this->assertResponseContains('{{7*7}}&lt;b&gt;test&lt;/b&gt;.csv');
        $this->assertResponseContains('&lt;b&gt;Test&lt;/b&gt;');
        $this->assertResponseContains('Discard preview');
        $this->assertResponseNotContains('{count}');
        $this->assertResponseNotContains('{0}');
        $this->assertSame(1, $this->viewVariable('counts')['ready']);
    }

    public function testRejectedPreviewCannotBeConfirmedInInterface()
    {
        $import = $this->preview(2147483647);
        $this->logInAs('contributor');
        $this->get('/en/translation_imports/view/' . $import->id);

        $this->assertResponseOk();
        $this->assertResponseContains('Nothing to import');
        $this->assertResponseContains('Discard preview');
        $this->assertResponseNotContains('name="confirmed"');
        $this->assertSame(1, $this->viewVariable('counts')['skipped']);
    }

    public function testUploadConfirmationProcessingAndReport()
    {
        $this->logInAs('contributor');
        $sentences = $this->fetchTable('Sentences');
        $before = $sentences->find()->count();
        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, [1, $sentences->get(1)->text, 'Ein HTTP-Importtest.']);
        $size = ftell($stream);
        rewind($stream);
        $file = new UploadedFile($stream, $size, UPLOAD_ERR_OK, 'http-test.csv');
        $this->configRequest(['files' => ['file' => $file]]);
        $this->post('/en/translation_imports/create', [
            'file' => $file,
            'source_lang' => 'eng',
            'target_lang' => 'deu',
            'delimiter' => 'comma',
            'context' => 'HTTP test',
        ]);

        $imports = $this->fetchTable('TranslationImports');
        $import = $imports->find()->firstOrFail();
        $this->assertRedirect('/en/translation_imports/view/' . $import->id);
        $this->assertSame($before, $sentences->find()->count());

        $this->configRequest(['files' => []]);
        $this->post('/en/translation_imports/confirm/' . $import->id, [
            'confirmed' => '1',
        ]);
        $this->assertRedirect('/en/translation_imports/view/' . $import->id);
        $import = $imports->get($import->id);
        $this->assertSame('queued', $import->status);
        $this->get('/en/translation_imports/view/' . $import->id);
        $this->assertResponseOk();
        $this->assertResponseContains('Your import is waiting to be processed.');
        $this->assertResponseNotContains('name="confirmed"');
        $imports->runBatch((int)$import->id, (int)$import->queued_job_id);

        $this->get('/en/translation_imports/report/' . $import->id);
        $this->assertResponseOk();
        $this->assertContentType('application/json');
        $this->assertHeaderContains('Content-Disposition', 'attachment;');
        $report = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame('completed', $report['import']['status']);
        $this->assertSame('imported', $report['items'][0]['status']);
        $this->assertSame($before + 1, $sentences->find()->count());
    }

    public function testConfirmationRequiresCheckbox()
    {
        $import = $this->preview();
        $this->logInAs('contributor');
        $this->post('/en/translation_imports/confirm/' . $import->id);

        $this->assertRedirect('/en/translation_imports/view/' . $import->id);
        $this->assertSame('previewed', $this->fetchTable('TranslationImports')
            ->get($import->id)->status);
        $this->assertFlashMessage(
            'Please confirm that you reviewed your translations.'
        );
    }

    public function testConfirmationRequiresCsrfToken()
    {
        $import = $this->preview();
        $this->logInAs('contributor', false);
        $this->post('/en/translation_imports/confirm/' . $import->id, [
            'confirmed' => '1',
        ]);
        $this->assertResponseCode(403);
        $this->assertSame('previewed', $this->fetchTable('TranslationImports')
            ->get($import->id)->status);
    }

    public function testInvalidUploadDoesNotCreateImport()
    {
        $this->logInAs('contributor');
        $this->post('/en/translation_imports/create');
        $this->assertRedirect('/en/translation_imports/index');
        $this->assertFlashMessage('Please upload a valid translation file.');
        $this->assertSame(
            0,
            $this->fetchTable('TranslationImports')->find()->count()
        );
    }

    public function testDiscardDeletesOnlyTheSelectedPreview()
    {
        $import = $this->preview();
        $other = $this->preview(1, 'keep-this.csv');
        $this->logInAs('contributor');
        $this->post('/en/translation_imports/discard/' . $import->id);

        $this->assertRedirect('/en/translation_imports/index');
        $imports = $this->fetchTable('TranslationImports');
        $this->assertFalse($imports->exists(['id' => $import->id]));
        $this->assertFalse($imports->TranslationImportItems->exists([
            'translation_import_id' => $import->id,
        ]));

        $this->get('/en/translation_imports/index');
        $this->assertResponseNotContains('controller-test.csv');
        $this->assertResponseContains('keep-this.csv');
        $this->assertResponseContains('Import #' . $other->id);

        $this->get('/en/translation_imports/view/' . $import->id);
        $this->assertResponseCode(404);
        $this->get('/en/translation_imports/report/' . $import->id);
        $this->assertResponseCode(404);
    }

    public function testDiscardDoesNotHideQueuedImport()
    {
        $import = $this->preview();
        $imports = $this->fetchTable('TranslationImports');
        $imports->queueImport((int)$import->id, 4);
        $this->logInAs('contributor');
        $this->post('/en/translation_imports/discard/' . $import->id);

        $this->assertRedirect('/en/translation_imports/view/' . $import->id);
        $this->assertFlashMessage(
            'Only an unconfirmed preview can be discarded.'
        );
        $this->assertSame('queued', $imports->get($import->id)->status);
    }

    public function testUnknownImportReturnsNotFound()
    {
        $this->logInAs('contributor');
        $this->get('/en/translation_imports/view/2147483647');
        $this->assertResponseCode(404);
    }

    public function importActionsProvider()
    {
        return [
            ['view', 'get'],
            ['report', 'get'],
            ['confirm', 'post'],
            ['resume', 'post'],
            ['discard', 'post'],
        ];
    }

    /**
     * @dataProvider importActionsProvider
     */
    public function testOtherUsersCannotAccessImport($action, $method)
    {
        $import = $this->preview();
        Configure::write('TranslationImports.allowedUserIds', [1, 4]);
        $this->logInAs('admin');
        $this->{$method}('/en/translation_imports/' . $action . '/' . $import->id);
        $this->assertResponseCode(403);
        $this->assertSame('previewed', $this->fetchTable('TranslationImports')
            ->get($import->id)->status);
    }

    public function mutationActionsProvider()
    {
        return [['create'], ['confirm'], ['resume'], ['discard']];
    }

    /**
     * @dataProvider mutationActionsProvider
     */
    public function testMutationsRequirePost($action)
    {
        $import = $this->preview();
        $this->logInAs('contributor');
        $this->get('/en/translation_imports/' . $action . '/' . $import->id);
        $this->assertResponseCode(405);
    }
}
