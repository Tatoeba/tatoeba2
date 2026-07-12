<?php
declare(strict_types=1);

namespace App\Console;

use Cake\Console\ConsoleInputArgument;
use Cake\Console\ConsoleOptionParser;

/**
 * Like ConsoleOptionParser but allows a variable-length argument list.
 */
class VarArgsConsoleOptionParser extends ConsoleOptionParser
{
    protected function _parseArg(string $argument, array $args): array
    {
        $args = parent::_parseArg($argument, $args);

        // define an extra argument in case more are coming
        $next = count($args);
        if ($this->_args && !isset($this->_args[$next])) {
            $prev = $this->_args[$next - 1];
            $this->_args[$next] = new ConsoleInputArgument($prev->name());
        }

        return $args;
    }
}
