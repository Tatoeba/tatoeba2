<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\CurrentUser;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

class TranslationImportsTableTest extends TestCase
{
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
    ];

    private $imports;
    private $sentences;
    private $previousAllowedIds;

    public function setUp(): void
    {
        parent::setUp();

        $this->previousAllowedIds = Configure::read(
            'TranslationImports.allowedUserIds'
        );

        Configure::write('TranslationImports.allowedUserIds', [4]);

        $this->imports = $this->fetchTable('TranslationImports');
        $this->sentences = $this->fetchTable('Sentences');
    }

    public function tearDown(): void
    {
        CurrentUser::store(null);

        Configure::write(
            'TranslationImports.allowedUserIds',
            $this->previousAllowedIds
        );

        parent::tearDown();
    }

    private function preview(int $count = 1)
    {
        $source = $this->sentences->get(1);
        $rows = [];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'record_number' => $i,
                'sentence_id' => 1,
                'source_text' => $source->text,
                'translation_text' => 'Eine Importübersetzung Nummer ' .
                    $i . '.',
            ];
        }

        return $this->imports->createPreview(4, $rows, [
            'source_lang' => 'eng',
            'target_lang' => 'deu',
            'context' => 'Local test',
            'filename' => 'test.csv',
        ]);
    }

    public function testPreviewDoesNotCreateSentences()
    {
        $before = $this->sentences->find()->count();

        $import = $this->preview();

        $this->assertSame('previewed', $import->status);
        $this->assertSame(
            $before,
            $this->sentences->find()->count()
        );
    }

    public function testConfirmationAndRepeatedJobAreIdempotent()
    {
        $import = $this->preview();
        $this->imports->queueImport((int)$import->id, 4);

        $jobId = (int)$this->imports->get($import->id)->queued_job_id;

        $this->imports->queueImport((int)$import->id, 4);
        $this->assertSame(
            $jobId,
            (int)$this->imports->get($import->id)->queued_job_id
        );

        $this->imports->runBatch((int)$import->id, $jobId);

        $item = $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $import->id])
            ->firstOrFail();

        $this->assertSame('imported', $item->status);

        $translation = $this->sentences->get($item->translation_id);
        $this->assertSame(4, (int)$translation->user_id);
        $this->assertSame(1, (int)$translation->based_on_id);

        $this->assertTrue($this->sentences->Links->exists([
            'sentence_id' => 1,
            'translation_id' => $translation->id,
        ]));
        $this->assertTrue($this->sentences->Links->exists([
            'sentence_id' => $translation->id,
            'translation_id' => 1,
        ]));

        $before = $this->sentences->find()->count();
        $this->imports->runBatch((int)$import->id, $jobId);

        $this->assertSame(
            $before,
            $this->sentences->find()->count()
        );
    }

    public function testSourceChangedAfterPreviewIsSkipped()
    {
        $import = $this->preview();

        $this->sentences->updateAll(
            ['text' => 'The source has changed.'],
            ['id' => 1]
        );

        $this->imports->queueImport((int)$import->id, 4);
        $jobId = (int)$this->imports->get($import->id)->queued_job_id;
        $before = $this->sentences->find()->count();

        $this->imports->runBatch((int)$import->id, $jobId);

        $item = $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $import->id])
            ->firstOrFail();

        $this->assertSame('skipped', $item->status);
        $this->assertSame('source_changed', $item->message);
        $this->assertSame(
            $before,
            $this->sentences->find()->count()
        );
    }

    /**
     * Queue a continuation after 50 records and stop the obsolete job.
     */
    public function testAnotherBatchIsQueuedAndOldJobStops()
    {
        $import = $this->preview(51);
        $this->imports->queueImport((int)$import->id, 4);

        $firstJobId = (int)$this->imports
            ->get($import->id)->queued_job_id;

        $this->imports->runBatch((int)$import->id, $firstJobId);

        $nextJobId = (int)$this->imports
            ->get($import->id)->queued_job_id;

        $this->assertNotSame($firstJobId, $nextJobId);

        // An obsolete job must not process the last record.
        $this->imports->runBatch((int)$import->id, $firstJobId);

        $ready = $this->imports->TranslationImportItems->find()
            ->where([
                'translation_import_id' => $import->id,
                'status' => 'ready',
            ])
            ->count();

        $this->assertSame(1, $ready);

        $this->imports->runBatch((int)$import->id, $nextJobId);

        $this->assertSame(
            'completed',
            $this->imports->get($import->id)->status
        );
    }

    public function testDiscardDeletesOnlyPreviewAndItsItems()
    {
        $import = $this->preview(2);
        $other = $this->preview();
        $before = $this->sentences->find()->count();
        $linksBefore = $this->sentences->Links->find()->count();

        $this->assertTrue($this->imports->discardPreview((int)$import->id, 4));

        $this->assertFalse($this->imports->exists(['id' => $import->id]));
        $this->assertFalse($this->imports->TranslationImportItems->exists([
            'translation_import_id' => $import->id,
        ]));
        $this->assertTrue($this->imports->exists(['id' => $other->id]));
        $this->assertSame(1, $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $other->id])->count());
        $this->assertSame($before, $this->sentences->find()->count());
        $this->assertSame($linksBefore, $this->sentences->Links->find()->count());
    }

    public function testDeletedPreviewCannotBeQueued()
    {
        $import = $this->preview();
        $this->imports->discardPreview((int)$import->id, 4);

        $this->expectException(\DomainException::class);
        $this->imports->queueImport((int)$import->id, 4);
    }

    public function testFailedDiscardRestoresPreviewAndItems()
    {
        $import = $this->preview(2);
        $rejectDelete = function ($event) {
            $event->stopPropagation();
            return false;
        };
        $events = $this->imports->getEventManager();
        $events->on('Model.beforeDelete', $rejectDelete);

        try {
            $this->imports->discardPreview((int)$import->id, 4);
            $this->fail('Discard must fail when the import cannot be deleted.');
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $this->assertSame(
                'previewed',
                $this->imports->get($import->id)->status
            );
        } finally {
            $events->off('Model.beforeDelete', $rejectDelete);
        }

        $this->assertSame(2, $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $import->id])->count());
    }

    public function testOnlyOwnerCanDiscardPreview()
    {
        $import = $this->preview();
        Configure::write('TranslationImports.allowedUserIds', [1, 4]);

        $this->expectException(\DomainException::class);
        $this->imports->discardPreview((int)$import->id, 1);
    }

    public function testQueuedAndCompletedImportsCannotBeDiscarded()
    {
        $import = $this->preview();
        $this->imports->queueImport((int)$import->id, 4);
        $jobId = (int)$this->imports->get($import->id)->queued_job_id;

        $this->assertFalse($this->imports->discardPreview((int)$import->id, 4));
        $this->imports->runBatch((int)$import->id, $jobId);
        $this->assertFalse($this->imports->discardPreview((int)$import->id, 4));
        $this->assertSame('completed', $this->imports->get($import->id)->status);
    }

    public function testPermissionRevokedBeforeProcessingStopsImport()
    {
        $import = $this->preview();
        $this->imports->queueImport((int)$import->id, 4);
        $jobId = (int)$this->imports->get($import->id)->queued_job_id;
        $before = $this->sentences->find()->count();
        Configure::write('TranslationImports.allowedUserIds', []);

        $this->imports->runBatch((int)$import->id, $jobId);

        $import = $this->imports->get($import->id);
        $this->assertSame('failed', $import->status);
        $this->assertSame('permission_revoked', $import->message);
        $this->assertSame($before, $this->sentences->find()->count());

        Configure::write('TranslationImports.allowedUserIds', [4]);
        $this->imports->queueImport((int)$import->id, 4, true);
        $nextJobId = (int)$this->imports->get($import->id)->queued_job_id;
        $this->assertNotSame($jobId, $nextJobId);
        $this->imports->runBatch((int)$import->id, $nextJobId);
        $this->assertSame('completed', $this->imports->get($import->id)->status);
    }

    public function testInactiveUserCannotImportEvenWhenAllowlisted()
    {
        $user = $this->imports->Users->get(4)->toArray();
        $user['role'] = \App\Model\Entity\User::ROLE_INACTIVE;

        $this->assertFalse($this->imports->canImport($user));
    }

    public function testDuplicateRecordsAreSkippedAndTextIsNormalized()
    {
        $row = [
            'record_number' => 1,
            'sentence_id' => 1,
            'source_text' => $this->sentences->get(1)->text,
            'translation_text' => "  Eine\nÜbersetzung.  ",
        ];
        $import = $this->imports->createPreview(4, [
            $row,
            array_merge($row, ['record_number' => 2]),
        ], ['source_lang' => 'eng', 'target_lang' => 'deu']);
        $items = $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $import->id])
            ->orderAsc('record_number')->all()->toList();

        $this->assertSame('Eine Übersetzung.', $items[0]->translation_text);
        $this->assertSame('ready', $items[0]->status);
        $this->assertSame('skipped', $items[1]->status);
        $this->assertSame('duplicate_in_file', $items[1]->message);
    }

    public function testAlreadyLinkedTranslationIsSkipped()
    {
        $import = $this->preview();
        $this->imports->queueImport((int)$import->id, 4);
        $this->imports->runBatch(
            (int)$import->id,
            (int)$this->imports->get($import->id)->queued_job_id
        );
        $before = $this->sentences->find()->count();

        $second = $this->preview();
        $item = $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $second->id])->firstOrFail();

        $this->assertSame('skipped', $item->status);
        $this->assertSame('already_linked', $item->message);
        $this->assertNotNull($item->translation_id);
        $this->assertSame($before, $this->sentences->find()->count());
    }

    public function testExistingSentenceIsReusedWithoutChangingItsOwner()
    {
        $this->sentences->updateAll(['user_id' => 1], ['id' => 5]);
        $this->sentences->Links->deleteAll(['OR' => [
            ['sentence_id' => 1, 'translation_id' => 5],
            ['sentence_id' => 5, 'translation_id' => 1],
        ]]);
        $import = $this->imports->createPreview(4, [[
            'record_number' => 1,
            'sentence_id' => 1,
            'source_text' => $this->sentences->get(1)->text,
            'translation_text' => $this->sentences->get(5)->text,
        ]], ['source_lang' => 'eng', 'target_lang' => 'deu']);
        $before = $this->sentences->find()->count();
        $this->imports->queueImport((int)$import->id, 4);
        $this->imports->runBatch(
            (int)$import->id,
            (int)$this->imports->get($import->id)->queued_job_id
        );

        $item = $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $import->id])->firstOrFail();
        $this->assertSame('imported', $item->status);
        $this->assertSame(5, (int)$item->translation_id);
        $this->assertSame(1, (int)$this->sentences->get(5)->user_id);
        $this->assertSame($before, $this->sentences->find()->count());
        $this->assertTrue($this->sentences->Links->exists([
            'sentence_id' => 1, 'translation_id' => 5,
        ]));
        $this->assertTrue($this->sentences->Links->exists([
            'sentence_id' => 5, 'translation_id' => 1,
        ]));
    }

    public function testMissingLinkRollsBackSentenceAndRecordsError()
    {
        $import = $this->preview();
        $this->imports->queueImport((int)$import->id, 4);
        $jobId = (int)$this->imports->get($import->id)->queued_job_id;
        $before = $this->sentences->find()->count();
        $linksBefore = $this->sentences->Links->find()->count();
        $links = $this->getMockForModel('Links', ['add']);
        $this->sentences->getAssociation('Links')->setTarget($links);
        $links->expects($this->once())->method('add')->willReturn(false);

        $this->imports->runBatch((int)$import->id, $jobId);

        $item = $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $import->id])->firstOrFail();
        $this->assertSame('error', $item->status);
        $this->assertSame('save_failed', $item->message);
        $this->assertNull($item->translation_id);
        $this->assertSame($before, $this->sentences->find()->count());
        $this->assertSame($linksBefore, $links->find()->count());
    }

    public function testResumeReplacesJobAndRestoresCurrentUser()
    {
        $import = $this->preview();
        $this->imports->queueImport((int)$import->id, 4);
        $oldJob = (int)$this->imports->get($import->id)->queued_job_id;
        $this->imports->queueImport((int)$import->id, 4, true);
        $newJob = (int)$this->imports->get($import->id)->queued_job_id;
        $this->assertNotSame($oldJob, $newJob);

        $previousUser = $this->imports->Users->get(1)->toArray();
        CurrentUser::store($previousUser);
        $before = $this->sentences->find()->count();
        $this->imports->runBatch((int)$import->id, $oldJob);
        $this->assertSame($before, $this->sentences->find()->count());
        $this->imports->runBatch((int)$import->id, $newJob);
        $this->assertSame($previousUser, CurrentUser::get('User'));
    }

    public function changedSourcesProvider()
    {
        return [
            [['lang' => 'fra'], 'source_language'],
            [['license' => ''], 'source_unlicensed'],
        ];
    }

    /**
     * @dataProvider changedSourcesProvider
     */
    public function testSourceIsRevalidatedBeforeSaving($changes, $message)
    {
        $import = $this->preview();
        $this->sentences->updateAll($changes, ['id' => 1]);
        $this->imports->queueImport((int)$import->id, 4);
        $before = $this->sentences->find()->count();
        $this->imports->runBatch(
            (int)$import->id,
            (int)$this->imports->get($import->id)->queued_job_id
        );

        $item = $this->imports->TranslationImportItems->find()
            ->where(['translation_import_id' => $import->id])->firstOrFail();
        $this->assertSame('skipped', $item->status);
        $this->assertSame($message, $item->message);
        $this->assertSame($before, $this->sentences->find()->count());
    }
}
