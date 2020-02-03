<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class ReviewsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.sentences',
        'app.users',
        'app.users_languages',
        'app.users_sentences',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/reviews/of/admin', null, true ],
            [ '/eng/reviews/of/admin', 'contributor', true ],
            [ '/eng/reviews/of/admin/ok', null, true ],
            [ '/eng/reviews/of/admin/ok', 'contributor', true ],
            [ '/eng/reviews/of/admin/ok/cmn', null, true ],
            [ '/eng/reviews/of/admin/ok/cmn', 'contributor', true ],
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
            [ '/eng/reviews/add_sentence/30/-1', null, false ],
            [ '/eng/reviews/add_sentence/30/-1', 'contributor', true ],
            [ '/eng/reviews/delete_sentence/30', null, false ],
            [ '/eng/reviews/delete_sentence/30', 'contributor', true ], // does not exist
            [ '/eng/reviews/delete_sentence/2', 'admin', true ], // does exist
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testRatingsControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
