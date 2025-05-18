<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2016  Gilles Bedel

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Model\Table;

use App\Event\StatsListener;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\Validation\Validator;
use Cake\Utility\Hash;
use InvalidArgumentException;


class AudiosTable extends Table
{
    const JOB_TYPE = 'AudioImport';

    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('external', 'json');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsTo('Sentences', [
            'joinType' => 'inner',
        ]);
        $this->belongsTo('Users');
        $this->hasOne('Queue.QueuedJobs');

        $this->addBehavior('Timestamp');
        if (Configure::read('Search.enabled')) {
            $this->addBehavior('Sphinx', ['alias' => $this->getAlias()]);
        }

        $this->getEventManager()->on(new StatsListener());
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('sentence_id', 'create')
            ->numeric('sentence_id');

        $validator
            ->numeric('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->dateTime('created');

        $validator
            ->dateTime('modified');

        $validator
            ->boolean('enabled');

        return $validator;
    }

    private function isAuthorConsistent($entity) {
        $ok = true;
        $user_id = $entity->user_id;
        $external = $entity->external;
        if ($external) {
            $external = array_filter($external);
        }
        if (!($user_id xor !empty($external))) {
            $ok = false;
            if (!$user_id) {
                $err = "Both 'user_id' and 'external' fields are empty.";
            } else {
                $err = "Both 'user_id' and 'external' fields are non-empty.";
            }
            $entity->setErrors([
                'user_id' => $err,
                'external' => $err,
            ]);
        }
        return $ok;
    }

    public function beforeSave($event, $entity, $options = array()) {
        if ($entity->isNew()) {
            if ($entity->sentence_id) {
                $sentence = $this->Sentences->get($entity->sentence_id);
                $entity->sentence_lang = $sentence->lang;
            }
        }
        return $this->isAuthorConsistent($entity);
    }

    public function afterSave($event, $entity, $options = array()) {
        if ($entity->isNew()) {
            $event = new Event('Model.Audio.audioCreated', $this, [
                'audio' => $entity,
            ]);
            $this->getEventManager()->dispatch($event);
        }

        if ($entity->sentence_id) {
            $this->Sentences->flagSentenceAndTranslationsToReindex(
                $entity->sentence_id
            );
            $prev_sentence_id = $entity->getOriginal('sentence_id');
            if ($prev_sentence_id != $entity->sentence_id) {
                $this->Sentences->flagSentenceAndTranslationsToReindex(
                    $prev_sentence_id
                );
            }
        }

        if (!$entity->enabled) {
            $this->moveRecordToOtherTable($entity, $this->Sentences->DisabledAudios);
        }
    }

    protected function moveRecordToOtherTable($entity, $tableModel) {
        $entity->isNew(true);
        $this->getConnection()->transactional(function () use ($entity, $tableModel) {
            if ($tableModel->save($entity)) {
                if ($this->delete($entity)) {
                    return true;
                }
            }
            return false;
        });
    }

    protected function removeAudioFile($entity, $options) {
        if ($options['deleteAudioFile'] ?? false) {
            $file = new File($entity->file_path);
            $file->delete();
        }
    }

    public function afterDelete($event, $entity, $options) {
        $this->removeAudioFile($entity, $options);

        $event = new Event('Model.Audio.audioDeleted', $this, [
            'audio' => $entity,
        ]);
        $this->getEventManager()->dispatch($event);

        if ($entity->sentence_id) {
            $this->Sentences->flagSentenceAndTranslationsToReindex(
                $entity->sentence_id
            );
        }
    }

    public function sphinxAttributesChanged(&$attributes, &$values, &$isMVA, $entity) {
        $sentenceId = $entity->sentence_id;
        if ($sentenceId) {
            $attributes[] = 'has_audio';
            $hasAudio = $this->findBySentenceId($sentenceId)->first();
            $values[$sentenceId][] = $hasAudio ? 1 : 0;
        }
    }

    public function numberOfAudiosBy($userId) {
        return $this->find()
            ->where(['user_id' => $userId])
            ->count();
    }

