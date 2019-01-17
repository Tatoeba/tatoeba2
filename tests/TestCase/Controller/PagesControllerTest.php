<?php
namespace App\Test\TestCase\Controller;

use App\Controller\PagesController;
use Cake\TestSuite\IntegrationTestCase;

class PagesControllerTest extends IntegrationTestCase
{
    public function testOldPagesRedirection()
    {
        $this->get("/eng/terms-of-use");

        $this->assertRedirect("/eng/terms_of_use");
    }
}
