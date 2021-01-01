<?php
namespace App\Test\TestCase;

use Cake\Log\Engine\BaseLog;

/**
 * Logger class used for testing
 *
 * This logger stores the log message and makes it
 * available for later retrieval.
 */
class TestLog extends BaseLog {

    private $_logMessage = '';

    public function log($level, $message, $context = []) {
        $this->_logMessage = $this->_format($message, $context);
    }

    public function getLogMessage() {
        return $this->_logMessage;
    }
}