    /**
     * Custom finder for optimized pagination of sentences having audio
     */
    public function findSentences(Query $query, array $options) {
        $subquery = $query
            ->applyOptions($options)
            ->distinct()
            ->select(['sentence_id' => 'sentence_id'])
            ->order(['id' => 'DESC']);

        if (isset($options['lang'])) {
            $subquery->where(['sentence_lang' => $options['lang']]);
        }

        if (isset($options['user_id'])) {
            $subquery->where(['user_id' => $options['user_id']]);
        }

        $query = $this->Sentences
            ->find()
            ->join([
                'Audios' => [
                    'table' => $subquery,
                    'type' => 'INNER',
                    'conditions' => 'Sentences.id = Audios.sentence_id'
                ],
            ])
            ->contain('Audios', function ($q) use ($options) {
                if (isset($options['user_id'])) {
                    $q->where(['Audios.user_id' => $options['user_id']]);
                }
                return $q->contain(['Users' => ['fields' => ['username']]]);
            })
            ->contain('Transcriptions')
            ->counter(function ($query) use ($subquery) {
                return $subquery->count();
            });

        return $query;
    }

    /**
     * Assign author to an audio entity.
     *
     * @param Audio   $entity                      Entity of the audio.
     * @param string  $ownerName                   Owner of the audio file.
     * @param boolean $allowedExternal (optional)  Whether metadata could be stored as JSON.
     */
    public function assignAuthor($entity, $ownerName, $allowExternal = true) {
        $result = $this->Users->findByUsername($ownerName)->first();
        if ($result) {
            $entity->user_id = $result->id;
            $entity->external = null;
        } elseif ($allowExternal && !empty($ownerName)) {
            $entity->user_id = null;
            $entity->external = array('username' => $ownerName);
        }
    }

    public function getFilesToImport() {
        $importPath = Configure::read('Recordings.importPath');
        $audioFiles = array();
        $allSentenceIds = array();

        $dh = opendir($importPath);
        if (!$dh) {
            return $audioFiles;
        }
        while (false !== ($filename = readdir($dh))) {
            $file = $importPath . DS . $filename;
            if (is_file($file)) {
                $fileInfos = array(
                    'fileName' => $filename,
                    'sourcePath' => $importPath.DS.$filename,
                    'valid'    => false,
                );
                if (preg_match('/^(\d+)(-.+)?\.mp3$/i', $filename, $matches)) {
                    $fileInfos['sentenceId'] = $allSentenceIds[] = $matches[1];
                }
                $audioFiles[] = $fileInfos;
            }
        }
        closedir($dh);

        if (empty($allSentenceIds)) {
            return [];
        }

        $sentences = $this->Sentences->find()
            ->where(['Sentences.id IN' => $allSentenceIds])
            ->select(['id', 'lang'])
            ->contain(['Audios' => ['Users' => ['fields' => ['username']]]])
            ->toList();
            
        $sentences = Hash::combine($sentences, '{n}.id', '{n}');

        foreach ($audioFiles as &$file) {
            if (isset($file['sentenceId'])) {
                $id = $file['sentenceId'];
                if (isset($sentences[$id])) {
                    $file['lang'] = $sentences[$id]['lang'];
                    $file['audios'] = $sentences[$id]->audios;
                    $file['valid'] = !is_null($sentences[$id]['lang']);
                }
            }
        }

        usort($audioFiles, function($a, $b) {
            /* Sort:
             * 1. May not be imported
             * 2. Number of existing recordings (desc)
             * 3. The rest by sentence id (asc)
             */
            $ret = 0;
            if (isset($a['valid']) || isset($b['valid'])) {
                $ret = ($a['valid'] ?? 0) - ($b['valid'] ?? 0);
            }
            if ($ret == 0 && (isset($a['audios']) || isset($b['audios']))) {
                $ret = count($b['audios'] ?? []) - count($a['audios'] ?? []);
            }
            if ($ret == 0 && (isset($a['sentenceId']) || isset($b['sentenceId']))) {
                $ret = ($a['sentenceId'] ?? 0) - ($b['sentenceId'] ?? 0);
            }
            return $ret;
        });

        return $audioFiles;
    }

