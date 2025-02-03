<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Http\Session;

/**
 * SessionGc command. This script basically executes session_gc() (https://php.net/session_gc)
 * using whatever session handler has been configured in the CakePHP app.
 * This command is meant to be executed regularly with a cron job, on systems where triggering
 * of session garbage collector is not probability-based (session.gc_probability = 0 in php.ini).
 */
class SessionGcCommand extends Command
{
    public function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription(
            'Sweeps stale sessions. This command is meant to be executed regularly with a cron job.'
        );
        return $parser;
    }

    /**
     * Triggers the session garbage collector of the configured session handler.
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $sessionConfig = (array)Configure::read('Session') + [
            'ini' => [
                # Set chance of running garbage collector to 100%
                # (Can be replaced with executing session_gc() after we upgrade to PHP 7)
                'session.gc_probability' => '1',
                'session.gc_divisor'     => '1',
            ],
        ];
        $session = Session::create($sessionConfig);
        # In theory we could use $session->start(); $session->destroy();
        # but CakePHP's Session skips running session_start() and
        # session_destroy() when we are running CLI
        session_start();
        session_destroy();
    }
}
