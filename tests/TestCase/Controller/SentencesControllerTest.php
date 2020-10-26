<?php
namespace App\Test\TestCase\Controller;

use App\Model\Entity\User;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class SentencesControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.sentences',
        'app.users',
        'app.users_languages',
        'app.sentences_sentences_lists',
        'app.languages',
        'app.links',
        'app.private_messages',
        'app.reindex_flags',
        'app.audios',
        'app.transcriptions',
        'app.contributions',
        'app.tags',
        'app.tags_sentences',
        'app.users_sentences',
        'app.sentence_comments',
        'app.favorites_users',
        'app.sentences_lists',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Search.enabled', false);
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/sentences/show/1', null, true ],
            [ '/eng/sentences/show/1', 'contributor', true ],
            [ '/eng/sentences/show', null, 302 ],
            [ '/eng/sentences/show/random', null, 302 ],
            [ '/eng/sentences/show/fra', null, true ], // no redirect because Search.enabled = false
            [ '/eng/sentences/show/9999999999', null, true ],
            [ '/eng/sentences/go_to_sentence?sentence_id=2', null, '/eng/sentences/show/2' ],
            [ '/eng/sentences/go_to_sentence?sentence_id=2', 'contributor', '/eng/sentences/show/2' ],
            [ '/eng/sentences/go_to_sentence?sentence_id=', null, '/eng/sentences/show/random' ],
            [ '/eng/sentences/add', null, '/eng/users/login?redirect=%2Feng%2Fsentences%2Fadd' ],
            [ '/eng/sentences/add', 'contributor', true ],
            [ '/eng/sentences/delete/1', null, '/eng/users/login?redirect=%2Feng%2Fsentences%2Fdelete%2F1' ],
            [ '/eng/sentences/delete/1', 'contributor', '/eng/sentences/show/1' ],
            [ '/eng/sentences/delete/1', 'admin', '/eng/sentences/show/1' ],
            [ '/eng/sentences/index', null, true ],
            [ '/eng/sentences/index', 'contributor', true ],
            [ '/eng/sentences/search', null, true ],
            [ '/eng/sentences/search', 'contributor', true ],
            [ '/eng/sentences/search?query=hacer&from=spa&to=fra', null, true ],
            [ '/eng/sentences/search?query=hacer&from=spa&to=fra&sort=random', null, true ], // TODO no redirect because Search.enabled = false
            [ '/eng/sentences/advanced_search', null, true ],
            [ '/eng/sentences/advanced_search', 'contributor', true ],
            [ '/eng/sentences/show_all_in/eng/none', null, true ],
            [ '/eng/sentences/show_all_in/eng/none', 'contributor', true ],
            [ '/eng/sentences/of_user/contributor', null, true ],
            [ '/eng/sentences/of_user/contributor', 'contributor', true ],
            [ '/eng/sentences/of_user/contributor/fra', null, true ],
            [ '/eng/sentences/of_user/nonexistent/fra', null, true ],
            [ '/eng/sentences/import', null, '/eng/users/login?redirect=%2Feng%2Fsentences%2Fimport' ],
            [ '/eng/sentences/import', 'contributor', '/eng' ],
            [ '/eng/sentences/import', 'advanced_contributor', '/eng' ],
            [ '/eng/sentences/import', 'corpus_maintainer', '/eng' ],
            [ '/eng/sentences/import', 'admin', true ],
            [ '/eng/sentences/with_audio', null, '/eng/audio/index' ],
            [ '/eng/sentences/with_audio', 'contributor', '/eng/audio/index' ],
            [ '/eng/sentences/with_audio/spa', null, '/eng/audio/index/spa' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxAccessesProvider() {
        return [
            [ '/eng/sentences/adopt/14', null, false ],
            [ '/eng/sentences/adopt/14', 'contributor', true ],
            [ '/eng/sentences/let_go/19', null, false ],
            [ '/eng/sentences/let_go/19', 'contributor', true ],
            [ '/eng/sentences/random/und', null, true ],
            [ '/eng/sentences/random/und', 'contributor', true ],
            [ '/eng/sentences/random/fra', null, true ],
            [ '/eng/sentences/random/fra', 'contributor', true ],
            [ '/eng/sentences/get_neighbors_for_ajax/1/eng', null, true ],
            [ '/eng/sentences/get_neighbors_for_ajax/1/eng', 'contributor', true ],
            [ '/eng/sentences/add_an_other_sentence', null, false ],
            [ '/eng/sentences/add_an_other_sentence', 'contributor', true],
        ];
    }

    private function addSentencesOfUser($userId, $nbSentences) {
        $newSentences = array();
        for ($i = 1; $i <= $nbSentences; $i++) {
            $newSentences[] = [
                'lang' => 'eng',
                'text' => "Ay ay ay $i.",
                'user_id' => $userId,
            ];
            $newSentences[] = [
                'lang' => 'eng',
                'text' => "Oy oy oy $i.",
                'user_id' => 1,
            ];
        }
        $sentences = TableRegistry::getTableLocator()->get('Sentences');
        $entities = $sentences->newEntities($newSentences);
        $sentences->saveMany($entities);

        return $sentences->find()->where(['user_id' => $userId])->count();
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }

    public function addSentenceProvider () {
        return [
            'as guest' => [
                null,
                ['value' => 'test', 'selectedLang' => 'eng'],
                'assertResponseError'
            ],
            'as member' => [
                'contributor',
                ['value' => 'test', 'selectedLang' => 'eng'],
                'assertResponseOk'
            ],
            'with license' => [
                'contributor',
                ['value' => 'test', 'selectedLang' => 'eng', 'sentenceLicense' => 'CC BY 2.0 FR'],
                'assertResponseOk'
            ],
            'user without profile language' => [
                'admin',
                ['value' => 'SPAM', 'selectedLang' => 'eng'],
                'assertResponseEmpty'
            ],
            'as member but no value' => [
                'contributor',
                ['selectedLang' => 'eng'],
                'assertResponseEmpty'
            ],
            'as member but empty value' => [
                'contributor',
                ['value' => '', 'selectedLang' => 'eng'],
                'assertResponseEmpty'
            ],
            'as member but no selectedLang' => [
                'contributor',
                ['value' => 'test'],
                'assertResponseEmpty'
            ],
            'as member but empty selectedLang' => [
                'contributor',
                ['value' => 'test', 'selectedLang' => ''],
                'assertResponseEmpty'
            ],
        ];
    }

    /**
     * @dataProvider addSentenceProvider
     */
    public function testAddSentence($user, $data, $assertion) {
        if ($user) {
            $this->logInAs($user);
        }
        $this->ajaxPost('/jpn/sentences/add_an_other_sentence', $data);
        $this->$assertion();
    }

    public function addSentenceWithLicenseProvider() {
        return [
            'user cannot choose license, submits no license' =>
            [
                'corpus_maintainer',
                ['value' => 'test', 'selectedLang' => 'jpn'],
                'CC BY 2.0 FR'
            ],
            'user cannot choose license, submits wrong license' =>
            [
                'corpus_maintainer',
                ['value' => 'test', 'selectedLang' => 'jpn', 'sentenceLicense' => 'CC0 1.0'],
                null
            ],
            'user cannot choose license, submits invalid license' =>
            [
                'corpus_maintainer',
                ['value' => 'test', 'selectedLang' => 'jpn', 'sentenceLicense' => 'xyz'],
                null
            ],
            'user cannot choose license, submits empty license' =>
            [
                'corpus_maintainer',
                ['value' => 'test', 'selectedLang' => 'jpn', 'sentenceLicense' => ''],
                'CC BY 2.0 FR'
            ],
            'user cannot choose license, submits default license' =>
            [
                'corpus_maintainer',
                ['value' => 'test', 'selectedLang' => 'jpn', 'sentenceLicense' => 'CC BY 2.0 FR'],
                'CC BY 2.0 FR'
            ],
            'user can choose license, submits no license' =>
            [
                'contributor',
                ['value' => 'test', 'selectedLang' => 'fra'],
                'CC BY 2.0 FR'
            ],
            'user can choose license, submits invalid license' =>
            [
                'contributor',
                ['value' => 'test', 'selectedLang' => 'fra', 'sentenceLicense' => 'xyz'],
                null
            ],
            'user can choose license, submits empty license' =>
            [
                'contributor',
                ['value' => 'test', 'selectedLang' => 'fra', 'sentenceLicense' => ''],
                'CC BY 2.0 FR'
            ],
            'user can choose license, submits valid license' =>
            [
                'contributor',
                ['value' => 'test', 'selectedLang' => 'fra', 'sentenceLicense' => 'CC0 1.0'],
                'CC0 1.0'
            ],
        ];
    }

    /**
     * @dataProvider addSentenceWithLicenseProvider
     */
    public function testAddSentence_WithLicense($user, $data, $expectedLicense) {
        $this->logInAs($user);
        $this->addHeader('Accept', 'application/json');
        $this->ajaxPost('/jpn/sentences/add_an_other_sentence', $data);

        $response = json_decode($this->_response->getBody());
        if ($expectedLicense) {
            $sentences = TableRegistry::getTableLocator()->get('Sentences');
            $license = $sentences->get($response->sentence->id)->license;
            $this->assertEquals($expectedLicense, $license);
        } else {
            $this->assertEmpty($response);
        }
    }

    public function testEditSentence_doesntWorkForUnknownSentence() {
        $this->logInAs('contributor');
        $this->ajaxPost('/jpn/sentences/edit_sentence', [
            'id' => '999999', 'lang' => 'epo', 'text' => 'Forlasu!',
        ]);
        $this->assertRedirect('/jpn/home');
    }

    public function testEditLicense_returnsHTTP400IfNoId() {
        $this->logInAs('contributor');
        $this->post('/jpn/sentences/edit_license', [
            'license' => 'CC0 1.0',
        ]);
        $this->assertResponseCode(400);
    }

    public function testEditLicense_returnsHTTP400IfNoLicense() {
        $this->logInAs('contributor');
        $this->post('/jpn/sentences/edit_license', [
            'id' => 48,
        ]);
        $this->assertResponseCode(400);
    }

    public function editLicenseProvider() {
        return [
            'can edit as user with permissions' =>
            [48, 'CC0 1.0', 'contributor', 'assertNotEquals'],
            'cannot edit as user without permissions' =>
            [54, 'CC0 1.0', 'kazuki', 'assertEquals'],
            'cannot switch to "admin_only" license as user' =>
            [48, '', 'contributor', 'assertEquals'],
            'cannot switch from "Licensing issue" as user' =>
            [52, 'CC BY 2.0 FR', 'advanced_contributor', 'assertEquals'],
        ];
    }

    /**
     * @dataProvider editLicenseProvider
     */
    public function testEditLicense_severalScenarios($sentenceId, $license, $username, $assertMethod) {
        $sentences = TableRegistry::get('Sentences');
        $oldSentence = $sentences->get($sentenceId);
        $this->logInAs($username);
        $this->post('/jpn/sentences/edit_license', [
            'id' => $sentenceId,
            'license' => $license,
        ]);
        $newSentence = $sentences->get($sentenceId);
        $this->$assertMethod($oldSentence->license, $newSentence->license);
    }

    public function testSaveTranslation_asGuest() {
        $this->ajaxPost('/jpn/sentences/save_translation', [
            'id' => '26',
            'selectLang' => 'fra',
            'value' => 'Elle essaie toujours de faire ce qu\'elle pense.'
        ]);
        $this->assertResponseError();
    }

    public function testSaveTranslation_asMember() {
        $this->logInAs('contributor');
        $this->ajaxPost('/jpn/sentences/save_translation', [
            'id' => '26',
            'selectLang' => 'fra',
            'value' => 'Elle essaie toujours de faire ce qu\'elle pense.'
        ]);
        $this->assertResponseOk();
    }

    public function testSaveTranslation_sentenceWithLicensingIssue() {
        $this->logInAs('contributor');
        $this->ajaxPost('/eng/sentences/save_translation', [
            'id' => '52',
            'selectLang' => 'rus',
            'value' => 'translation text',
        ]);
        $this->assertResponseEmpty();
    }

    public function testChangeLanguage_asGuest() {
        $this->ajaxPost('/jpn/sentences/change_language', [
            'id' => '9',
            'newLang' => 'eng',
        ]);
        $this->assertResponseError();
    }

    public function testChangeLanguage_asMember() {
        $this->logInAs('contributor');
        $this->ajaxPost('/jpn/sentences/change_language', [
            'id' => '9',
            'newLang' => 'eng',
        ]);
        $this->assertResponseOk();
    }

    public function testEditCorrectness_asGuest() {
        $this->enableCsrfToken();
        $this->post('/jpn/sentences/edit_correctness', ['id' => '1', 'correctness' => '-1']);
        $this->assertRedirect('/jpn/users/login');
    }

    public function testEditCorrectness_asContributor() {
        $this->logInAs('contributor');
        $this->post('/jpn/sentences/edit_correctness', ['id' => '1', 'correctness' => '-1']);
        $this->assertRedirect('/');
    }

    public function testEditCorrectness_asAdvancedContributor() {
        $this->logInAs('advanced_contributor');
        $this->post('/jpn/sentences/edit_correctness', ['id' => '1', 'correctness' => '-1']);
        $this->assertRedirect('/');
    }

    public function testEditCorrectness_asCorpusMaintainer() {
        $this->logInAs('advanced_contributor');
        $this->post('/jpn/sentences/edit_correctness', ['id' => '1', 'correctness' => '-1']);
        $this->assertRedirect('/');
    }

    public function testEditCorrectness_asAdmin() {
        $this->logInAs('admin');
        $this->post('/jpn/sentences/edit_correctness', ['id' => '1', 'correctness' => '-1']);
        $this->assertRedirect('/jpn/sentences/show/1');
    }

    public function testEditAudio_asGuest() {
        $this->enableCsrfToken();
        $this->post('/jpn/sentences/edit_audio', ['id' => '1', 'hasaudio' => '1', 'ownerName' => 'kazuki']);
        $this->assertRedirect('/jpn/users/login');
    }

    public function testEditAudio_asContributor() {
        $this->logInAs('contributor');
        $this->post('/jpn/sentences/edit_audio', ['id' => '1', 'hasaudio' => '1', 'ownerName' => 'kazuki']);
        $this->assertRedirect('/');
    }

    public function testEditAudio_asAdvancedContributor() {
        $this->logInAs('advanced_contributor');
        $this->post('/jpn/sentences/edit_audio', ['id' => '1', 'hasaudio' => '1', 'ownerName' => 'kazuki']);
        $this->assertRedirect('/');
    }

    public function testEditAudio_asCorpusMaintainer() {
        $this->logInAs('advanced_contributor');
        $this->post('/jpn/sentences/edit_audio', ['id' => '1', 'hasaudio' => '1', 'ownerName' => 'kazuki']);
        $this->assertRedirect('/');
    }

    public function testEditAudio_asAdmin() {
        $this->logInAs('admin');
        $this->post('/jpn/sentences/edit_audio', ['id' => '1', 'hasaudio' => '1', 'ownerName' => 'kazuki']);
        $this->assertRedirect('/jpn/sentences/show/1');
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage_asGuest() {
        $user = 'kazuki';
        $userId = 7;
        $defaultNbPerPage = User::$defaultSettings['sentences_per_page'];

        $nbSentences = $this->addSentencesOfUser($userId, $defaultNbPerPage + 1);

        $lastPage = ceil($nbSentences / $defaultNbPerPage);

        $this->get("/eng/sentences/of_user/$user?page=9999999");
        $this->assertRedirect("/eng/sentences/of_user/$user?page=$lastPage");
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage_withUserSetting() {
        $user = 'kazuki';
        $userId = 7;
        $users = TableRegistry::getTableLocator()->get('Users');
        $nbPerPageSetting = $users->getSettings($userId)['settings']['sentences_per_page'];

        $nbSentences = $this->addSentencesOfUser($userId, $nbPerPageSetting + 1);

        $lastPage = ceil($nbSentences / $nbPerPageSetting);

        $this->logInAs($user);
        $this->get("/eng/sentences/of_user/$user?page=9999999");
        $this->assertRedirect("/eng/sentences/of_user/$user?page=$lastPage");
    }
}
