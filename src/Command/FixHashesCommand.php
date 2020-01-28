<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Command;
use Cake\Datasource\ConnectionManager;
use App\Shell\BatchOperationTrait;

class FixHashesCommand extends Command
{
    use BatchOperationTrait;

    protected $log;

    protected function buildOptionParser(ConsoleOptionParser $parser) {
        $parser
            ->setDescription('Fix wrong hashes in table.')
            ->addArgument('table', [
                'help' => 'Table to fix',
                'required' => true
            ])
            ->addOption('hash-column', [
                'help' => 'Name of the hash column.',
                'short' => 'c',
                'default' => 'hash'
            ])
            ->addOption('source-columns', [
                'help' => 'Comma separated list of columns the hash' .
                          ' is based on.',
                'short' => 's',
                'default' => 'lang,text'
            ])
            ->addOption('dry-run', [
                'help' => 'Do everything except saving any changes.',
                'short' => 'n',
                'boolean' => true
            ])
            ->addOption('batch-size', [
                'help' => 'Number of rows to proceed in one batch.',
                'short' => 'b',
                'default' => '1000'
            ])
            ->addOption('raw', [
                'help' => 'Output changed rows in a machine readable format' .
                          ' (id <tab> old hash <tab> new hash)',
                'short' => 'r',
                'boolean' => true
            ])
            ->addOption('progress-bar', [
                'help' => 'Show a progress bar.',
                'short' => 'p',
                'boolean' => true
            ]);
        return $parser;
    }

    protected function getTable($name) {
        $dbTables = ConnectionManager::get('default')->getSchemaCollection()->listTables();
        $table = $this->loadModel($name);
        $tableName = $table->getTable();
        return in_array($tableName, $dbTables) ? $table : false;
    }

    protected function fixHash($entities, $options) {
        extract($options);
        foreach ($entities->extract('id') as $id) {
            $table->getConnection()->transactional(
                function ($conn) use ($id, $table, $hashColumn, $sourceColumns, $dryRun) {
                    $entity = $table->get($id);
                    $storedHash = $entity->get($hashColumn);
                    $columnValues = array_map(function ($col) use ($entity) {
                        return $entity->get($col);
                    }, $sourceColumns);
                    $data = array_combine($sourceColumns, $columnValues);
                    $correctHash = $table->newEntity($data)->get($hashColumn);
                    if ($storedHash != $correctHash) {
                        $this->log[] = [$id, $storedHash, $correctHash];
                        $table->patchEntity($entity, [$hashColumn => $correctHash]);
                        $entity->setDirty('modified', true); // Don't update modified column
                        if (!$dryRun) {
                            return $table->save($entity);
                        }
                    }
                    return true;
                });
        }
        if ($progressBar) {
            $progressBar->increment($entities->count());
            $progressBar->draw();
        }
        return $entities->count();
    }

    public function execute(Arguments $args, ConsoleIo $io) {
        $dryRun = $args->getOption('dry-run');
        $machineReadable = $args->getOption('raw');
        $this->batchOperationSize = $args->getOption('batch-size');
        $progressBar = $args->getOption('progress-bar');

        $tableArg = $args->getArgument('table');
        $table = $this->getTable($tableArg);
        if (!$table) {
            $io->error(format(
                'The table "{table}" does not exist!',
                ['table' => $tableArg]
            ));
            $this->abort();
        }

        $hashColumn = $args->getOption('hash-column');
        $sourceColumns = explode(',', $args->getOption('source-columns'));
        $wrongColumns = array_diff(array_merge([$hashColumn], $sourceColumns),
                                   $table->getSchema()->columns());
        if ($wrongColumns) {
            $message = implode(
                "\n",
                array_map(function ($column) use ($table) {
                    return format(
                        'The table "{table}" does not have a column named {column}!',
                        ['table' => $table->getTable(), 'column' => $column]);
                    },
                    $wrongColumns
                )
            );
            $io->error($message);
            $this->abort();
        }

        $this->log = [];
        $query = $table->find()->select(['id' => $table->aliasField('id')]);
        if ($progressBar) {
            $progressBar = $io->helper('Progress');
            $progressBar->init(['total' => $query->count()]);
            $progressBar->draw();
        }
        $proceeded = $this->BatchOperationNewORM(
            $query,
            [$this, 'fixHash'],
            compact('table', 'hashColumn', 'sourceColumns', 'dryRun', 'progressBar')
        );

        if ($progressBar) {
            $io->out("\n");
        }
        if ($machineReadable) {
            $noProblemFormat = '0/{total}';
            $headerFormat = '{changed}/{total}';
            $logFormat = "{id}\t{old}\t{new}";
        } else {
            $noProblemFormat = '{total} rows checked, no problems found.';
            $headerFormat = '{total} rows checked, {changed} rows changed:';
            $logFormat = 'id {id} - hash changed from "{old}" to "{new}"';
        }
        if (empty($this->log)) {
            $io->out(format($noProblemFormat, ['total' => $proceeded]));
        } else {
            $io->out(format(
                $headerFormat,
                ['total' => $proceeded, 'changed' => count($this->log)]
            ));
            array_walk($this->log, function ($value, $index) use ($io, $logFormat) {
                $io->out(format(
                    $logFormat,
                    ['id' => $value[0], 'old' => $value[1], 'new' => $value[2]]
                ));
            });
        }
    }
}
