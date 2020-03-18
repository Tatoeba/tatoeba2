<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;
use \Datetime;

class FillContributionsStatsCommand extends Command
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Contributions');
        $this->loadModel('ContributionsStats');
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        // Initialize
        $firstDay = new DateTime('2007-09-30');
        $today = new DateTime('now');

        for ($day = $firstDay; $day <= $today; $day->modify('+1 day')) {
            $date = $day->format('Y-m-d');
            $stats[$date] = [
                ['sentence', 'insert', 0],
                ['sentence', 'delete', 0],
                ['link', 'insert', 0],
                ['link', 'delete', 0]
            ];

            $inserted[$date] = [];
        }

        // Fetch
        $contributions = $this->Contributions->find()
                              ->where(["type !=" => "license", "action !=" => "update"])
                              ->order(["datetime" => "ASC"]);
        $contributions->disableBufferedResults();
        foreach ($contributions as $contribution) {
            if(!is_null($contribution->datetime)) {
                $date = $contribution->datetime->format('Y-m-d');
                if ($contribution->type === 'sentence' && $contribution->action === 'insert') {
                    $stats[$date][0][2] += 1;
                    array_push($inserted[$date], $contribution->sentence_id);
                } elseif ($contribution->type === 'sentence' && $contribution->action === 'delete') {
                    if (in_array($contribution->sentence_id, $inserted[$date])) {
                        $stats[$date][0][2] -= 1;
                    } else {
                        $stats[$date][1][2] += 1;
                    }
                } elseif ($contribution->type === 'link' && $contribution->action === 'insert') {
                        $stats[$date][2][2] += 1;
                } elseif ($contribution->type === 'link' && $contribution->action === 'delete') {
                        $stats[$date][3][2] += 1;
                }
            }
        }

        // Truncate table and Fill
        $contributionsStats = TableRegistry::getTableLocator()->get('ContributionsStats');
        $contributionsStats->deleteAll([]);
        foreach ($stats as $date => $dailyStat) {
            foreach ($dailyStat as $unitRecord) {
                if ($unitRecord[2] != 0) {
                    $record = $contributionsStats->newEntity([
                        'date' => $date,
                        'type' => $unitRecord[0],
                        'action' => $unitRecord[1],
                        'sentences' => $unitRecord[2]
                    ]);
                    $contributionsStats->save($record);
                }
            }
        }
    }
}
