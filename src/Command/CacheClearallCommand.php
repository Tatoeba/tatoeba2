<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Cache\Cache;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Wrapper around the ClearCacheall Command
 *
 * We disable cache for CLI tools in config/bootstrap_cli.php to avoid file
 * permission problems, however running "cake cache clear_all" silently has
 * no effect when cache is disabled, which is very confusing. This problem
 * is resolved by the \App\Command\CacheClearCommand wrapper.
 *
 * This CacheClearallCommand wrapper makes sure "cake cache clear_all" executes
 * \App\Command\CacheClearCommand instead of \Cake\Core\CacheClearCommand::class.
 */
class CacheClearallCommand extends \Cake\Command\CacheClearallCommand
{
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $engines = Cache::configured();
        foreach ($engines as $engine) {
            $this->executeCommand(CacheClearCommand::class, [$engine], $io);
        }

        return static::CODE_SUCCESS;
    }
}
