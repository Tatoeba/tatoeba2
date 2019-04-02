<?php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

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
           ->select(['name', 'url', 'status'])
           ->where(['user_id' => $userId]);
    }

    public function createExport($userId, $config)
    {
        $export = $this->newEntity();
        $export->name        = $config['name'];
        $export->description = $config['description'];
        $export->status      = 'queued';
        $export->user_id     = $userId;

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
                        return $export->extract(['name', 'url', 'status']);
                    }
                }
            }
            return false;
        });
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

    public function runExport($config, $jobId)
    {
        if ($config['type'] == 'list') {
            // TODO get exception
            $export = $this->get($config['export_id']);
            $filename = $this->newFilename($config);
            $export->generated = Time::now();
            $export->filename = $filename;
            $this->save($export);

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

            $this->batchOperationNewORM($query, function ($entities) use ($file) {
                foreach ($entities as $sentence) {
                    $fields = $sentence->extract(['lang', 'text']);
                    $file->write(implode($fields, "\t")."\n");
                }
            });
            $file->close();

            $export = $this->get($export->id);
            $export->url = $this->urlFromFilename($filename);
            $export->status = 'online';
            return (bool)$this->save($export);
        }
    }
}
