<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\I18n\Time;
use Cake\TestSuite\IntegrationTestCase;

class ContributionsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.Contributions',
        'app.ContributionsStats',
        'app.LastContributions',
        'app.Users',
        'app.UsersLanguages',
        'app.PrivateMessages',
        'app.Sentences',
        'app.WikiArticles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/contributions/index', null, '/en/contributions/latest' ],
            [ '/en/contributions/index', 'contributor', '/en/contributions/latest' ],
            [ '/en/contributions/index/eng', null, '/en/contributions/latest/eng' ],
            [ '/en/contributions/index/eng', 'contributor', '/en/contributions/latest/eng' ],
            [ '/en/contributions/latest', null, true ],
            [ '/en/contributions/latest', 'contributor', true ],
            [ '/en/contributions/latest/eng', null, true ],
            [ '/en/contributions/latest/eng', 'contributor', true ],
            [ '/en/contributions/activity_timeline', null, '/en/contributions/activity_timeline/2017/04' ],
            [ '/en/contributions/activity_timeline', 'contributor', '/en/contributions/activity_timeline/2017/04' ],
            [ '/en/contributions/activity_timeline/2017/04', null, true ],
            [ '/en/contributions/activity_timeline/2017/04', 'contributor', true ],
            [ '/en/contributions/of_user/admin', null, true ],
            [ '/en/contributions/of_user/admin', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $now = new Time('2017-04-22 07:22:01');
        Time::setTestNow($now);
        $this->assertAccessUrlAs($url, $user, $response);
        Time::setTestNow();
    }
}
