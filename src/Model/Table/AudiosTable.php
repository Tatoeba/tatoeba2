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
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Validation\Validator;
use Cake\Utility\Hash;
use InvalidArgumentException;


class AudiosTable extends Table
{
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
        }

        return $ok;
    }

    public function beforeSave($event, $entity, $options = array()) {
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
    }

    public function afterDelete($event, $entity, $options) {
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
                if (preg_match('/^(\d+)(-\d+)?\.mp3$/i', $filename, $matches)) {
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
            ->contain(['Audios'])
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
            if (isset($a['valid']) && isset($b['valid'])
                && $a['valid'] != $b['valid']) {
                return $a['valid'] ? 1 : -1;
            } elseif (isset($a['audios']) && isset($b['audios'])) {
                return count($b['audios']) - count($a['audios']);
            } elseif (isset($a['sentenceId']) && isset($b['sentenceId'])) {
                return $a['sentenceId'] - $b['sentenceId'];
            } else {
                return 0;
            }
        });

        return $audioFiles;
    }

    public function importFiles(&$errors, $author) {
        $errors = array();
        $filesImported = array('total' => 0);

        $files = $this->getFilesToImport();
        foreach ($files as $file) {
            if (!$file['valid']) {
                $errors[] = format(
                    __d('admin', "Invalid file “{file}” ignored."),
                    array('file' => $file['fileName'])
                );
                continue;
            }

            $this->getConnection()->transactional(function () use ($file, $author, &$errors, &$filesImported) {
                $audio = $this->newEntity();
                $audio->sentence_id = $file['sentenceId'];
                $this->assignAuthor($audio, $author, false);

                if (!$this->save($audio)) {
                    $errors[] = format(
                        __d('admin', "Unable to assign audio to “{author}” for sentence {sentenceId} inside the database. Make sure it's a valid username."),
                        array('sentenceId' => $file['sentenceId'], 'author' => $author)
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

                if (!isset($filesImported[$file['lang']]))
                    $filesImported[$file['lang']] = 0;
                $filesImported[$file['lang']]++;
                $filesImported['total']++;
            });
        }
        
        return $filesImported;
    }

    public function massEdit($audioChangeReqs) {
        return $this->getConnection()->transactional(function () use ($audioChangeReqs) {
            foreach ($audioChangeReqs as $id => $audioChangeReq) {
                try {
                    $audio = $this->get($id);
                } catch (RecordNotFoundException $e) {
                    return false;
                }
                if (isset($audioChangeReq['enabled'])) {
                    $audio->enabled = $audioChangeReq['enabled'];
                }
                if (isset($audioChangeReq['author'])) {
                    $this->assignAuthor($audio, $audioChangeReq['author'], true);
                }
                try {
                    if (!$this->save($audio)) {
                        return false;
                    }
                } catch (InvalidArgumentException $e) {
                    return false;
                }
            }
            return true;
        });
    }
}
