<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Cache\Cache;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

/**
 * Wrapper around the ClearCache Command
 *
 * We disable cache for CLI tools in config/bootstrap_cli.php to avoid file
 * permission problems, however running "cake cache clear" silently has
 * no effect when cache is disabled, which is very confusing.
 * This wrapper makes sure cache is enabled just when we want to clear it.
 */
class CacheClearCommand extends \Cake\Command\CacheClearCommand
{
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        if (Cache::enabled()) {
            $ret = parent::execute($args, $io);
        } else {
            Cache::enable();
            $ret = parent::execute($args, $io);
            Cache::disable();
        }
        return $ret;
    }
}
