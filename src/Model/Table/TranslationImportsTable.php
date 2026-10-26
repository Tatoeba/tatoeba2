<?php
namespace App\Model\Table;

use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use App\Model\Entity\User;
use App\Model\TranslationImportCsv;
use Cake\Core\Configure;
use Cake\Datasource\FactoryLocator;
use Cake\ORM\Table;
use DomainException;
use InvalidArgumentException;

/**
 * Previews and imports translations using the ordinary sentence save path.
 */
class TranslationImportsTable extends Table
{
    public const BATCH_SIZE = 50;

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('translation_imports');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsTo('Users');
        $this->belongsTo('Queue.QueuedJobs');
        $this->hasMany('TranslationImportItems');
    }

    /**
     * Check the contributor role and configured import allowlist.
     *
     * @param array $user User data.
     * @return bool
     */
    public static function canImport(array $user): bool
    {
        $allowedIds = array_map(
            'intval',
            (array)Configure::read(
                'TranslationImports.allowedUserIds',
                []
            )
        );

        return in_array((int)($user['id'] ?? 0), $allowedIds, true) &&
            in_array(
                $user['role'] ?? null,
                User::ROLE_CONTRIBUTOR_OR_HIGHER,
                true
            );
    }

    /**
     * Localized labels keyed by stored status and reason codes.
     */
    public static function labels(): array
    {
        return [
            'previewed' => __('Ready for review'),
            'queued' => __('Queued'),
            'importing' => __('Importing'),
            'completed' => __('Completed'),
            'failed' => __('Stopped'),
            'ready' => __('Ready'),
            'skipped' => __('Skipped'),
            'imported' => __('Imported'),
            'error' => __('Error'),
            'source_missing' => __('The source sentence no longer exists.'),
            'source_language' => __('The source language does not match.'),
            'source_changed' => __('The source sentence text has changed.'),
            'source_unlicensed' => __('The source cannot be translated.'),
            'empty_translation' => __('The normalized translation is empty.'),
            'self_link' => __('A sentence cannot be linked to itself.'),
            'already_linked' => __('This translation is already linked.'),
            'duplicate_in_file' => __('This record repeats an earlier one.'),
            'save_failed' => __('The translation could not be saved and linked.'),
            'permission_revoked' => __('Import permission is no longer available.'),
        ];
    }

    private function sentences()
    {
        return FactoryLocator::get('Table')->get('Sentences');
    }

    /**
     * Snapshot source text and normalize translations without changing sentences.
     *
     * @param int $userId Import owner ID.
     * @param array $rows Parsed translation records.
     * @param array $options Source/target languages and optional context/filename.
     * @return \Cake\Datasource\EntityInterface Saved preview.
     */
    public function createPreview(
        int $userId,
        array $rows,
        array $options
    ) {
        $user = $this->Users->get($userId)->toArray();

        if (!self::canImport($user)) {
            throw new DomainException(__('Import permission is required.'));
        }

        $languages = LanguagesLib::languagesInTatoeba();

        foreach (['source_lang', 'target_lang'] as $field) {
            $value = $options[$field] ?? null;

            if (!is_string($value) ||
                !array_key_exists($value, $languages)
            ) {
                throw new InvalidArgumentException(
                    __('Please select valid source and target languages.')
                );
            }
        }

        $context = $options['context'] ?? '';

        if (!is_string($context) ||
            !mb_check_encoding($context, 'UTF-8') ||
            mb_strlen($context) > 255
        ) {
            throw new InvalidArgumentException(
                __('The project description must be at most 255 characters.')
            );
        }

        if (!$rows || count($rows) > TranslationImportCsv::MAX_ROWS) {
            throw new InvalidArgumentException(
                __('The number of translation records is invalid.')
            );
        }

        $filename = $options['filename'] ?? 'translations.csv';

        if (!is_string($filename) ||
            !mb_check_encoding($filename, 'UTF-8')
        ) {
            $filename = 'translations.csv';
        }

        return $this->getConnection()->transactional(
            function () use ($userId, $rows, $options, $context, $filename) {
                $import = $this->newEmptyEntity();
                $import->set([
                    'user_id' => $userId,
                    'source_lang' => $options['source_lang'],
                    'target_lang' => $options['target_lang'],
                    'context' => trim($context),
                    'filename' => mb_substr($filename, 0, 255),
                    'status' => 'previewed',
                ]);
                $this->saveOrFail($import);

                $seen = [];
                $items = $this->TranslationImportItems;
                $sentences = $this->sentences();

                foreach ($rows as $row) {
                    $item = $items->newEmptyEntity();
                    $item->set($row);
                    $item->translation_import_id = $import->id;

                    // Apply the same text cleaning as normal sentences.
                    $translation = $sentences->newEntity([
                        'text' => $item->translation_text,
                        'lang' => $import->target_lang,
                    ]);
                    $item->translation_text = $translation->text;

                    $source = $sentences->find()
                        ->where(['id' => $item->sentence_id])
                        ->first();

                    $message = $this->failureReason(
                        $import,
                        $item,
                        $source
                    );

                    $key = $item->sentence_id . "\0" .
                        $item->translation_text;

                    if (isset($seen[$key])) {
                        $message = 'duplicate_in_file';
                    }

                    $seen[$key] = true;
                    $item->status = 'ready';

                    if ($message !== null) {
                        $item->status = 'skipped';
                    }

                    $item->message = $message;
                    $items->saveOrFail($item);
                }

                return $import;
            }
        );
    }

    private function hasBothLinks(int $sourceId, int $targetId): bool
    {
        $links = $this->sentences()->Links;

        return $links->exists([
            'sentence_id' => $sourceId,
            'translation_id' => $targetId,
        ]) && $links->exists([
            'sentence_id' => $targetId,
            'translation_id' => $sourceId,
        ]);
    }

    /**
     * Check a record, storing the target ID if already linked.
     *
     * @return string|null Skip reason code, or null if the record is ready.
     */
    private function failureReason($import, $item, $source): ?string
    {
        if (!$source) {
            return 'source_missing';
        }

        if ($source->lang !== $import->source_lang) {
            return 'source_language';
        }

        if ($source->text !== $item->source_text) {
            return 'source_changed';
        }

        if (empty($source->license)) {
            return 'source_unlicensed';
        }

        if ($item->translation_text === '') {
            return 'empty_translation';
        }

        if ($source->lang === $import->target_lang &&
            $source->text === $item->translation_text
        ) {
            return 'self_link';
        }

        $target = $this->sentences()->find()
            ->where([
                'lang' => $import->target_lang,
                'text' => $item->translation_text,
            ])
            ->orderAsc('id')
            ->first();

        if ($target && $this->hasBothLinks(
            (int)$source->id,
            (int)$target->id
        )) {
            $item->translation_id = $target->id;
            return 'already_linked';
        }

        return null;
    }

    /**
     * Lock the import within the caller's transaction.
     *
     * @return \Cake\Datasource\EntityInterface|null
     */
    private function lockImport(int $id)
    {
        // Confirmation, discarding and workers must serialize on the same row.
        return $this->find()
            ->where(['id' => $id])
            ->epilog('FOR UPDATE')
            ->first();
    }

    private function isCurrentJob($import, int $jobId): bool
    {
        // A resumed or continued import replaces this pointer. Old jobs stop.
        return $import &&
            (int)$import->queued_job_id === $jobId &&
            in_array($import->status, ['queued', 'importing'], true);
    }

    private function hasReadyItems(int $id): bool
    {
        return $this->TranslationImportItems->exists([
            'translation_import_id' => $id,
            'status' => 'ready',
        ]);
    }

    private function newJob($import)
    {
        return $this->QueuedJobs->createJob(
            'TranslationImport',
            ['import_id' => (int)$import->id],
            ['group' => (string)$import->user_id]
        );
    }

    /**
     * Delete an unconfirmed preview and its items without changing sentences.
     *
     * @param int $id Translation import ID.
     * @param int $userId Import owner ID.
     * @return bool Whether the preview was deleted.
     */
    public function discardPreview(int $id, int $userId): bool
    {
        return $this->getConnection()->transactional(
            function () use ($id, $userId) {
                $import = $this->lockImport($id);
                $user = $this->Users->get($userId)->toArray();

                if (!$import || (int)$import->user_id !== $userId ||
                    !self::canImport($user)
                ) {
                    throw new DomainException(
                        __('You cannot discard this preview.')
                    );
                }

                if ($import->status !== 'previewed') {
                    return false;
                }

                $this->TranslationImportItems->deleteAll([
                    'translation_import_id' => $id,
                ]);
                $this->deleteOrFail($import);
                return true;
            }
        );
    }

    /**
     * Queue confirmation or resume atomically with the current job pointer.
     *
     * @param int $id Translation import ID.
     * @param int $userId Import owner ID.
     * @param bool $resume Resume remaining records instead of confirming a preview.
     * @return void
     */
    public function queueImport(
        int $id,
        int $userId,
        bool $resume = false
    ): void {
        $this->getConnection()->transactional(
            function () use ($id, $userId, $resume) {
                $import = $this->lockImport($id);
                $user = $this->Users->get($userId)->toArray();

                if (!$import ||
                    (int)$import->user_id !== $userId ||
                    !self::canImport($user)
                ) {
                    throw new DomainException(
                        __('You cannot confirm this import.')
                    );
                }

                if (!$resume && $import->status !== 'previewed') {
                    return;
                }

                if ($resume && !in_array(
                    $import->status,
                    ['queued', 'importing', 'failed'],
                    true
                )) {
                    return;
                }

                if (!$this->hasReadyItems($id)) {
                    $import->status = 'completed';
                    $this->saveOrFail($import);
                    return;
                }

                $job = $this->newJob($import);
                $import->queued_job_id = $job->id;
                $import->status = 'queued';
                $import->message = null;
                $this->saveOrFail($import);
            }
        );
    }

    /**
     * Process one ready record atomically, record save errors after rollback.
     * Records marked skipped or error still allow the batch to continue.
     *
     * @param int $id Translation import ID.
     * @param int $jobId Queue job ID currently executing.
     * @return bool True to continue the batch; false to stop.
     */
    private function processOne(int $id, int $jobId): bool
    {
        $failedItemId = null;

        $result = $this->getConnection()->transactional(
            function () use ($id, $jobId, &$failedItemId) {
                $import = $this->lockImport($id);

                if (!$this->isCurrentJob($import, $jobId)) {
                    return null;
                }

                $user = $this->Users->find()
                    ->where(['id' => $import->user_id])
                    ->first();

                if (!$user || !self::canImport($user->toArray())) {
                    $import->status = 'failed';
                    $import->message = 'permission_revoked';
                    $this->saveOrFail($import);
                    return null;
                }

                $items = $this->TranslationImportItems;
                $item = $items->find()
                    ->where([
                        'translation_import_id' => $id,
                        'status' => 'ready',
                    ])
                    ->orderAsc('record_number')
                    ->first();

                if (!$item) {
                    return null;
                }

                $failedItemId = (int)$item->id;
                CurrentUser::store($user->toArray());

                $import->status = 'importing';
                $this->saveOrFail($import);

                $sentences = $this->sentences();

                // Keep the source stable until this record commits.
                $source = $sentences->find()
                    ->where(['id' => $item->sentence_id])
                    ->epilog('FOR UPDATE')
                    ->first();

                $message = $this->failureReason($import, $item, $source);

                if ($message !== null) {
                    $item->status = 'skipped';
                    $item->message = $message;
                    $items->saveOrFail($item);
                    return true;
                }

                $translation = $sentences->saveTranslation(
                    $item->sentence_id,
                    $source->lang,
                    $item->translation_text,
                    $import->target_lang
                );

                if (!$translation || !$this->hasBothLinks(
                    (int)$item->sentence_id,
                    (int)$translation->id
                )) {
                    // Returning false rolls back this transaction.
                    return false;
                }

                $item->translation_id = $translation->id;
                $item->status = 'imported';
                $item->message = null;
                $items->saveOrFail($item);

                return true;
            }
        );

        if ($result === false) {
            // The sentence/link writes have been rolled back.
            // Record the failure in a separate transaction.
            $this->getConnection()->transactional(
                function () use ($id, $jobId, $failedItemId) {
                    $import = $this->lockImport($id);

                    if (!$this->isCurrentJob($import, $jobId)) {
                        return;
                    }

                    $items = $this->TranslationImportItems;
                    $item = $items->find()
                        ->where([
                            'id' => $failedItemId,
                            'translation_import_id' => $id,
                            'status' => 'ready',
                        ])
                        ->first();

                    if ($item) {
                        $item->status = 'error';
                        $item->message = 'save_failed';
                        $items->saveOrFail($item);
                    }
                }
            );
        }

        return $result !== null;
    }

    /**
     * Commit each record separately so retries never repeat committed records.
     * Queue a continuation if ready records remain.
     *
     * @param int $id Translation import ID.
     * @param int $jobId Queue job ID currently executing.
     * @return void
     */
    public function runBatch(int $id, int $jobId): void
    {
        $previousUser = CurrentUser::get('User');

        try {
            for ($i = 0; $i < self::BATCH_SIZE; $i++) {
                if (!$this->processOne($id, $jobId)) {
                    break;
                }
            }

            $this->getConnection()->transactional(
                function () use ($id, $jobId) {
                    $import = $this->lockImport($id);

                    if (!$this->isCurrentJob($import, $jobId)) {
                        return;
                    }

                    if ($this->hasReadyItems($id)) {
                        $job = $this->newJob($import);
                        $import->queued_job_id = $job->id;
                        $import->status = 'queued';
                    } else {
                        $import->status = 'completed';
                    }

                    $this->saveOrFail($import);
                }
            );
        } finally {
            CurrentUser::store($previousUser);
        }
    }
}
