<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2015  Gilles Bedel

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

class Recordings extends AppModel
{
    public $useTable = false;

    public $belongsTo = array(
        'Sentence',
    );

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
            'fields' => array('id', 'lang', 'hasaudio'),
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
                    __d('admin', "Invalid file “{file}” ignored.", true),
                    array('file' => $file['fileName'])
                );
                continue;
            }

            $destDir = $recsBaseDir . $file['lang'];
            if (!file_exists($destDir)) {
                if (!mkdir($destDir)) {
                    $errors[] = format(
                        __d('admin', "Failed to create directory “{dir}” to import file “{file}”.", true),
                        array('dir' => $destDir, 'file' => $file['fileName'])
                    );
                    continue;
                }
            }

            $destFile = $destDir . DS . strtolower($file['fileName']);
            if (!copy($file['sourcePath'], $destFile)) {
                $errors[] = format(
                    __d('admin', "Failed to copy file “{file}” to directory “{dir}”.", true),
                    array('file' => $file['fileName'], 'dir' => $destDir)
                );
                continue;
            }

            $ok = $this->Sentence->Audio->assignAudioTo($file['sentenceId'], $author);
            if (!$ok) {
                $errors[] = format(
                    __d('admin', "Unable to assign audio to “{author}” for sentence {sentenceId} inside the database.", true),
                    array('sentenceId' => $file['sentenceId'], 'author' => $author)
                );
                unlink($destFile); // cleaning up, no need to warn on error
                continue;
            }

            if (!unlink($file['sourcePath'])) {
                $errors[] = format(
                    __d('admin', "File “{file}” was successfully imported but could not be removed from the import directory.", true),
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
}
?>
