<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

class ErrorComponent extends Component
{
    private function infoLine($infoKey, $infoValue) {
        $info = '';
        if ($infoValue) {
            $info = "$infoKey: $infoValue\n";
        }
        return $info;
    }

    public function format($message) {
        if (strlen($message)) {
            $request = $this->getController()->getRequest();
            $message .= "\nRequest URL: " . $request->getRequestTarget() . "\n";

            $referer = $request->getEnv('HTTP_REFERER');
            $message .= $this->infoLine('Referer URL', $referer);

            $clientIp = $request->clientIp();
            $message .= $this->infoLine('Client IP', $clientIp);

            $agent = $request->getEnv('HTTP_USER_AGENT');
            $message .= $this->infoLine('User Agent', $agent);

            return $message;
        }
    }

    protected function generateNewCode() {
        return uniqid();
    }

    public function traceError($message, $code = null) {
        if (is_null($code)) {
            $code = $this->generateNewCode();
        }
        $message = $this->format("[$code] $message");
        $this->_registry->getController()->log($message);

        return $code;
    }
}
