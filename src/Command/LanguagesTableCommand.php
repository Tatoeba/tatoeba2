<?php
declare(strict_types=1);

/**
 *  Tatoeba Project, free collaborative creation of languages corpuses project
 *  Copyright (C) 2018  Gilles Bedel
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Command;

use App\Lib\LanguagesLib;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\I18n;

class LanguagesTableCommand extends Command
{
    private $Languages;

    public function initialize(): void
    {
        parent::initialize();
        $this->Languages = $this->fetchTable('Languages');
    }

    private function get_tatoeba_languages() {
        I18n::setLocale('en');
        return LanguagesLib::languagesInTatoeba();
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser
            ->setDescription('Reset or update the `languages` table with values.')
            ->addArgument('operation', [
                'required' => true,
                'choices' => ['reset', 'update'],
            ]);

        return $parser;
    }

    private function removeStats() {
        $this->Languages->deleteAll('1=1');
    }

    private function insertLanguages() {
        $codes = array_keys($this->get_tatoeba_languages());
        $data = array_map(
            function ($code) {
                return array('code' => $code);
            },
            $codes
        );
        $data[] = [ 'code' => null ];
        $entities = $this->Languages->newEntities($data);
        $this->Languages->saveMany($entities);
    }

    private function insertStats() {
        $updateScript = ROOT . '/docs/database/scripts/update_languages_stats.sql';
        $script = file_get_contents($updateScript);
        $conn = ConnectionManager::get('default');
        $conn->execute($script);
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $op = $args->getArgument('operation');
        switch ($op) {
            case 'reset':
                $this->removeStats();
                $this->insertLanguages();
            case 'update':
                $this->insertStats();
                break;
        }
    }
}
