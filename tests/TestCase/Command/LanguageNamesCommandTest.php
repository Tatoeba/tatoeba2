<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class LanguageNamesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public function testExecute_wrongFile(): void
    {
        $this->exec('language_names invalidsource doesnotexists');
        $this->assertExitError();
        $this->assertErrorContains("Can't open 'doesnotexists'.");
    }

    public function testExecute_wrongSource(): void
    {
        $this->exec('language_names invalidsource resources/locales/fr/languages.po');
        $this->assertExitError();
        $this->assertErrorContains("Unknown translation source 'invalidsource'.");
    }
}
