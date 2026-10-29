<?php
namespace App\Controller;

use App\Model\CurrentUser;
use App\Model\Table\TranslationImportsTable;
use App\Model\TranslationImportCsv;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Psr\Http\Message\UploadedFileInterface;

class TranslationImportsController extends AppController
{
    protected $defaultTable = 'TranslationImports';

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $user = (array)CurrentUser::get('User');

        // Authentication redirects guests during startup, after beforeFilter.
        if ($this->Authentication->getIdentity() &&
            !TranslationImportsTable::canImport($user)
        ) {
            throw new ForbiddenException();
        }

        $this->set('isResponsive', true);
    }

    private function _own_import(int $id)
    {
        try {
            $import = $this->TranslationImports->get($id);
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException();
        }

        if ((int)$import->user_id !== (int)CurrentUser::get('id')) {
            throw new ForbiddenException();
        }

        return $import;
    }

    public function index()
    {
        $query = $this->TranslationImports->find()
            ->where(['user_id' => CurrentUser::get('id')])
            ->orderDesc('created')
            ->orderDesc('id');

        $imports = $this->paginate($query);

        $this->set(compact('imports'));
    }

    private function _uploaded_rows(): array
    {
        $file = $this->request->getData('file');

        if (!$file instanceof UploadedFileInterface ||
            $file->getError() !== UPLOAD_ERR_OK
        ) {
            throw new \InvalidArgumentException(
                __('Please upload a valid translation file.')
            );
        }

        $size = $file->getSize();

        if ($size === null || $size < 1 ||
            $size > TranslationImportCsv::MAX_BYTES
        ) {
            throw new \InvalidArgumentException(
                __('The file must contain data and be at most 1 MiB.')
            );
        }

        $stream = $file->getStream();
        $stream->rewind();

        return TranslationImportCsv::parse(
            $stream->getContents(),
            $this->request->getData('delimiter')
        );
    }

    public function create()
    {
        $this->request->allowMethod(['post']);

        try {
            $rows = $this->_uploaded_rows();
            $file = $this->request->getData('file');

            $options = [
                'source_lang' => $this->request->getData('source_lang'),
                'target_lang' => $this->request->getData('target_lang'),
                'context' => $this->request->getData('context', ''),
                'filename' => $file->getClientFilename(),
            ];

            $import = $this->TranslationImports->createPreview(
                (int)CurrentUser::get('id'),
                $rows,
                $options
            );

            return $this->redirect(['action' => 'view', $import->id]);
        } catch (\InvalidArgumentException | \DomainException $e) {
            $this->Flash->error($e->getMessage());

            return $this->redirect(['action' => 'index']);
        }
    }

    public function view($id = null)
    {
        $import = $this->_own_import((int)$id);
        $table = $this->TranslationImports->TranslationImportItems;

        $query = $table->find()
            ->where(['translation_import_id' => $import->id])
            ->orderAsc('record_number');

        $items = $this->paginate($query, ['limit' => 50]);

        $query = $table->find();
        $totals = $query
            ->select(['status', 'count' => $query->func()->count('*')])
            ->where(['translation_import_id' => $import->id])
            ->group('status')
            ->all();

        $counts = ['ready' => 0, 'imported' => 0, 'skipped' => 0, 'error' => 0];

        foreach ($totals as $total) {
            $counts[$total->status] = (int)$total->count;
        }

        $readyCount = $counts['ready'];

        $this->set(compact('import', 'items', 'readyCount', 'counts'));
    }

    public function confirm($id = null)
    {
        $this->request->allowMethod(['post']);
        $import = $this->_own_import((int)$id);

        if ($this->request->getData('confirmed') !== '1') {
            $this->Flash->error(
                __('Please confirm that you reviewed your translations.')
            );

            return $this->redirect(['action' => 'view', $import->id]);
        }

        try {
            $this->TranslationImports->queueImport(
                (int)$import->id,
                (int)CurrentUser::get('id')
            );
        } catch (\DomainException $e) {
            throw new ForbiddenException($e->getMessage());
        }

        return $this->redirect(['action' => 'view', $import->id]);
    }

    public function resume($id = null)
    {
        $this->request->allowMethod(['post']);
        $import = $this->_own_import((int)$id);

        try {
            $this->TranslationImports->queueImport(
                (int)$import->id,
                (int)CurrentUser::get('id'),
                true
            );
        } catch (\DomainException $e) {
            throw new ForbiddenException($e->getMessage());
        }

        return $this->redirect(['action' => 'view', $import->id]);
    }

    public function discard($id = null)
    {
        $this->request->allowMethod(['post']);
        $import = $this->_own_import((int)$id);

        try {
            $discarded = $this->TranslationImports->discardPreview(
                (int)$import->id,
                (int)CurrentUser::get('id')
            );
        } catch (\DomainException $e) {
            throw new ForbiddenException($e->getMessage());
        }

        if (!$discarded) {
            $this->Flash->error(
                __('Only an unconfirmed preview can be discarded.')
            );

            return $this->redirect(['action' => 'view', $import->id]);
        }

        $this->Flash->success(__('Preview discarded. No sentences were imported.'));

        return $this->redirect(['action' => 'index']);
    }

    public function report($id = null)
    {
        $this->request->allowMethod(['get']);
        $import = $this->_own_import((int)$id);

        $items = $this->TranslationImports->TranslationImportItems
            ->find()
            ->where(['translation_import_id' => $import->id])
            ->orderAsc('record_number')
            ->all()
            ->toArray();

        $json = json_encode([
            'import' => $import,
            'items' => $items,
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        return $this->response
            ->withType('application/json')
            ->withDownload('translation-import-' . $import->id . '.json')
            ->withStringBody($json);
    }
}
