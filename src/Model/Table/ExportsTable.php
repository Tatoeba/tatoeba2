<?php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Exception;

class ExportsTable extends Table
{
    use \App\Shell\BatchOperationTrait;

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('exports');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Queue.QueuedJobs');
        $this->belongsTo('Users');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->scalar('url')
            ->maxLength('url', 2048)
            ->allowEmpty('url');

        $validator
            ->scalar('filename')
            ->maxLength('filename', 255)
            ->allowEmpty('filename');

        $validator
            ->dateTime('generated')
            ->allowEmpty('generated');

        $validator
            ->integer('queued_job_id')
            ->allowEmpty('queued_job_id', 'create');

        $validator
            ->scalar('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['queued_job_id'], 'QueuedJobs'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        $rules->add(function ($entity) {
            $data = $entity->extract($this->schema()->columns(), true);
            $validator = $this->validator('default');
            $errors = $validator->errors($data, $entity->isNew());
            $entity->errors($errors);
            return empty($errors);
        });

        return $rules;
    }

    public function getExportsOf($userId)
    {
        return $this->find()
           ->select(['name', 'id', 'status', 'generated'])
           ->where(['user_id' => $userId]);
    }

    private function createExportFromConfig($config, $userId)
    {
        $export = $this->newEntity();
        $export->status = 'queued';
        $export->user_id = $userId;

        if (isset($config['type'])
            && $config['type'] == 'list'
            && isset($config['list_id'])) {

            $SL = TableRegistry::get('SentencesLists');
            $listId = $config['list_id'];
            try {
                $perms = $SL->getListWithPermissions($listId, $userId);
            }
            catch (Exception $e) {
                return false;
            }
            if ($perms['Permissions']['canView']) {
                $export->name = sprintf("List %d", $listId);
                $export->description = sprintf("Sentences from list %d", $listId);
                return $export;
            }
        }

        return $export;
    }

    public function createExport($userId, $config)
    {
        $export = $this->createExportFromConfig($config, $userId);
        if (!$export) {
            return false;
        }

        return $this->getConnection()->transactional(function () use ($export, $config) {
            if ($this->save($export)) {
                $config['export_id'] = $export->id;
                $job = $this->QueuedJobs->createJob(
                    'Export',
                    $config,
                    ['group' => $export->user_id]
                );
                if ($job) {
                    $export->queued_job_id = $job->id;
                    if ($this->save($export)) {
                        return $export->extract(['name', 'status']);
                    }
                }
            }
            return false;
        });
    }

    public function afterDelete($event, $entity, $options)
    {
        if ($entity->filename) {
            $file = new File($entity->filename);
            if ($file->exists()) {
                $file->delete();
            }
        }
    }

    private function removeOldExports()
    {
        $maxSize = Configure::read('Exports.maxSizeInBytes', 0);
        if ($maxSize > 0) {
            $exportPath = new Folder(Configure::read('Exports.path'));
            while ($exportPath->dirsize() > $maxSize) {
                $export = $this->find()->orderAsc('generated')->first();
                if (!$export) {
                    break;
                }
                $this->delete($export);
            }
        }
    }

    private function urlFromFilename($filename)
    {
        return Configure::read('Exports.url').basename($filename);
    }

    private function newFilename($config)
    {
        $filename = $config['type'].'_'.$config['export_id'].'.csv';
        return Configure::read('Exports.path').$filename;
    }

    public function runExport($config)
    {
        $export = $ok = false;
        try {
            $export = $this->get($config['export_id']);
            $ok = $this->_runExport($export, $config);
        }
        catch (Exception $e) {
            $ok = false;
        }

        if ($export) {
            $export->status = $ok ? 'online' : 'failed';
            $this->save($export);
        }

        $this->removeOldExports();
        return $ok;
    }

    private function _runExport($export, $config)
    {
        if ($config['type'] != 'list') {
            return false;
        }

        $filename = $this->newFilename($config);
        $export->generated = Time::now();
        $export->filename = $filename;
        if (!$this->save($export)) {
            return false;
        }

        $Sentences = TableRegistry::get('Sentences');
        $query = $Sentences->find()
            ->select(['Sentences.id', 'Sentences.lang', 'Sentences.text'])
            ->matching('SentencesLists', function ($q) use ($config) {
                return $q->where(['SentencesLists.id' => $config['list_id']]);
            });

        $file = new File($filename, true, 0600);
        if (!$file->open('w')) {
            return false;
        }

        $this->getConnection()->transactional(function () use ($query, $file) {
            $this->batchOperationNewORM($query, function ($entities) use ($file) {
                foreach ($entities as $sentence) {
                    $fields = $sentence->extract(['lang', 'text']);
                    $file->write(implode($fields, "\t")."\n");
                }
            });
        });
        $file->close();

        $export = $this->get($export->id);
        $export->url = $this->urlFromFilename($filename);
        return (bool)$this->save($export);
    }
}
