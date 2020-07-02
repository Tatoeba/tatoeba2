<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Psr\Log\LogLevel;

class ErrorComponent extends Component
{
    private function infoLine($infoKey, $infoValue) {
        $info = '';
        if ($infoValue) {
            $info = "$infoKey: $infoValue\n";
        }
        return $info;
    }

    public function log($message, $level = LogLevel::ERROR, $context = []) {
        if (strlen($message)) {
            $message .= "\nRequest URL: " . $this->request->getRequestTarget() . "\n";

            $referer = $this->request->getEnv('HTTP_REFERER');
            $message .= $this->infoLine('Referer URL', $referer);

            $clientIp = $this->request->clientIp();
            $message .= $this->infoLine('Client IP', $clientIp);

            $agent = $this->request->getEnv('HTTP_USER_AGENT');
            $message .= $this->infoLine('User Agent', $agent);

            return parent::log($message);
        }
    }
}
