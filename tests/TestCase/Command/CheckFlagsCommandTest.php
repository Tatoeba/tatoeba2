<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class CheckFlagsCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public function testExecute_wrongLanguage(): void
    {
        $this->exec('check_flags invalid');
        $this->assertExitError();
        $this->assertErrorContains('invalid ISO language');
    }
}
