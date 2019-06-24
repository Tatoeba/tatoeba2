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
           ->where(['user_id' => $userId]);
    }

    private function createExportFromConfig($config, $userId)
    {
        $export = $this->newEntity();
        $export->status = 'queued';
        $export->user_id = $userId;

        if (isset($config['type'])
            && $config['type'] == 'list'
            && isset($config['list_id'])
            && isset($config['fields'])
            && is_array($config['fields'])
            && $this->getSqlFields($config['fields'])) {

            $SL = TableRegistry::get('SentencesLists');
            $listId = $config['list_id'];
            try {
                $list = $SL->getListWithPermissions($listId, $userId);
            }
            catch (Exception $e) {
                return false;
            }
            if ($list['Permissions']['canView']) {
                $export->name = format(__('List {listName}'), ['listName' => $list->name]);
                $export->description = __("Sentence id [tab] Sentence text");
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
                try {
                    $job = $this->QueuedJobs->createJob(
                        'Export',
                        $config,
                        ['group' => $export->user_id]
                    );
                    $export->queued_job_id = $job->id;
                    if ($this->save($export)) {
                        return $export->extract(['id', 'name', 'status']);
                    }
                } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
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

    private function getSqlFields($configFields)
    {
        $availableFields = [
            'id' => 'SentencesSentencesLists.sentence_id',
            'lang' => 'Sentences.lang',
            'text' => 'Sentences.text',
        ];
        $sqlFields = [];
        foreach ($configFields as $field) {
            if (isset($availableFields[$field])) {
                $sqlFields[$field] = $availableFields[$field];
            } else {
                return false;
            }
        }
        return $sqlFields;
    }

    private function buildQueryFromConfig($config)
    {
        if ($config['type'] != 'list') {
            return false;
        }

        $sqlFields = $this->getSqlFields($config['fields']);
        if (!$sqlFields) {
            return false;
        }

        $SSL = TableRegistry::get('SentencesSentencesLists');
        $query = $SSL->find()
            ->select($sqlFields)
            ->where(['SentencesSentencesLists.sentences_list_id' => $config['list_id']])
            ->contain('Sentences', function ($q) {
                return $q->select(['Sentences.lang', 'Sentences.text']);
            })
            ->order([$sqlFields['id']]);
        return $query;
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

    private function newUniqueFilename($config)
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
        $query = $this->buildQueryFromConfig($config);
        if (!$query) {
            return false;
        }

        $filename = $this->newUniqueFilename($config);
        $export->generated = Time::now();
        $export->filename = $filename;
        if (!$this->save($export)) {
            return false;
        }

        $file = new File($filename, true, 0600);
        if (!$file->open('w')) {
            return false;
        }

        $BOM = "\xEF\xBB\xBF";
        $file->write($BOM);

        $this->getConnection()->transactional(function () use ($query, $file, $config) {
            $this->batchOperationNewORM($query, function ($entities) use ($file, $config) {
                foreach ($entities as $ssl) {
                    $fields = array_map(
                        function ($field) use ($ssl, $config) {
                            return $ssl->{$field};
                        },
                        $config['fields']
                    );
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
