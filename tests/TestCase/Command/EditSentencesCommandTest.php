<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class EditSentencesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public function testExecute_wrongUser(): void
    {
        $this->exec('edit_sentences invaliduser /dev/stdin');
        $this->assertExitError();
        $this->assertErrorContains('not a valid username');
    }
}
