<?php
namespace App\Test\TestCase;

use Cake\Log\Engine\BaseLog;
use Stringable;

/**
 * Logger class used for testing
 *
 * This logger stores the log message and makes it
 * available for later retrieval.
 */
class TestLog extends BaseLog {

    private $_logMessage = '';

    public function log($level, Stringable|string $message, array $context = []): void {
        $this->_logMessage = $this->interpolate($message, $context);
    }

    public function getLogMessage() {
        return $this->_logMessage;
    }
}
