<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;

class MigrateAudioFilesCommand extends Command
{
    private $log = [];
    private $total = 0;
    private $migrated = 0;
    private $to_migrate = 0;

    private function migrateFiles($mode)
    {
        $this->loadModel('Audios');
        $audios = $this->Audios->find()
            ->contain(['Sentences' => ['fields' => ['lang']]])
            ->disableBufferedResults()
            ->all();

        foreach ($audios as $audio) {
            $this->total++;
            $oldPath = Configure::read('Recordings.path').DS.$audio->sentence->lang.DS.$audio->sentence_id.'.mp3';
            $newPath = $audio->file_path;
            if (file_exists($newPath)) { // Already migrated
                continue;
            }
            $this->to_migrate++;
            if (!file_exists($oldPath)) {
                $this->log[] = sprintf("Skipped sentence %d: missing audio file: %s", $audio->sentence_id, $oldPath);
                continue;
            }
            $destDir = dirname($newPath);
            if (!file_exists($destDir)) {
                if (!mkdir($destDir, 0777, true)) {
                    $this->log[] = sprintf("Skipped sentence %d: could not create directory: %s", $audio->sentence_id, $destDir);
                    continue;
                }
                if (!chown($destDir, 'www-data')) {
                    $this->log[] = sprintf("Sentence %d: could not set owner as 'www-data' for directory: %s", $audio->sentence_id, $destDir);
                    continue;
                }
                if (!chgrp($destDir, 'www-data')) {
                    $this->log[] = sprintf("Sentence %d: could not set group as 'www-data' for directory: %s", $audio->sentence_id, $destDir);
                    continue;
                }
            }
            if ($mode == 'link') {
                if (!link($oldPath, $newPath)) {
                    $this->log[] = sprintf("Skipped sentence %d: could not link new audio file: %s", $audio->sentence_id, $newPath);
                    continue;
                }
            } else {
                if (!copy($oldPath, $newPath)) {
                    $this->log[] = sprintf("Skipped sentence %d: could not copy new audio file: %s", $audio->sentence_id, $newPath);
                    continue;
                }
            }
            if (!chown($newPath, 'www-data')) {
                $this->log[] = sprintf("Sentence %d was migrated but could not set owner as 'www-data' for file: %s", $audio->sentence_id, $newPath);
                continue;
            }
            if (!chgrp($newPath, 'www-data')) {
                $this->log[] = sprintf("Sentence %d was migrated but could not set group as 'www-data' for file: %s", $audio->sentence_id, $newPath);
                continue;
            }
            $this->migrated++;
        }
    }

    public function printLog(ConsoleIo $io) {
        if ($this->total == 0) {
            $io->out('No audio files detected.');
        } elseif ($this->to_migrate == 0) {
            $io->out(sprintf('There were nothing to do. It looks like all the %d audio files have already been migrated.', $this->total));
        } elseif ($this->to_migrate == $this->migrated) {
            $io->out(sprintf('All %d old audio files were successfully migrated.', $this->migrated));
        } else {
            $io->out(sprintf('Only %d of %d audio files were successfully migrated:', $this->migrated, $this->to_migrate));
            foreach ($this->log as $line) {
                $io->out(" - $line");
            }
        }
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $mode = 'link';
        if ($args->getArgumentAt(0) == 'copy') {
            $mode = $args->getArgumentAt(0);
        }
        $this->migrateFiles($mode);
        $this->printLog($io);
    }
}
