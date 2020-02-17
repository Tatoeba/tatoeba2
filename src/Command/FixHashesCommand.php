<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Command;
use Cake\Datasource\ConnectionManager;
use Cake\Collection\Collection;
use Cake\Datasource\Exception\RecordNotFoundException;
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
            ])
            ->addOption('input', [
                'help' => 'Read a list of ids from the given file. ' .
                          '("stdin" will read from standard input)',
                'short' => 'i',
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
                function ($conn) use ($id, $table, $hashColumn, $sourceColumns, $dryRun, $io) {
                    try {
                        $entity = $table->get($id);
                    } catch (RecordNotFoundException $e) {
                        $io->error(format('{id} ignored: Record not found', ['id' => $id]));
                        return false;
                    }
                    $allColumns = array_merge($sourceColumns, [$hashColumn]);
                    $allStoredValues = $entity->extract($allColumns);
                    $allNewValues = $table->newEntity($entity->extract($sourceColumns))
                                    ->extract($allColumns);
                    if (array_diff($allStoredValues, $allNewValues)) {
                        $this->log[] = [$id, $entity->get($hashColumn), $allNewValues[$hashColumn]];
                        $table->patchEntity($entity, $allNewValues);
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
        $input = $args->getOption('input');
        if ($input === 'stdin') {
            $input = 'php://stdin';
        } elseif ($input && !file_exists($input)) {
            $io->error(format('{path} does not exist!', ['path' => $input]));
            $this->abort();
        }

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

        if ($input) {
            $ids = collection(file($input, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
                   ->filter(function ($v) { return preg_match('/^\d+$/', $v); });
            $total = $ids->count();
        } else {
            $query = $table->find()->select(['id' => $table->aliasField('id')]);
            $total = $query->count();
        }

        if ($progressBar) {
            $progressBar = $io->helper('Progress');
            $progressBar->init(['total' => $total]);
            $progressBar->draw();
        }

        if ($input) {
            $proceeded = $this->fixHash(
                $ids->map(function ($v) { return ['id' => $v]; }),
                compact('table', 'hashColumn', 'sourceColumns', 'dryRun', 'progressBar', 'io')
            );
        } else {
            $proceeded = $this->BatchOperationNewORM(
                $query,
                [$this, 'fixHash'],
                compact('table', 'hashColumn', 'sourceColumns', 'dryRun', 'progressBar', 'io')
            );
        }

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
