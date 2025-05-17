<?php
namespace App\Model\Table;

use App\Model\ExportRateThrottler;
use App\Model\Exporter\ListExporter;
use App\Model\Exporter\PairsExporter;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Exception;

class ExportsTable extends Table
{
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

    private function createExportFromConfig(&$config, $userId)
    {
        $export = $this->newEntity();
        $export->status = 'queued';
        $export->user_id = $userId;

        if (isset($config['format']) && $config['format'] == 'shtooka') {
            $config['fields'] = ['id', 'text'];
        }

        $exporter = $this->newExporter($config, $userId);

        if ($exporter && $exporter->validates()) {
            $export->name = $exporter->getExportName();
            $export->description = $exporter->getExportDescription();
            return $export;
        }

        return false;
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
                    $this->QueuedJobs->wakeUpWorkers();
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

    private function newExporter($config, $userId)
    {
        if (isset($config['type'])) {
            switch ($config['type']) {
                case 'list' : return new ListExporter($config, $userId);
                case 'pairs': return new PairsExporter($config, $userId);
            }
        }
        return false;
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
        $extMap = [ 'shtooka' => 'txt' ];
        $ext = $extMap[ $config['format'] ] ?? $config['format'];
        $filename = $config['type'].'_'.$config['export_id'].'.'.$ext;
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
        $exporter = $this->newExporter($config, $export->user_id);
        $query = $exporter && $exporter->validates() ? $exporter->getQuery() : false;
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

        $throttler = new ExportRateThrottler();
        $throttler->start();
        foreach ($query as $fields) {
            $linefeed = "\r\n";
            if ($config['format'] == 'shtooka') {
                $file->write(implode(" - ", $fields).$linefeed);
            } else {
                $file->write(implode("\t", $fields).$linefeed);
            }
            $throttler->oneMoreRecord();
            $throttler->control();
        }
        $file->close();

        $export = $this->get($export->id);
        $export->url = $this->urlFromFilename($filename);
        return (bool)$this->save($export);
    }
}
