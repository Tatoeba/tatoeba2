<?php
namespace App\Command;

use App\Command\EditCommand_;
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class EditLanguagesCommand extends EditCommand_
{
    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser = parent::buildOptionParser($parser);
        $parser
            ->setDescription('Change sentence languages.')
            ->addArgument('language', [
                'help' => 'ISO code of the new language.',
                'required' => true,
            ]);
        return $parser;
    }

    protected function editLanguage($ids, $lang) {
        foreach ($ids as $id) {
            $result = $this->Sentences->editSentence(compact('id', 'lang'));
            if ($result) {
                $this->log[] = "id $id - Language set to {$result->lang}";
            } else {
                $this->log[] = "id $id - Record not found or could not save changes";
            }
            $this->total++;
        }
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $this->becomeUser($args, $io);
        if (!CurrentUser::isModerator()) {
            $io->error('User must be corpus maintainer or admin!');
            $this->abort();
        }

        $newLanguage = $args->getArgument('language');
        if (!LanguagesLib::languageExists($newLanguage)) {
            $io->error("Language '$newLanguage' does not exist!");
            $this->abort();
        }

        $ids = $this->readIdsFromFile($args, $io);

        $this->editLanguage($ids, $newLanguage);

        $this->printLog($io);
    }
}
