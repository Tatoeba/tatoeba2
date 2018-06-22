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

class Audio extends AppModel
{
    public $validate = array(
        'sentence_id' => array(
            'validateType' => array(
                'rule' => 'numeric',
                'required' => true,
                'on' => 'create',
            ),
        ),
        'user_id' => array(
            'rule' => 'numeric',
            'allowEmpty' => true,
        ),
        'created' => array(
            'rule' => 'notBlank',
        ),
        'modified' => array(
            'rule' => 'notBlank',
        ),
    );

    public $actsAs = array('Containable');

    public $belongsTo = array(
        'Sentence' => array('type' => 'inner'),
        'User',
    );

    public $defaultExternal = array(
        'username' => null,
        'license' => null,
        'attribution_url' => null,
    );

    /**
     * The constructor is here only to conditionally attach Sphinx.
     *
     * @return void
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);

        if (Configure::read('Search.enabled')) {
            $this->Behaviors->attach('Sphinx');
        }
    }

    public function afterFind($results, $primary = false) {
        foreach ($results as &$result) {
            if (isset($result[$this->alias])
                && array_key_exists('external', $result[$this->alias])) {
                $result[$this->alias]['external'] = (array)json_decode(
                    $result[$this->alias]['external']
                );
                $result[$this->alias]['external'] = array_merge(
                    $this->defaultExternal,
                    $result[$this->alias]['external']
                );
            }
        }
        return $results;
    }

    private function encodeExternal() {
        if (isset($this->data[$this->alias]['external'])
            && is_array($this->data[$this->alias]['external'])) {
            $external = $this->field('external', array('id' => $this->id));
            if ($external === false) {
                $external = array();
            }
            $external = array_merge($external, $this->data[$this->alias]['external']);
            $external = array_intersect_key($external, $this->defaultExternal);
            $this->data[$this->alias]['external'] = json_encode($external);
        }
    }

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['id']) &&
            isset($this->data[$this->alias]['sentence_id'])) {
            // save the previous sentence_id before updating it
            $result = $this->findById($this->data[$this->alias]['id'], 'sentence_id');
            if (isset($result[$this->alias]['sentence_id'])) {
                $this->data['PrevSentenceId'] = $result[$this->alias]['sentence_id'];
            }
        }

        $ok = true;
        $user_id = $this->_getFieldFromDataOrDatabase('user_id');
        $external = $this->_getFieldFromDataOrDatabase('external');
        $external = array_filter($external);
        if (!($user_id xor !empty($external))) {
            $ok = false;
        }

        $this->encodeExternal();
        return $ok;
    }

    public function afterSave($created, $options = array()) {
        if (isset($this->data[$this->alias]['sentence_id'])) {
            $this->Sentence->flagSentenceAndTranslationsToReindex(
                $this->data[$this->alias]['sentence_id']
            );
            if (isset($this->data['PrevSentenceId']) &&
                $this->data['PrevSentenceId'] != $this->data[$this->alias]['sentence_id']) {
                $this->Sentence->flagSentenceAndTranslationsToReindex(
                    $this->data['PrevSentenceId']
                );
                unset($this->data['PrevSentenceId']);
            }
        }
    }

    public function afterDelete() {
        if (isset($this->data[$this->alias]['sentence_id'])) {
            $this->Sentence->flagSentenceAndTranslationsToReindex(
                $this->data[$this->alias]['sentence_id']
            );
        }
    }

    public function sphinxAttributesChanged(&$attributes, &$values, &$isMVA) {
        if (array_key_exists('sentence_id', $this->data[$this->alias])) {
            $attributes[] = 'has_audio';
            $sentenceId = $this->data[$this->alias]['sentence_id'];
            $hasAudio = (bool)$this->findBySentenceId($sentenceId, 'sentence_id');
            $values[$sentenceId][] = intval($hasAudio);
        }
    }

    public function numberOfAudiosBy($userId) {
        return $this->find('count', array(
            'conditions' => array('user_id' => $userId),
        ));
    }

    public function assignAudioTo($sentenceId, $ownerName, $allowExternal = true) {
        $data = array(
            'sentence_id' => $sentenceId,
            'user_id' => null,
            'external' => null,
        );

        $result = $this->User->findByUsername($ownerName);
        if ($result) {
            $data['user_id'] = $result[$this->User->alias]['id'];
        } elseif ($allowExternal && !empty($ownerName)) {
            $data['external'] = array('username' => $ownerName);
        }

        $result = $this->findBySentenceId($sentenceId, 'id');
        if ($result) { // reassign audio
            $data['id'] = $result[$this->alias]['id'];
        } else {
            $this->create();
        }

        return $this->save($data);
    }

    public function getFilesToImport() {
        $importPath = Configure::read('Recordings.importPath');
        $audioFiles = array();
        $allSentenceIds = array();

        $dh = opendir($importPath);
        while (false !== ($filename = readdir($dh))) {
            $file = $importPath.$filename;
            if (is_file($file)) {
                $fileInfos = array(
                    'fileName' => $filename,
                    'sourcePath' => $importPath.$filename,
                    'valid'    => false,
                );
                if (preg_match('/(\d+)\.mp3$/i', $file, $matches)) {
                    $fileInfos['sentenceId'] = $allSentenceIds[] = $matches[1];
                }
                $audioFiles[] = $fileInfos;
            }
        }
        closedir($dh);

        $sentences = $this->Sentence->find('all', array(
            'conditions' => array('Sentence.id' => $allSentenceIds),
            'fields' => array('id', 'lang'),
            'contain' => array('Audio'),
        ));
        $sentences = Set::combine($sentences, '{n}.Sentence.id', '{n}');

        foreach ($audioFiles as &$file) {
            if (isset($file['sentenceId'])) {
                $id = $file['sentenceId'];
                if (isset($sentences[$id])) {
                    $file['lang'] = $sentences[$id]['Sentence']['lang'];
                    $file['hasaudio'] = count($sentences[$id]['Audio']) > 0;
                    $file['valid'] = !is_null($sentences[$id]['Sentence']['lang']);
                }
            }
        }

        usort($audioFiles, function($a, $b) {
            /* Sort:
             * 1. May not be imported
             * 2. Already has audio
             * 3. The rest by sentence id
             */
            if (isset($a['valid']) && isset($b['valid'])
                && $a['valid'] != $b['valid']) {
                return $a['valid'] ? 1 : -1;
            } elseif (isset($a['hasaudio']) && isset($b['hasaudio'])
                      && $a['hasaudio'] != $b['hasaudio']) {
                return $a['hasaudio'] ? -1 : 1;
            } elseif (isset($a['sentenceId']) && isset($b['sentenceId'])) {
                return $a['sentenceId'] - $b['sentenceId'];
            } else {
                return 0;
            }
        });

        return $audioFiles;
    }

    public function importFiles(&$errors, $author) {
        $recsBaseDir = Configure::read('Recordings.path');
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

            $destDir = $recsBaseDir . $file['lang'];
            if (!file_exists($destDir)) {
                if (!mkdir($destDir)) {
                    $errors[] = format(
                        __d('admin', "Failed to create directory “{dir}” to import file “{file}”."),
                        array('dir' => $destDir, 'file' => $file['fileName'])
                    );
                    continue;
                }
            }

            $destFile = $destDir . DS . strtolower($file['fileName']);
            if (!copy($file['sourcePath'], $destFile)) {
                $errors[] = format(
                    __d('admin', "Failed to copy file “{file}” to directory “{dir}”."),
                    array('file' => $file['fileName'], 'dir' => $destDir)
                );
                continue;
            }

            $ok = $this->assignAudioTo($file['sentenceId'], $author, false);
            if (!$ok) {
                $errors[] = format(
                    __d('admin', "Unable to assign audio to “{author}” for sentence {sentenceId} inside the database. Make sure it's a valid username."),
                    array('sentenceId' => $file['sentenceId'], 'author' => $author)
                );
                unlink($destFile); // cleaning up, no need to warn on error
                continue;
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
        }
        
        return $filesImported;
    }

    /**
     * Update audio count.
     *
     * @return void
     */
    public function updateCount()
    {
        $query = "
            UPDATE `languages` l,
                (SELECT count(distinct sentence_id) as count, lang
                FROM audios JOIN sentences ON audios.sentence_id = sentences.id
                GROUP BY lang
                ) as s
            SET audio = s.count
            WHERE l.code = s.lang;
        ";
        $this->query($query);
    }
}
