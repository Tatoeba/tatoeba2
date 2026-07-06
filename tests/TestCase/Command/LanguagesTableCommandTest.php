<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class LanguagesTableCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public array $fixtures = [
        'app.Languages',
        'app.Users',
        'app.UsersLanguages',
        'app.Sentences',
    ];

    public function testExecute_reset(): void
    {
        $this->exec('languages_table reset');

        $languages = $this->fetchTable('Languages');

        $eng = $languages->findByCode('eng')->first();
        $this->assertSame(28, $eng->sentences);

        $jpn = $languages->findByCode('jpn')->first();
        $this->assertSame(6, $jpn->sentences);

        $this->assertSame(0, $jpn->group_1, 'among admins, no one has native level');
        $this->assertSame(0, $jpn->group_2, 'among corpus maintainers, no one has native level');
        $this->assertSame(0, $jpn->group_3, 'among advanced users, no one has native level');
        $this->assertSame(1, $jpn->group_4, 'among regular members, one has native level');

        $this->assertSame(0, $jpn->level_0, 'among all members, no one has level 0/5');
        $this->assertSame(1, $jpn->level_1, 'among all members, one has level 1/5');
        $this->assertSame(0, $jpn->level_2, 'among all members, no one has level 2/5');
        $this->assertSame(1, $jpn->level_3, 'among all members, one has level 3/5');
        $this->assertSame(0, $jpn->level_4, 'among all members, no one has level 4/5');
        $this->assertSame(1, $jpn->level_5, 'among all members, one has level 5/5 (=native)');
    }
}
