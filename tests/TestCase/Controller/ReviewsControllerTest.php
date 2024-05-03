<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class ReviewsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.PrivateMessages',
        'app.Sentences',
        'app.Users',
        'app.UsersLanguages',
        'app.UsersSentences',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/reviews/of/admin', null, '/en/reviews/of/admin/all' ],
            [ '/en/reviews/of/admin', 'contributor', '/en/reviews/of/admin/all' ],
            [ '/en/reviews/of/admin/all', null, true ],
            [ '/en/reviews/of/admin/all', 'contributor', true ],
            [ '/en/reviews/of/admin/ok', null, true ],
            [ '/en/reviews/of/admin/ok', 'contributor', true ],
            [ '/en/reviews/of/admin/ok/cmn', null, true ],
            [ '/en/reviews/of/admin/ok/cmn', 'contributor', true ],
            [ '/en/reviews/of/admin/foobar', null, '/en/reviews/of/admin/all' ],
            [ '/en/reviews/of/admin/foobar/eng', null, '/en/reviews/of/admin/all/eng' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testReviewsControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxAccessesProvider() {
        return [
            [ '/en/reviews/add_sentence/30/-1', null, false ],
            [ '/en/reviews/add_sentence/30/-1', 'contributor', true ],
            [ '/en/reviews/delete_sentence/30', null, false ],
            [ '/en/reviews/delete_sentence/30', 'contributor', true ], // does not exist
            [ '/en/reviews/delete_sentence/2', 'admin', true ], // does exist
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testRatingsControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
