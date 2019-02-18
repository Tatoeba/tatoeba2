<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class TagsControllerTest extends IntegrationTestCase {
    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.tags',
        'app.tags_sentences',
        'app.users',
        'app.users_languages'
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    private function logInAs($username) {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->session(['Auth' => ['User' => $user->toArray()]]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    private function _removeAsUser($username, $tagId, $sentenceId) {
        $TagsSentences = TableRegistry::get('TagsSentences');
        $beforeCount = $TagsSentences->find()
            ->where(['tag_id' => $tagId, 'sentence_id' => $sentenceId])
            ->count();
        if ($username) {
            $this->logInAs($username);
        }

        $this->get("/jpn/tags/remove_tag_from_sentence/$tagId/$sentenceId");

        $afterCount = $TagsSentences->find()
            ->where(['tag_id' => $tagId, 'sentence_id' => $sentenceId])
            ->count();
        return $afterCount - $beforeCount;
    }

    public function testGuestDoesntRemoveTag() {
        $delta = $this->_removeAsUser(null, 2, 2);
        $this->assertEquals(0, $delta);
    }

    public function testRegularUserDoesNotRemoveTag() {
        $delta = $this->_removeAsUser('contributor', 1, 8);
        $this->assertEquals(0, $delta);
    }

    public function testAdvancedUserAuthorDoesRemoveTag() {
        $delta = $this->_removeAsUser('advanced_contributor', 2, 2);
        $this->assertEquals(-1, $delta);
    }

    public function testDifferentAdvancedUserDoesNotRemoveTag() {
        $delta = $this->_removeAsUser('advanced_contributor', 1, 8);
        $this->assertEquals(0, $delta);
    }

    public function testCorpusMaintainerDoesRemoveTag() {
        $delta = $this->_removeAsUser('corpus_maintainer', 2, 2);
        $this->assertEquals(-1, $delta);
    }

    public function testAdminDoesRemoveTag() {
        $delta = $this->_removeAsUser('admin', 2, 2);
        $this->assertEquals(-1, $delta);
    }
}
