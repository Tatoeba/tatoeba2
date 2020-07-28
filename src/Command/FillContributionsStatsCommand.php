<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;
use \Datetime;
use \Exception;

class FillContributionsStatsCommand extends Command
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Contributions');
        $this->loadModel('ContributionsStats');
    }

    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser
            ->setDescription('Rewrite records of the contributions_stats table ' .
                             'corresponding to contributions between the two given dates.')
            ->addOption('from', [
                'help' => 'The date from which stats need to be rewritten.',
                'default' => '2007-09-30',
                'short' => 'f'
            ])
            ->addOption('to', [
                'help' => 'The date until which stats need to be rewritten.',
                'default' => (new DateTime('now'))->format('Y-m-d'),
                'short' => 't'
            ]);
        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $from = $args->getOption('from');
        $to = $args->getOption('to');
        // Initialize
        try {
            $firstDay = new DateTime($from);
            $lastDay = new DateTime($to);
            $lastDay->modify('+1 day');
        } catch (Exception $e) {
            $io->error($e->getMessage());
            $this->abort();
        }

        for ($day = new DateTime($from); $day < $lastDay; $day->modify('+1 day')) {
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
                              ->where(["type !=" => "license", "action !=" => "update",
                                       "datetime >=" => $firstDay, "datetime <=" => $lastDay])
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
        $contributionsStats->deleteAll(['date >=' => $from, 'date <' => $lastDay->format('Y-m-d')]);
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
