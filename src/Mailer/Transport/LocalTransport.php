<?php
namespace App\Mailer\Transport;

use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\Transport\MailTransport;
use Exception;

/**
 * Local Mail Transport Class
 *
 * This subclass of CakePHP's MailTransport won't throw an
 * exception in case of a failure and enables optional logging
 * of the missed email.
 *
 * Configuration:
 *   'log' => true|false   Turns on/off logging of unsent mails
 */
class LocalTransport extends MailTransport {

    public function _mail($to, $subject, $message, $headers, $params = null) {
        try {
            $this->_parent($to, $subject, $message, $headers, $params);
        } catch (Exception $e) {
            if ($this->getConfig('log')) {
                $logMessage = [
                    $e->getMessage(),
                    '---Unsent Email Start---',
                    "To: $to",
                    "Subject: $subject",
                    $headers,
                    '',
                    $message,
                    '---Unsent Email End---'
                ];
                Log::error(implode("\n", $logMessage), 'unsent');
            }
        }
    }

    /**
     * Wrapped parent::_mail in order to make this class testable
     */
    protected function _parent($to, $subject, $message, $headers, $params) {
        parent::_mail($to, $subject, $message, $headers, $params);
    }
}
