<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\I18n\Time;
use Cake\TestSuite\IntegrationTestCase;

class ContributionsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.contributions',
        'app.contributions_stats',
        'app.last_contributions',
        'app.users',
        'app.users_languages',
        'app.private_messages',
        'app.sentences',
        'app.wiki_articles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/contributions/index', null, '/eng/contributions/latest' ],
            [ '/eng/contributions/index', 'contributor', '/eng/contributions/latest' ],
            [ '/eng/contributions/index/eng', null, '/eng/contributions/latest/eng' ],
            [ '/eng/contributions/index/eng', 'contributor', '/eng/contributions/latest/eng' ],
            [ '/eng/contributions/latest', null, true ],
            [ '/eng/contributions/latest', 'contributor', true ],
            [ '/eng/contributions/latest/eng', null, true ],
            [ '/eng/contributions/latest/eng', 'contributor', true ],
            [ '/eng/contributions/activity_timeline', null, '/eng/contributions/activity_timeline/2017/04' ],
            [ '/eng/contributions/activity_timeline', 'contributor', '/eng/contributions/activity_timeline/2017/04' ],
            [ '/eng/contributions/activity_timeline/2017/04', null, true ],
            [ '/eng/contributions/activity_timeline/2017/04', 'contributor', true ],
            [ '/eng/contributions/of_user/admin', null, true ],
            [ '/eng/contributions/of_user/admin', 'contributor', true ],
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
