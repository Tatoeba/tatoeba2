<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ErrorComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;

class ErrorComponentTest extends TestCase
{
    private $Error;
    private $testErrorLog = 'test-error';
    private $logFilePath;
    private $originalConfig;

    private function eraseTestLog() {
        @unlink($this->logFilePath);
    }

    private function setupTestErrorLogFile() {
        $config = $this->originalConfig = Log::getConfig('error');
        $config['file'] = $this->testErrorLog;
        Log::drop('error');
        Log::setConfig('error', $config);
        $this->logFilePath = LOGS . $this->testErrorLog . '.log';
    }

    private function restoreErrorLogFile() {
        Log::drop('error');
        Log::setConfig('error', $this->originalConfig);
    }

    private function buildTestErrorComponent() {
        $request = new ServerRequest([
            'environment' => [
                'REMOTE_ADDR' => '93.184.216.340',
                'HTTP_REFERER' => 'https://example.com/to/tatoeba',
            ],
            'url' => '/here/and/there',
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        return new ErrorComponent($registry);
    }

    public function setUp()
    {
        parent::setUp();

        $this->setupTestErrorLogFile();
        $this->eraseTestLog();
        $this->Error = $this->buildTestErrorComponent();
    }

    public function tearDown()
    {
        unset($this->Error);
        $this->restoreErrorLogFile();
        $this->eraseTestLog();

        parent::tearDown();
    }

    public function testLog()
    {
        $date = ''; //date('Y-m-d H:i:s'); // can't test this part
        $expectedLog = <<<END_OF_LOG
$date Error: ouch!
Request URL: /here/and/there
Referer URL: https://example.com/to/tatoeba
Client IP: 93.184.216.340


END_OF_LOG;

        $this->Error->log('ouch!');

        $result = file_get_contents($this->logFilePath);
        $this->assertContains($expectedLog, $result);
    }
}
