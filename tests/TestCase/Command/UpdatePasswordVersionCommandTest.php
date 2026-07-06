<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Command\UpdatePasswordVersionCommand;
use App\PasswordHasher\VersionedPasswordHasher;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;

class UpdatePasswordVersionCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public array $fixtures = [
        'app.Users',
    ];

    private $previousSalt;

    public function setUp(): void {
        parent::setUp();
        $this->previousSalt = Security::getSalt();
        Security::setSalt('ze@9422#5dS?!99xx');
    }

    public function tearDown(): void {
        Security::setSalt($this->previousSalt);
        parent::tearDown();
    }

    public function testExecute(): void
    {
        $oldPasswordHash = $this->fetchTable('Users')->findByUsername('mr_old_style_passwd')->first()->password;

        $this->exec('update_password_version');

        $this->assertOutputContains('1 password hashes updated.');

        $newPasswordHash = $this->fetchTable('Users')->findByUsername('mr_old_style_passwd')->first()->password;
        $this->assertNotSame($oldPasswordHash, $newPasswordHash);

        $hasher = new VersionedPasswordHasher();
        $this->assertTrue($hasher->isOutdated($newPasswordHash));
        $this->assertTrue($hasher->check('123456', $newPasswordHash));
    }
}