    public function importFiles(&$errors, $config) {
        $errors = array();
        $filesImported = array('total' => 0, 'replaced' => 0);

        $author = $this->Users->findByUsername($config['author'])->first();
        if (!$author) {
            $errors[] = format(
                __d('admin', "Unable to import audio: user “{author}” not found."),
                ['author' => $config['author']]
            );
            return $filesImported;
        }

        $files = $this->getFilesToImport();
        foreach ($files as $file) {
            if (!$file['valid']) {
                $errors[] = format(
                    __d('admin', "Invalid file “{file}” ignored."),
                    array('file' => $file['fileName'])
                );
                continue;
            }

            $this->getConnection()->transactional(function () use ($file, $author, $config, &$errors, &$filesImported) {
                if (!empty($config['replace'][ $file['sentenceId'] ])) {
                    $existing = $this->find()
                        ->where(['sentence_id' => $file['sentenceId'], 'user_id' => $author->id])
                        ->all();
                    if ($existing->count() == 0) {
                        unset($existing);
                    }
                }

                if (isset($existing)) {
                    if ($existing->count() > 1) {
                        $errors[] = format(
                            __d('admin', "Unable to replace audio contributed by “{author}” for sentence {sentenceId}: this user has more than one audio."),
                            array('sentenceId' => $file['sentenceId'], 'author' => $author->username)
                        );
                        return false;
                    }
                    $audio = $existing->first();
                    $this->touch($audio);
                } else {
                    $audio = $this->newEntity();
                    $audio->sentence_id = $file['sentenceId'];
                    $audio->user_id = $author->id;
                }

                if (!$this->save($audio)) {
                    $errors[] = format(
                        __d('admin', "Unable to save audio for sentence {sentenceId} inside the database."),
                        array('sentenceId' => $file['sentenceId'])
                    );
                    return false;
                }

                $audioPath = $audio->file_path;
                $destDir = dirname($audioPath);
                if (!file_exists($destDir)) {
                    if (!mkdir($destDir, 0777, true)) {
                        $errors[] = format(
                            __d('admin', "Failed to create directory “{dir}” to import file “{file}”."),
                            array('dir' => $destDir, 'file' => $file['fileName'])
                        );
                        return false;
                    }
                }

                if (!copy($file['sourcePath'], $audioPath)) {
                    $errors[] = format(
                        __d('admin', "Failed to copy file “{file}” to directory “{dir}”."),
                        array('file' => $file['fileName'], 'dir' => $destDir)
                    );
                    return false;
                }

                if (!unlink($file['sourcePath'])) {
                    $errors[] = format(
                        __d('admin', "File “{file}” was successfully imported but could not be removed from the import directory."),
                        array('file' => $file['fileName'])
                    );
                }

                if (isset($existing))
                    $filesImported['replaced']++;
                if (!isset($filesImported[$file['lang']]))
                    $filesImported[$file['lang']] = 0;
                $filesImported[$file['lang']]++;
                $filesImported['total']++;
            });
        }
        
        return $filesImported;
    }

    public function edit($audio, $fields) {
        if (isset($fields['enabled'])) {
            $this->patchEntity($audio, ['enabled' => $fields['enabled']]);
        }
        if (isset($fields['author'])) {
            $this->assignAuthor($audio, $fields['author'], true);
        }
    }

    public function lastImportJob() {
        return $this->QueuedJobs->find()
            ->where(['job_type' => self::JOB_TYPE])
            ->last();
    }

    public function enqueueImportTask($author, $replace = []) {
        $job = $this->QueuedJobs->createJob(
            self::JOB_TYPE,
            compact('author', 'replace')
        );
        $this->QueuedJobs->wakeUpWorkers();
        return $job;
    }
}
