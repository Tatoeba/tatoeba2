<?php
declare(strict_types=1);

namespace App\Test\TestCase\View\Helper;

use App\View\AppView;
use App\View\Helper\ClickableLinksHelper;
use Cake\TestSuite\TestCase;

class ClickableLinksHelperTest extends TestCase
{
    protected $ClickableLinksHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
        $this->ClickableLinksHelper = new ClickableLinksHelper(new AppView());
    }

    public function tearDown(): void
    {
        unset($this->ClickableLinksHelper);
        parent::tearDown();
    }

    public function testBuildSentenceLink(): void
    {
        $expected = '<a href="/sentences/show/123">#123<md-tooltip ng-cloak="ng-cloak">An example sentence.</md-tooltip></a>';
        $result = $this->ClickableLinksHelper->buildSentenceLink(123, "An example sentence.");
        $this->assertSame($expected, $result);
    }
}
