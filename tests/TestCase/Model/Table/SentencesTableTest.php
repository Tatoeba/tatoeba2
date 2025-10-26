<?php
namespace App\Test\TestCase\Model\Table;

use App\Test\TestCase\SearchMockTrait;
use App\Model\Table\SentencesTable;
use App\Behavior\Sphinx;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use App\Model\CurrentUser;
use App\Lib\Autotranscription;
use Cake\Datasource\Exception\RecordNotFoundException;
use App\Model\Entity\Contribution;
use App\Model\Entity\User;
use Cake\Utility\Hash;
use Cake\I18n\I18n;

class SentencesTableTest extends TestCase {
    use SearchMockTrait;

    public $fixtures = array(
        'app.sentences',
        'app.users',
        'app.users_languages',
        'app.contributions',
        'app.disabled_audios',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.tags',
        'app.tags_sentences',
        'app.languages',
        'app.links',
        'app.transcriptions',
        'app.reindex_flags',
        'app.audios',
        'app.users_sentences',
        'app.favorites_users',
    );

    function setUp() {
        parent::setUp();
        Configure::write('AutoTranscriptions.enabled', true);

        $foundIds = [1, 2, 3, 4, 5, 16, 17, 18, 19, 20];
        $totalResults = 10;
        $this->enableMockedSearch($foundIds, $totalResults);

        $this->Sentence = TableRegistry::getTableLocator()->get('Sentences');
        $autotranscription = $this->_installAutotranscriptionMock();
        $autotranscription
            ->expects($this->any())
            ->method('cmn_detectScript')
            ->will($this->returnValue('Hans'));
        $autotranscription
            ->expects($this->any())
            ->method('jpn_Jpan_to_Hrkt_generate')
            ->with($this->logicalNot($this->isEmpty()), $this->anything())
            ->will($this->returnValue('transcription in furigana'));
        $autotranscription
            ->expects($this->any())
            ->method('jpn_Jpan_to_Hrkt_validate')
            ->will($this->returnValue(true));
    }

    function _installAutotranscriptionMock() {
        $autotranscription = $this->getMockBuilder(Autotranscription::class)
            ->setMethods([
                'cmn_detectScript',
                'jpn_Jpan_to_Hrkt_generate',
                'jpn_Jpan_to_Hrkt_validate',
            ])
            ->getMock();

        $this->Sentence->Transcriptions->setAutotranscription($autotranscription);
        return $autotranscription;
    }

    function tearDown() {
        unset($this->Sentence);
        parent::tearDown();
    }

    function beOwnerOfCurrentSentence($id) {
        $sentence = $this->Sentence->get($id);
        CurrentUser::store(array('id' => $sentence->user_id));
    }

    function testSave_firesEventOnUpdate() {
        $dispatched = false;
        $id = 1;
        $data = $this->Sentence->newEntity([
            'id' => $id,
            'text' => 'Changing text of sentence #1.',
        ]);
        $model = $this->Sentence;
        $model->getEventManager()->on(
            'Model.Sentence.saved',
            function (Event $event) use ($model, &$dispatched, $id, $data) {
                $this->assertSame($model, $event->getSubject());
                // filter out unpredictable keys like 'modified' => now()
                // from $event->data['data']
                $result = [
                    'id' => $event->getData('id'),
                    'created' => $event->getData('created'),
                    'data' => $event->getData('data')
                ];
                $created = $result['created'];
                $expectedEventData = compact('id', 'created', 'data');
                $this->assertEquals($expectedEventData, $result);
                $dispatched = true;
            }
        );

        $this->Sentence->save($data);

        $this->assertTrue($dispatched);
    }

    function testSaveNewSentence_addsOneSentence() {
        $oldNumberOfSentences = $this->Sentence->find('all')->count();
        $this->Sentence->saveNewSentence('Hello world.', 'eng', 1);
        $newNumberOfSentences = $this->Sentence->find('all')->count();
        $sentencesAdded = $newNumberOfSentences - $oldNumberOfSentences;

        $this->assertEquals($sentencesAdded, 1);
    }

    function testSaveNewSentence_nullifiesEmptyLangs() {
        $text = 'Hello world.';

        $this->Sentence->saveNewSentence($text, '', 1);
        $savedSentence = $this->Sentence->find('all')
            ->where(['text' => $text])
            ->first();

        $this->assertNull($savedSentence->lang);
    }

    function testSaveNewSentence_returnsTrueWhenSaved() {
        $returnValue = $this->Sentence->saveNewSentence('Hello world.', 'eng', 1);
        $this->assertTrue((bool)$returnValue);
    }

    function duplicatesProvider() {
        return [
            'Exact duplicate' =>
                [['What are you doing?', 'eng', 1], 27],
            'Duplicate ending with space' =>
                [['What are you doing? ', 'eng', 1], 27],
            'Duplicate beginning with space' =>
                [[' What are you doing?', 'eng', 1], 27],
            'Duplicate with extra spaces between words' =>
                [["What  are you\u{2004}\u{2003}doing?", 'eng', 1], 27],
            'Tab at beginning, LINE FEED at end' =>
                [["\u{9}Bana ne önerirsin?\u{a}", 'tur', 2], 41],
            'NEXT LINE at beginning, NO-BREAK SPACE at end' =>
                [["\u{85}Bana ne önerirsin?\u{a0}", 'tur', 2], 41],
            'LINE SEPARATOR at beginning' =>
                [["\u{2028}Bana ne önerirsin?", 'tur', 2], 41],
            'IDEOGRAPHIC SPACE at end' =>
                [["Bana ne önerirsin?\u{3000}", 'tur', 2], 41],
            'More than one whitespace character' =>
                [["\u{85} Bana ne önerirsin?\u{a0}\u{2029}", 'tur', 2], 41],
            'Decomposed e acute' =>
                [["Elle donna une re\u{301}ponse e\u{301}vasive.", 'fra', 4], 17],
        ];
    }

    /**
     * @dataProvider duplicatesProvider
     */
    function testSaveNewSentence_doesntAddDuplicate($sentence, $id) {
        $saved = $this->Sentence->saveNewSentence(...$sentence);
        $this->assertEquals($id, $saved->id);
        $this->assertTrue($saved->isDuplicate);
    }

    function testSaveNewSentence_advancedContributorCanAdoptSentenceFromSpammer() {
        CurrentUser::store($this->Sentence->Users->get(3));
        $this->Sentence->saveNewSentence('Bana ne önerirsin?', 'tur', 3);
        $sentence = $this->Sentence->getSentenceWith(41);
        $this->assertEquals(3, $sentence->user_id);
    }

    function testSaveNewSentence_contributorCannotAdoptSentenceFromSpammer() {
        CurrentUser::store($this->Sentence->Users->get(4));
        $sentence = $this->Sentence->saveNewSentence('Bana ne önerirsin?', 'tur', 4);
        $this->assertEquals(6, $sentence->user_id);
        $this->assertTrue($sentence->isDuplicate);
    }

    function testSaveNewSentence_canAdoptOrphanSentences() {
        CurrentUser::store($this->Sentence->Users->get(4));
        $sentence = $this->Sentence->saveNewSentence('An orphan sentence.', 'eng', 4);
        $this->assertEquals(4, $sentence->user_id);
    }

    function testSaveTranslation_links() {
        CurrentUser::store(['id' => 7]);

        $translationFromSentenceId = 1;
        $lastSentence = $this->Sentence->find()->select(['max' => 'MAX(id)'])->first();
        $newlyCreatedSentenceId = $lastSentence->max + 1;

        $this->Sentence->Links = $this->getMockBuilder(LinksTable::class)
            ->setMethods(['add', 'findDirectAndIndirectTranslationsIds'])
            ->getMock();

        $this->Sentence->Links
            ->expects($this->once())
            ->method('add')
            ->with($translationFromSentenceId, $newlyCreatedSentenceId, 'eng', 'eng');

        $translation = $this->Sentence->saveTranslation(
            $translationFromSentenceId,
            'eng',
            'This is the translation.',
            'eng'
        );
    }

    function testSave_validSentence() {
        $data = $this->Sentence->newEntity([
            'text' => 'Hi there!',
        ]);

        $result = $this->Sentence->save($data);
        $this->assertTrue((bool)$result);
    }

    function testSave_checksValidLicense() {
        $data = $this->Sentence->newEntity([
            'text' => 'Trying to save a sentence with an invalid license.',
            'license' => 'some-strange-thing',
        ]);
        $result = $this->Sentence->save($data);
        $this->assertFalse((bool)$result);
    }

    function testSave_checksLicenseUpdatesFine() {
        $this->beOwnerOfCurrentSentence(48);
        $data = $this->Sentence->get(48);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
        $result = $this->Sentence->save($data);

        $this->assertTrue((bool)$result);
    }

    function testSave_checksLicenseDoesntUpdateIfCurrentUserIsNotOwner() {
        CurrentUser::store(array('id' => 3));
        $data = $this->Sentence->get(48);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
        $result = $this->Sentence->save($data);
        $this->assertFalse((bool)$result);
    }

    function testSave_checksLicenseDoesUpdateIfAdmin() {
        CurrentUser::store($this->Sentence->Users->get(1));
        $data = $this->Sentence->get(48);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
        $result = $this->Sentence->save($data);
        $this->assertTrue((bool)$result);
    }

    function testSave_checksLicenseDoesntUpdateIfBasedOnIdIsNull() {
        $this->beOwnerOfCurrentSentence(1);
        $data = $this->Sentence->get(1);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
        $result = $this->Sentence->save($data);
        $this->assertFalse((bool)$result);
    }

    function testSave_checksLicenseDoesntUpdateIfAddedAsTranslation() {
        $this->beOwnerOfCurrentSentence(49);
        $data = $this->Sentence->get(49);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
        $result = $this->Sentence->save($data);
        $this->assertFalse((bool)$result);
    }

    function testSave_checksLicenseDoesntUpdateToAMoreRestrictiveLicense() {
        $this->beOwnerOfCurrentSentence(51);
        $data = $this->Sentence->get(51);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC BY 2.0 FR']);
        $result = $this->Sentence->save($data);
        $this->assertFalse((bool)$result);
    }

    function testSave_checksLicenseUpdatesFromNullLicense() {
        $this->beOwnerOfCurrentSentence(52);
        $data = $this->Sentence->get(52);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC BY 2.0 FR']);
        $result = $this->Sentence->save($data);
        $this->assertTrue((bool)$result);
    }

    function testSave_checksLicenseDoesntUpdateToTheSameLicense() {
        $this->beOwnerOfCurrentSentence(48);
        $data = $this->Sentence->get(48);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC BY 2.0 FR']);
        $result = $this->Sentence->save($data);
        $this->assertFalse((bool)$result);
    }

    function testSave_checksLicenseDoesntUpdateIfOwnerIsNotTheOriginalCreator() {
        $this->beOwnerOfCurrentSentence(50);
        $data = $this->Sentence->get(50);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
        $result = $this->Sentence->save($data);
        $this->assertFalse((bool)$result);
    }

    function testSave_checksLicenseBypassValidationIfAdmin() {
        CurrentUser::store($this->Sentence->Users->get(1));
        $data = $this->Sentence->get(50);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CC0 1.0']);
        $result = $this->Sentence->save($data);
        $this->assertTrue((bool)$result);
    }

    function testSave_checksLicenseDoesntUpdateToInvalidLicense() {
        CurrentUser::store($this->Sentence->Users->get(1));
        $data = $this->Sentence->get(50);
        $data = $this->Sentence->patchEntity($data, ['license' => 'CL42 Crazy License']);
        $result = $this->Sentence->save($data);
        $this->assertFalse((bool)$result);
    }

    function testSave_setsDefaultLicenseSettingOnCreation() {
        $data = $this->Sentence->saveNewSentence(
            "User 7's default license is CC0 1.0",
            'eng',
            7
        );
        $savedSentence = $this->Sentence->save($data);
        $this->assertEquals('CC0 1.0', $savedSentence->license);
    }

    function testSave_doesNotChangeLicenseOnUpdate() {
        $data = $this->Sentence->newEntity([
            'id' => 1,
            'text' => 'Updating sentence #1.',
            'user_id' => 7,
        ]);

        $savedSentence = $this->Sentence->save($data);
        $savedSentence = $this->Sentence->get($savedSentence->id);
        $this->assertEquals('CC BY 2.0 FR', $savedSentence->license);
    }

    function testSentenceAdditionAddsTranscription() {
        $data = $this->Sentence->newEntity([
            'text' => '歌舞伎ってご存知ですか？',
            'lang' => 'jpn'
        ]);
        $newSentence = $this->Sentence->save($data);
        $transcriptions = $this->Sentence->Transcriptions->find()
            ->where(['sentence_id' => $newSentence->id])
            ->count();
        $this->assertEquals(1, $transcriptions);
    }

    function testSentenceTextEditionUpdatesScript() {
        $autotranscription = $this->_installAutotranscriptionMock();
        $autotranscription
            ->expects($this->once())
            ->method('cmn_detectScript')
            ->will($this->returnValue('Hant'));
        $cmnSentenceId = 2;
        $data = $this->Sentence->get($cmnSentenceId);
        $data->text = '問題的根源是，在當今世界，愚人充滿了自信，而智者充滿了懷疑。';
        $this->Sentence->save($data);
        $result = $this->Sentence->get($cmnSentenceId);
        $this->assertEquals('Hant', $result->script);
    }

    function testSentenceFlagEditionUpdatesScript() {
        $cmnSentenceId = 2;
        $user = $this->Sentence->Users->get(1);
        CurrentUser::store($user);

        $this->Sentence->changeLanguage($cmnSentenceId, 'eng');

        $result = $this->Sentence->get($cmnSentenceId, ['fields' => ['script']]);
        $this->assertNull($result->script);
    }

    function testSentenceTextEditionRegeneratesTranscriptions() {
        $jpnSentenceId = 6;
        $conditions = array('sentence_id' => $jpnSentenceId);
        $transcrBefore = $this->Sentence->Transcriptions->find('all')
            ->where($conditions)
            ->first();
        $data = $this->Sentence->get($jpnSentenceId);
        $data->text = '未来から来ました。';
        $this->Sentence->save($data);

        $transcrAfter = $this->Sentence->Transcriptions->find('all')
            ->where($conditions)
            ->first();

        $this->assertNotEquals($transcrBefore, $transcrAfter);
    }

    function assertLinksLanguage($sentenceId, $prefix, $expectedLang) {
        $expectedLink = ["${prefix}_lang" => $expectedLang];
        $links = $this->Sentence->Links
            ->find()
            ->select(["${prefix}_lang"])
            ->where(["${prefix}_id" => $sentenceId])
            ->enableHydration(false)
            ->all();
        foreach ($links as $link) {
            $this->assertEquals($expectedLink, $link);
        }
    }

    function testSentenceFlagEditionUpdatesFlagsInLinksTable_oldDesign() {
        $user = $this->Sentence->Users->get(1);
        CurrentUser::store($user);
        $cmnSentenceId = 2;
        $newLang = 'por';

        $this->Sentence->changeLanguage($cmnSentenceId, $newLang);

        $this->assertLinksLanguage($cmnSentenceId, 'sentence',    $newLang);
        $this->assertLinksLanguage($cmnSentenceId, 'translation', $newLang);
    }

    function testSentenceFlagEditionUpdatesFlagsInLinksTable_newDesign() {
        $user = $this->Sentence->Users->get(1);
        CurrentUser::store($user);
        $cmnSentenceId = 2;
        $newLang = 'por';
        $requestData = [
            'id' => $cmnSentenceId,
            'lang' => $newLang,
            'text' => $this->Sentence->get($cmnSentenceId)->text
        ];

        $this->Sentence->editSentence($requestData);

        $this->assertLinksLanguage($cmnSentenceId, 'sentence',    $newLang);
        $this->assertLinksLanguage($cmnSentenceId, 'translation', $newLang);
    }

    function testSentenceFlagEditionGeneratesTranscriptions() {
        $user = $this->Sentence->Users->get(1);
        CurrentUser::store($user);
        $engSentenceId = 1;

        $this->Sentence->changeLanguage($engSentenceId, 'jpn');

        $nbTranscr = $this->Sentence->Transcriptions->find()
            ->where(['sentence_id' => $engSentenceId])
            ->count();
        $this->assertTrue($nbTranscr > 0);
    }

    function testSentenceFlagEditionDeletesTranscriptions() {
        $user = $this->Sentence->Users->get(1);
        CurrentUser::store($user);
        $jpnSentenceId = 6;

        $this->Sentence->changeLanguage($jpnSentenceId, 'deu');

        $nbTranscr = $this->Sentence->Transcriptions->find()
            ->where(['sentence_id' => $jpnSentenceId])
            ->count();
        $this->assertTrue($nbTranscr == 0);
    }

    function testSentenceUnadoptionDoesntTouchTranscriptions() {
        $jpnSentenceId = 6;
        $jpnSentenceOwner = 7;
        $conditions = array('sentence_id' => $jpnSentenceId);
        $transcrBefore = $this->Sentence->Transcriptions->find('all')
            ->where($conditions)
            ->select(['id', 'script', 'text', 'user_id', 'needsReview'])
            ->toList();
        $this->Sentence->unsetOwner($jpnSentenceId, $jpnSentenceOwner);

        $transcrAfter = $this->Sentence->Transcriptions->find('all')
            ->where($conditions)
            ->select(['id', 'script', 'text', 'user_id', 'needsReview'])
            ->toList();
        $this->assertEquals($transcrBefore, $transcrAfter);
    }

    function testSentenceDeletionDeletesTranscriptions() {
        $jpnSentence = $this->Sentence->get(6);
        $this->Sentence->delete($jpnSentence);

        $transcr = $this->Sentence->Transcriptions->find('all')
            ->where(['sentence_id' => $jpnSentence->id])
            ->toList();
        $this->assertEquals(array(), $transcr);
    }

    function testGetSentencesLang_returnsLang() {
        $result = $this->Sentence->getSentencesLang(array(3, 4, 8));
        $expectedLangs = array(3 => 'spa', 4 => 'fra', 8 => 'fra');
        $this->assertEquals($expectedLangs, $result);
    }

    function testGetSentencesLang_returnsNullForFlaglessSentences() {
        $result = $this->Sentence->getSentencesLang(array(9));
        $expectedLangs = array(9 => null);
        $this->assertEquals($expectedLangs, $result);
    }

    function testSentenceRemovedOnDelete() {
        $sentence = $this->Sentence->get(1);

        $this->Sentence->delete($sentence);

        try {
            $result = $this->Sentence->get(1);
        } catch (RecordNotFoundException $e) {
            $result = false;
        }
        $this->assertFalse($result);
    }

    function testReturnsTrueOnDelete() {
        $sentence = $this->Sentence->get(1);

        $result = $this->Sentence->delete($sentence);

        $this->assertTrue($result);
    }

    function testReturnsFalseIfAudioOnDelete() {
        $sentence = $this->Sentence->get(3);

        $result = $this->Sentence->delete($sentence);

        $this->assertFalse($result);
    }

    function testTranslationLinksFromSentenceRemovedOnDelete() {
        $sentenceId = 1;
        $sentence = $this->Sentence->get($sentenceId);

        $this->Sentence->delete($sentence);

        $trans = $this->Sentence->Links->findDirectTranslationsIds($sentenceId);
        $this->assertEquals(array(), $trans);
    }

    function testLogsSentenceDeletionOnDelete() {
        $sentence = $this->Sentence->get(1);
        $conditions = array('type' => 'sentence');
        $before = $this->Sentence->Contributions->find()
            ->where($conditions)
            ->count();

        $this->Sentence->delete($sentence);

        $after = $this->Sentence->Contributions->find()
            ->where($conditions)
            ->count();
        $added = $after - $before;
        $this->assertEquals(1, $added);
    }

    function testLogsSentenceDeletionWithFieldsOnDelete() {
        $sentenceId = 1;
        $sentence = $this->Sentence->get($sentenceId);
        $expected = array(
            'sentence_id' => $sentenceId,
            'sentence_lang' => $sentence->lang,
            'text' => $sentence->text,
            'action' => 'delete',
        );
        $fields = array('sentence_id', 'sentence_lang', 'text', 'action');
        $conditions = array('type' => 'sentence');
        $before = $this->Sentence->Contributions->deleteAll(['id >' => 0]);

        $this->Sentence->delete($sentence);

        $log = $this->Sentence->Contributions->find()
                    ->where($conditions)
                    ->select($fields)
                    ->disableHydration()
                    ->first();
        $this->assertEquals($expected, $log);
    }

    function testLogsLinkDeletionOnDelete() {
        $sentence = $this->Sentence->get(5);
        $conditions = array('type' => 'link');
        $before = $this->Sentence->Contributions->find('all')
            ->where($conditions)
            ->count();

        $this->Sentence->delete($sentence);

        $after = $this->Sentence->Contributions->find('all')
            ->where($conditions)
            ->count();
        $added = $after - $before;
        $this->assertEquals(2, $added);
    }

    function testLogsLinkDeletionWithFieldsOnDelete() {
        $sentenceId = 1;
        $sentence = $this->Sentence->get($sentenceId);
        $expected = array(
            array('Contribution' => array(
                'sentence_id' => $sentenceId,
                'translation_id' => 2,
            )),
            array('Contribution' => array(
                'sentence_id' => 2,
                'translation_id' => $sentenceId,
            )),
            array('Contribution' => array(
                'sentence_id' => $sentenceId,
                'translation_id' => 3,
            )),
            array('Contribution' => array(
                'sentence_id' => 3,
                'translation_id' => $sentenceId,
            )),
            array('Contribution' => array(
                'sentence_id' => $sentenceId,
                'translation_id' => 4,
            )),
            array('Contribution' => array(
                'sentence_id' => 4,
                'translation_id' => $sentenceId,
            )),
        );
        $conditions = array('type' => 'link');
        $contain = array();
        $fields = array('sentence_id', 'translation_id');
        $before = $this->Sentence->Contributions->deleteAll(['id >' => 0]);

        $this->Sentence->delete($sentence);

        $logs = $this->Sentence->Contributions->find('all')
            ->where($conditions)
            ->select($fields)
            ->toList();

        $result = [];
        foreach ($logs as $log) {
            $result[] = ['Contribution' => [
                'sentence_id' => $log->sentence_id,
                'translation_id' => $log->translation_id
            ]];
        }

        $this->assertEquals($expected, $result);
    }

    function testTranslationLinksToSentenceRemovedOnDelete() {
        $sentenceId = 1;
        $sentence = $this->Sentence->get($sentenceId);
        $translations = $this->Sentence->Links->findDirectTranslationsIds($sentenceId);

        $this->Sentence->delete($sentence);

        foreach($translations as $transId) {
            $trans = $this->Sentence->Links->findDirectTranslationsIds($transId);
            $this->assertFalse(in_array($sentenceId, $trans));
        }
    }

    function testLanguageCountDecrementedOnDelete() {
        $sentenceId = 1;
        $sentence = $this->Sentence->get($sentenceId);
        $language = $this->Sentence->Languages->findByCode($sentence->lang)->first();
        $countBefore = $language->sentences;

        $this->Sentence->delete($sentence);

        $language = $this->Sentence->Languages->findByCode($sentence->lang)->first();
        $countAfter = $language->sentences;
        $delta = $countAfter - $countBefore;
        $this->assertEquals(-1, $delta);
    }

    function testListsCleanedOnDelete() {
        $sentenceId = 8;
        $sentence = $this->Sentence->get($sentenceId);
        $inListBefore = $this->Sentence->SentencesLists->SentencesSentencesLists
            ->findAllBySentenceId($sentenceId)
            ->count();

        $this->Sentence->delete($sentence);

        $inListAfter = $this->Sentence->SentencesLists->SentencesSentencesLists
            ->findAllBySentenceId($sentenceId)
            ->count();
        $delta = $inListAfter - $inListBefore;
        $this->assertEquals(-1, $delta);
    }

    function testTagsAreRemovedOnDelete() {
        $sentenceId = 8;
        $sentence = $this->Sentence->get($sentenceId);
        $tagsBefore = $this->Sentence->TagsSentences->getAllTagsOnSentence($sentenceId);

        $this->Sentence->delete($sentence);

        $tagsAfter = $this->Sentence->TagsSentences->getAllTagsOnSentence($sentenceId);
        $this->assertNotEquals(0, count($tagsBefore));
        $this->assertEquals(0, count($tagsAfter));
    }

    function testScriptIsSetOnSentenceCreation() {
        $cmnSentence = $this->Sentence->newEntity([
            'lang' => 'cmn',
            'text' => '我们试试看！',
        ]);

        $savedSentence = $this->Sentence->save($cmnSentence);

        $this->assertEquals('Hans', $savedSentence->script);
    }

    function testScriptIsNotSetOnSentenceCreation() {
        $cmnSentence = $this->Sentence->newEntity([
            'lang' => 'eng',
            'text' => 'Who needs to specify script in English?',
        ]);

        $savedSentence = $this->Sentence->save($cmnSentence);

        $this->assertNull($savedSentence->script);
    }

    function testScriptShouldBeValidOnUpdate() {
        $cmnSentence = $this->Sentence->newEntity([
            'id' => 2,
            'script' => 'invalid script code!',
        ]);

        $result = $this->Sentence->save($cmnSentence);
        $this->assertFalse($result);
    }

    function testScriptShouldBeValidOnCreate() {
        $cmnSentence = $this->Sentence->newEntity([
            'script' => 'invalid script code!',
            'lang' => 'cmn',
            'text' => '我们试试看！',
        ]);

        $result = $this->Sentence->save($cmnSentence);
        $this->assertFalse($result);
    }

    function testScriptShouldBeValidAndCheckType() {
        $cmnSentence = $this->Sentence->newEntity([
            'script' => true,
            'lang' => 'cmn',
            'text' => '我们试试看！',
        ]);

        $result = $this->Sentence->save($cmnSentence);
        $this->assertFalse($result);
    }

    function testNeedsReindex() {
        $reindex = array(2, 3);
        $this->Sentence->needsReindex($reindex);
        $result = $this->Sentence->ReindexFlags->find('all')
            ->where(['sentence_id' => $reindex], ['sentence_id' => 'integer[]']);
        $this->assertEquals(2, $result->count());
        $this->assertTrue($result->every(function ($entity) {
            return $entity->type == 'change' && $entity->indexed === false;
        }));
    }

    function testNeedsReindex_ExcludeUnknownLanguage() {
        $ids = [6, 7, 9, 57];
        $this->Sentence->needsReindex($ids);
        $result = $this->Sentence->ReindexFlags->find('all')
            ->where(['sentence_id' => $ids], ['sentence_id' => 'integer[]'])
            ->select('sentence_id')
            ->disableHydration()
            ->toList();
        $this->assertNotContains(9, $result);
        $this->assertCount(3, $result);
    }

    function testModifiedSentenceNeedsReindex() {
        $id = 1;
        $sentence = $this->Sentence->get($id);
        $sentence->text = 'Changed!';
        $this->Sentence->save($sentence);
        $result = $this->Sentence->ReindexFlags->findBySentenceId($id)->first();
        $this->assertEquals('change', $result->type);
    }

    function testModifiedSentenceInUnknownDoesNotNeedReindex() {
        $id = 9;
        $sentence = $this->Sentence->get($id);
        $sentence->text = 'Changed!';
        $result = $this->Sentence->save($sentence);
        $this->assertTrue((bool)$result);
        $result = $this->Sentence->ReindexFlags->findBySentenceId($id)->first();
        $this->assertNull($result);
    }

    function testModifiedSentenceNeedsTranslationsReindex() {
        $expected = array(1, 2, 4, 5);
        $sentence = $this->Sentence->get(5);
        $sentence->user_id = 0;
        $this->Sentence->save($sentence);
        $result = $this->Sentence->ReindexFlags->find('all')
            ->order(['sentence_id']);
        $ids = $result->extract('sentence_id')->toList();
        $this->assertEquals($expected, $ids);
        $counts = $result->countBy('type')->toArray();
        $this->assertEquals(4, $counts['change']);
    }

    function testRemovedSentenceNeedsItselfAndTranslationsReindex() {
        $expected = array(1, 2, 4, 5);
        $sentence = $this->Sentence->get(5);
        $this->Sentence->delete($sentence);
        $result = $this->Sentence->ReindexFlags->find('all')
            ->order(['sentence_id']);
        $ids = $result->extract('sentence_id')->toList();
        $this->assertEquals($expected, $ids);
        $types = $result->groupBy('type')->toArray();
        $this->assertCount(1, $types['removal']);
        $this->assertCount(3, $types['change']);
    }

    function testSentenceLoosesOKTagOnEdition() {
        $sentenceId = 2;
        $OKTagId = $this->Sentence->Tags->getIdFromName(
            $this->Sentence->Tags->getOKTagName()
        );
        $this->assertTrue(
            $this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
        );
        $sentence = $this->Sentence->newEntity([
            'id' => $sentenceId,
            'lang' => 'cmn',
            'text' => "That should remove the OK tag automatically!"
        ]);
        $this->Sentence->save($sentence);
        $this->assertFalse(
            $this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
        );
    }

    function testSentenceDontLoosesOKTagOnFlagChange() {
        $user = $this->Sentence->Users->get(1);
        CurrentUser::store($user);
        $sentenceId = 2;
        $OKTagId = $this->Sentence->Tags->getIdFromName(
            $this->Sentence->Tags->getOKTagName()
        );
        $this->assertTrue(
            $this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
        );

        $this->Sentence->changeLanguage($sentenceId, 'ita');

        $this->assertTrue(
            $this->Sentence->TagsSentences->isSentenceTagged($sentenceId, $OKTagId)
        );
    }

    function testSphinxAttributesChanged_onLetGo() {
        $sentenceId = 1;
        $expectedAttributes = array('user_id');
        $expectedValues = array(
            $sentenceId => array(0),
        );
        $entity = $this->Sentence->get($sentenceId);
        $entity->user_id = null;

        $this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

        $this->assertEquals($expectedAttributes, $attributes);
        $this->assertEquals($expectedValues, $values);
    }

    function testSphinxAttributesChanged_onOwn() {
        $sentenceId = 1;
        $ownerId = 42;
        $expectedAttributes = array('user_id');
        $expectedValues = array(
            $sentenceId => array($ownerId),
        );
        $entity = $this->Sentence->get($sentenceId);
        $entity->user_id = $ownerId;

        $this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

        $this->assertEquals($expectedAttributes, $attributes);
        $this->assertEquals($expectedValues, $values);
    }

    function testSphinxAttributesChanged_correctness() {
        $sentenceId = 1;
        $correctness = -1;
        $expectedAttributes = array('ucorrectness');
        $expectedValues = array(
            $sentenceId => array($correctness + 128),
        );
        $entity = $this->Sentence->get($sentenceId);
        $entity->correctness = $correctness;

        $this->Sentence->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

        $this->assertEquals($expectedAttributes, $attributes);
        $this->assertEquals($expectedValues, $values);
    }

    function testEditSentence_succeedsForSentenceOwner() {
        $user = $this->Sentence->Users->get(4);
        CurrentUser::store($user);
        $this->editSentenceWithSuccess();
    }

    function testEditSentence_succeedsForCorpusMaintainer() {
        $user = $this->Sentence->Users->get(2);
        CurrentUser::store($user);
        $this->editSentenceWithSuccess();
    }

    function editSentenceWithSuccess() {
        $before = $this->Sentence->get(53);
        $data = array(
            'id' => '53',
            'lang' => 'eng',
            'text' => 'Edited sentence.'
        );
        $sentence = $this->Sentence->editSentence($data);

        $this->assertArraySubset($data, $sentence->toArray());

        $after = $this->Sentence->get(53);
        $this->assertNotEquals($before->text, $after->text);
        $this->assertNotEquals($before->modified, $after->modified);
    }

    function testEditSentence_succeedsWhenLangEmtpy() {
        $user = $this->Sentence->Users->get(2);
        CurrentUser::store($user);

        $data = array(
            'id' => '53',
            'lang' => '',
            'text' => 'Sentence with unknown lang.'
        );
        $sentence = $this->Sentence->editSentence($data);

        $this->assertArraySubset($data, $sentence->toArray());
    }

    function testEditSentence_failsBecauseHasAudio() {
        $user = $this->Sentence->Users->get(7);
        CurrentUser::store($user);

        $data = array(
            'id' => '3',
            'lang' => 'spa',
            'text' => 'changing'
        );
        $expected = $this->Sentence->get(3);
        $result = $this->Sentence->editSentence($data);

        $this->assertEquals($expected, $result);
    }

    function testEditSentence_failsBecauseNotOwner() {
        $user = $this->Sentence->Users->get(4);
        CurrentUser::store($user);

        $data = array(
            'id' => '1',
            'lang' => 'eng',
            'text' => 'Edited sentence.'
        );
        $before = $this->Sentence->get(1);

        $result = $this->Sentence->editSentence($data);
        $this->assertNotFalse($result);

        $after = $this->Sentence->get(1);
        $this->assertEquals($before, $after);
    }

    function testEditSentence_failsBecauseWrongId() {
        $user = $this->Sentence->Users->get(4);
        CurrentUser::store($user);

        $data = array(
            'id' => 'eng',
            'lang' => '53',
            'text' => 'Edited sentence.'
        );
        $result = $this->Sentence->editSentence($data);

        $this->assertEmpty($result);
    }

    function testEditSentence_languageChangeUpdatesReindexFlags() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $data = [
            'id' => '7',
            'lang' => 'ita',
            'text' => 'This is a lonely sentence.'
        ];

        $result = $this->Sentence->editSentence($data);
        $entries = $this->Sentence->ReindexFlags->findBySentenceId($result->id)
                ->select(['lang', 'type'])
                ->disableHydration()
                ->toArray();
        $this->assertContains(['lang' => 'eng', 'type' => 'removal'], $entries);
        $this->assertContains(['lang' => 'ita', 'type' => 'change'], $entries);
    }

    function testEditSentence_noEntryInReindexFlagsForUnknownPreviousLanguage() {
        CurrentUser::store($this->Sentence->Users->get(3));
        $data = [
            'id' => '9',
            'lang' => 'eng',
            'text' => 'This sentence purposely misses its flag.'
        ];

        $sentence = $this->Sentence->editSentence($data);
        $this->assertTrue((bool)$sentence);
        $row = $this->Sentence->ReindexFlags->findBySentenceId($sentence->id)
            ->where(['type' => 'removal'])
            ->first();
        $this->assertNull($row);
    }

    function testEditSentence_noEntryInReindexFlagsForUnknownNewLanguage() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $data = [
            'id' => '7',
            'lang' => '',
            'text' => 'This is a lonely sentence.'
        ];
        $sentence = $this->Sentence->editSentence($data);
        $this->assertTrue((bool)$sentence);
        $row = $this->Sentence->ReindexFlags->findBySentenceId($sentence->id)
            ->where(['type' => 'change'])
            ->first();
        $this->assertNull($row);
    }

    function testEditSentence_onlyTextAndLangAreEditable() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $before = $this->Sentence->get(7);
        $data = [
            'id' => '7',
            'text' => 'New text.',
            'lang' => 'por',
            'created' => '2020-10-01 12:34:56',
            'user_id' => '1',
        ];
        $after = $this->Sentence->editSentence($data);
        $this->assertEquals($before->created, $after->created);
        $this->assertEquals($before->user_id, $after->user_id);
    }

    function testEditSentence_onlyNewText() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $before = $this->Sentence->get(7);
        $data = ['id' => '7', 'text' => 'New text.'];
        $after = $this->Sentence->editSentence($data);
        $this->assertNotEquals($before->text, $after->text);
        $this->assertEquals($before->lang, $after->lang);
    }

    function testEditSentence_onlyNewLang() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $before = $this->Sentence->get(7);
        $data = ['id' => '7', 'lang' => 'fra'];
        $after = $this->Sentence->editSentence($data);
        $this->assertEquals($before->text, $after->text);
        $this->assertNotEquals($before->lang, $after->lang);
    }

    function testEditSentence_onlyIdGiven() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $before = $this->Sentence->get(2);
        $data = ['id' => '2'];
        $after = $this->Sentence->editSentence($data);
        $this->assertEquals($before, $after);
        $this->assertFalse($this->Sentence->UsersSentences->get(1)->dirty);
    }

    function testDeleteSentence_succeedsBecauseIsOwnerAndHasNoTranslations()
    {
        $user = $this->Sentence->Users->get(4);
        CurrentUser::store($user);
        $this->deleteSentenceWithSuccess(53);
    }

    function testDeleteSentence_succeedsBecauseIsCorpusMaintainer()
    {
        $user = $this->Sentence->Users->get(2);
        CurrentUser::store($user);
        $this->deleteSentenceWithSuccess(53);
    }

    function testDeleteSentence_succeedsBecauseIsAdmin()
    {
        $user = $this->Sentence->Users->get(1);
        CurrentUser::store($user);
        $this->deleteSentenceWithSuccess(53);
    }

    function deleteSentenceWithSuccess($id) {
        $result = $this->Sentence->deleteSentence($id);
        $this->assertTrue($result);

        $count = $this->Sentence->find()->where(['id' => $id])->count();
        $this->assertEquals(0, $count);
    }

    function testDeleteSentence_failsIfNotOwnerAndHasNoTranslations()
    {
        $user = $this->Sentence->Users->get(4);
        CurrentUser::store($user);
        $this->deleteSentenceWithFailure(52);
    }

    function testDeleteSentence_failsIfOwnerAndHasTranslations()
    {
        $this->beOwnerOfCurrentSentence(1);
        $this->deleteSentenceWithFailure(1);
    }

    function deleteSentenceWithFailure($id) {
        $result = $this->Sentence->deleteSentence($id);
        $this->assertFalse($result);

        $count = $this->Sentence->find()->where(['id' => $id])->count();
        $this->assertEquals(1, $count);
    }

    function testDeleteSentence_ListsNumberOfSentencesIsCorrect() {
        $sentenceId = 8;
        $sentencesLists = $this->Sentence->get($sentenceId, ['contain' => ['SentencesLists']])->sentences_lists;
        $idsAndNumbers = array_combine(array_column($sentencesLists, 'id'), array_column($sentencesLists, 'numberOfSentences'));

        $user = $this->Sentence->Users->get(1); // Admin
        CurrentUser::store($user);
        $this->Sentence->deleteSentence($sentenceId);

        foreach ($idsAndNumbers as $id => $oldNumberOfSentences) {
            $newNumberOfSentences = $this->Sentence->SentencesLists->get($id)->numberOfSentences;
            $this->assertEquals($oldNumberOfSentences - 1, $newNumberOfSentences);
        }
    }

    function testDeleteSentence_EntryInReindexFlags() {
        CurrentUser::store($this->Sentence->Users->get(1));
        $id = 1;
        $this->Sentence->deleteSentence($id);
        $result = $this->Sentence->ReindexFlags->findBySentenceId($id)->first();
        $expected = [
            'sentence_id' => 1,
            'lang' => 'eng',
            'indexed' => false,
            'type' => 'removal'
        ];
        $this->assertArraySubset($expected, $result->toArray());
    }

    function testDeleteSentene_NoEntryInReindexFlagsForUnknownLanguage() {
        CurrentUser::store($this->Sentence->Users->get(1));
        $id = 9;
        $this->Sentence->deleteSentence($id);
        $result = $this->Sentence->ReindexFlags->findBySentenceId($id)->first();
        $this->assertNull($result);
    }

    function testNumberOfSentencesOwnedBy() {
        $result = $this->Sentence->numberOfSentencesOwnedBy(7);
        $this->assertEquals(21, $result);
    }

    function testGetSentenceTextForId_succeeds() {
        $result = $this->Sentence->getSentenceTextForId(1);
        $expected = 'The fundamental cause of the problem is that in the modern world, idiots are full of confidence, while the intelligent are full of doubt.';
        $this->assertEquals($expected, $result);
    }

    function testGetSentenceTextForId_fails() {
        $result = $this->Sentence->getSentenceTextForId(99999999);
        $this->assertEquals('', $result);
    }

    function testGetLanguageCodeFromSentenceId_succeeds() {
        $result = $this->Sentence->getLanguageCodeFromSentenceId(1);
        $this->assertEquals('eng', $result);
    }

    function testGetLanguageCodeFromSentenceId_fails() {
        $result = $this->Sentence->getLanguageCodeFromSentenceId(99999999);
        $this->assertEquals(null, $result);
    }

    function testChangeLanguage_succeeds() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $result = $this->Sentence->changeLanguage(1, 'jpn');
        $this->assertEquals('jpn', $result);
    }

    function testChangeLanguage_succeedsAsCorpusMaintainter() {
        CurrentUser::store($this->Sentence->Users->get(2));
        $result = $this->Sentence->changeLanguage(1, 'jpn');
        $this->assertEquals('jpn', $result);
    }

    function testChangeLanguage_failsBecauseNowAllowed() {
        CurrentUser::store($this->Sentence->Users->get(4));
        $result = $this->Sentence->changeLanguage(1, 'jpn');
        $this->assertEquals('eng', $result);
    }

    function testChangeLanguage_failsBecauseWrongSentenceId() {
        $result = $this->Sentence->changeLanguage(9999999, 'jpn');
        $this->assertFalse($result);
    }

    function testChangeLanguage_failsBecauseAudio() {
        CurrentUser::store($this->Sentence->Users->get(2));
        $result = $this->Sentence->changeLanguage(3, 'eng');
        $this->assertEquals('spa', $result);
    }

    function testChangeLanguage_failsBecauseInvalidLang() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $result = $this->Sentence->changeLanguage(1, '123');
        $this->assertEquals('eng', $result);
    }

    function testChangeLanguage_correctEntriesInReindexFlags() {
        CurrentUser::store($this->Sentence->Users->get(2));
        $this->Sentence->changeLanguage(53, 'rus');
        $changes = $this->Sentence->ReindexFlags->findBySentenceId(53)
            ->select(['lang', 'type'])
            ->disableHydration()
            ->all();
        $this->assertContains(['lang' => 'rus', 'type' => 'change'], $changes);
        $this->assertContains(['lang' => 'eng', 'type' => 'removal'], $changes);
    }

    function testChangeLanguage_noEntryInReindexFlagsForUnknownPreviousLanguage() {
        CurrentUser::store($this->Sentence->Users->get(3));
        $sentenceId = 9;
        $result = $this->Sentence->changeLanguage($sentenceId, 'eng');
        $this->assertEquals('eng', $result);
        $changes = $this->Sentence->ReindexFlags->findBySentenceId($sentenceId)
            ->where(['type' => 'removal'])
            ->first();
        $this->assertNull($changes);
    }

    function testChangeLanguage_noEntryInReindexFlagsForUnknownNewLanguage() {
        CurrentUser::store($this->Sentence->Users->get(7));
        $sentenceId = 8;
        $result = $this->Sentence->changeLanguage($sentenceId, '');
        $this->assertEquals('', $result);
        $changes = $this->Sentence->ReindexFlags->findBySentenceId($sentenceId)
            ->where(['type' => 'change'])
            ->first();
        $this->assertNull($changes);
    }

    function testSetOwner_succeeds() {
        $id = 14;
        $before = $this->Sentence->get($id)->user_id;

        $result = $this->Sentence->setOwner($id, 7, User::ROLE_CONTRIBUTOR);
        $this->assertEquals(7, $result->user_id);

        $after = $this->Sentence->get($id)->user_id;
        $this->assertNotEquals($before, $after);
    }

    function testSetOwner_failsIfNotOrphan() {
        $id = 1;
        $before = $this->Sentence->get($id)->user_id;

        $result = $this->Sentence->setOwner($id, 1, User::ROLE_ADMIN);
        $this->assertNotEquals(1, $result->user_id);

        $after = $this->Sentence->get($id)->user_id;
        $this->assertEquals($before, $after);
    }

    function testUnsetOwner_succeeds() {
        $id = 1;
        $before = $this->Sentence->get($id)->user_id;

        $result = $this->Sentence->unsetOwner($id, 7);
        $this->assertTrue($result);

        $after = $this->Sentence->get($id)->user_id;
        $this->assertNotEquals($before, $after);
    }

    function testUnsetOwner_failsIfNotOwner() {
        $id = 1;
        $before = $this->Sentence->get($id)->user_id;

        $result = $this->Sentence->unsetOwner($id, 1);
        $this->assertFalse($result);

        $after = $this->Sentence->get($id)->user_id;
        $this->assertEquals($before, $after);
    }

    function testGetTranslationsOf() {
        $results = $this->Sentence->getTranslationsOf(1);
        $directTranslationsIds = Hash::extract($results[0], '{n}.id');
        $indirectTranslationsIds = Hash::extract($results[1], '{n}.id');
        $this->assertEquals([2, 4, 3], $directTranslationsIds);
        $this->assertEquals([5, 6], $indirectTranslationsIds);
    }

    function testEditCorrectness() {
        $result = $this->Sentence->editCorrectness(1, -1);
        $this->assertEquals(-1, $result->correctness);
    }

    function testMarkUnreliable_ofSpammer_asAdmin() {
        CurrentUser::store($this->Sentence->Users->get(1));
        $this->Sentence->saveNewSentence('test', 'eng', 6);

        $result = $this->Sentence->markUnreliable('spammer');
        $this->assertNotFalse($result);

        $correctCount = $this->Sentence->find('all')
            ->where(['user_id' => 6, 'correctness' => 0])
            ->count();
        $this->assertEquals(0, $correctCount);
    }

    function testMarkUnreliable_ofSpammer_asContributor() {
        CurrentUser::store($this->Sentence->Users->get(4));
        $result = $this->Sentence->markUnreliable('spammer');
        $this->assertFalse($result);
    }

    function testMarkUnreliable_ofContributor_asAdmin() {
        CurrentUser::store($this->Sentence->Users->get(1));
        $result = $this->Sentence->markUnreliable('contributor');
        $this->assertFalse($result);
    }

    function testGetNeighborsSentenceIds() {
        $result = $this->Sentence->getNeighborsSentenceIds(8, 'fra');
        $expected = [
            'prev' => 4,
            'next' => 12
        ];
        $this->assertEquals($expected, $result);
    }

    function findFilteredTranslationsProvider () {
        // userId, find options, expected result (in alphabetic order)
        return [
            'with lang settings but without translation lang' =>
            [4, [], ['deu', 'fra']],
            'with lang settings and translation lang' =>
            [4, ['translationLang' => 'jpn'], ['jpn']],
            'without lang settings and translation lang' =>
            [null, [], ['cmn', 'deu', 'fra', 'jpn', 'spa']],
            'without lang settings but with translation lang' =>
            [null, ['translationLang' => 'spa'], ['spa']],
        ];
    }

    /**
     * @dataProvider findFilteredTranslationsProvider
     */
    function testFindFilteredTranslations($userId, $findOptions, $expected) {
        if ($userId) {
            $Users = TableRegistry::getTableLocator()->get('Users');
            CurrentUser::store($Users->get($userId));
        } else {
            CurrentUser::store(null);
        }

        $result = $this->Sentence->find('filteredTranslations', $findOptions)
            ->where(['Sentences.id' => 1])
            ->contain($this->Sentence->contain(['translations' => true]))
            ->first();

        $languages = array_unique(Hash::extract($result, 'translations.{n}.{n}.lang'));
        sort($languages);
        $this->assertEquals($expected, $languages);
    }

    function testGetSentenceWith_translationsHaveAudioInfo() {
        CurrentUser::store(null);
        $sentence = $this->Sentence->getSentenceWith(1, ['translations' => true]);
        $result = [];
        foreach($sentence->translations as $translationsGroup) {
            foreach($translationsGroup as $translation) {
                $audios = $translation->audios;
                $result[$translation->id] = isset($audios[0]) ? $audios[0]->author : null;
            }
        }
        $expected = [
            2 => null,
            3 => 'contributor',
            4 => 'Philippe Petit',
            5 => null,
            6 => null,
        ];
        $this->assertEquals($expected, $result);
    }

    function testGetSentenceWith_isHidingUnneededFieldsFromJson() {
        $sentence = $this->Sentence->getSentenceWith(1, ['translations' => true]);
        $sentence = json_decode(json_encode($sentence));
        $this->assertFalse(isset($sentence->user_id));
        $this->assertFalse(isset($sentence->user->id));
        $this->assertFalse(isset($sentence->user->level));
        $this->assertFalse(isset($sentence->user->role));
        $translationAudio = $sentence->translations[0][1]->audios[0];
        $this->assertFalse(isset($translationAudio->sentence_id));
    }

    function testGetSentenceWith_noTranslations() {
        $sentence = $this->Sentence->getSentenceWith(1, ['translations' => false]);
        $expected = [0 => [], 1 => []];
        $this->assertEquals($expected, $sentence->translations);
    }

    function testGetSentenceWith_doesNotReturnUnlistedLists() {
        CurrentUser::store($this->Sentence->Users->get(4));
        $sentence = $this->Sentence->getSentenceWith(4);
        $result = Hash::extract($sentence->sentences_lists, '{n}.id');
        $expected = [];
        $this->assertEquals($expected, $result);
    }

    function testSaveNewSentence_correctDateUsingArabicLocale() {
        $prevLocale = I18n::getLocale();
        I18n::setLocale('ar');

        $added = $this->Sentence->saveNewSentence('test', 'eng', 1);
        $returned = $this->Sentence->get($added->id);
        $this->assertEquals($added->created->format('Y-m-d H:i:s'), $returned->created->format('Y-m-d H:i:s'));
        $this->assertEquals($added->modified->format('Y-m-d H:i:s'), $returned->modified->format('Y-m-d H:i:s'));

        I18n::setLocale($prevLocale);
    }

    function testEditSentence_recognizeDuplicateWithExtraSpaces() {
        $user = $this->Sentence->Users->get(7);
        CurrentUser::store($user);

        $old = $this->Sentence->get(7);
        $data = array(
            'id' => '7',
            'lang' => 'eng',
            'text' => 'This  is  a   lonely sentence. '
        );
        $result = $this->Sentence->editSentence($data);

        $this->assertEquals($old->id, $result->id);
        $this->assertEquals($old->text, $result->text);
    }

    function testSaveNewSentence_replaceControlCharactersWithSpace() {
        $text = "Text\u{a}with\u{1}\u{7f}whitespace\u{a0}and\u{a0}\u{a} control\u{90}characters\u{2009}in between.";
        $expected = "Text with whitespace\u{a0}and control characters\u{2009}in between.";
        $sentence = $this->Sentence->saveNewSentence($text, 'eng', 1);
        $stored = $this->Sentence->get($sentence->id)->text;
        $this->assertEquals($expected, $stored);
    }

    function testSaveTranslation_licenseIsCCBY() {
        // 'contributor', default license: CC BY
        CurrentUser::store($this->Sentence->Users->get(4));
        $translation = $this->Sentence->saveTranslation(1, 'eng', 'Lorem ipsum', 'lat');
        $this->assertEquals('CC BY 2.0 FR', $translation->license);

        // 'kazuki', default license: CC0
        CurrentUser::store($this->Sentence->Users->get(7));
        $translation = $this->Sentence->saveTranslation(2, 'cmn', 'Lorem ipsum', 'lat');
        $this->assertEquals('CC BY 2.0 FR', $translation->license);
    }

    function testLanguagesHavingSentences() {
        $expected = [
            null, 'ara', 'cmn', 'deu', 'eng', 'fra', 'jpn', 'pol', 'rus',
            'spa', 'tur', 'ukr', 'wuu', 'yue'
        ];
        $result = $this->Sentence->languagesHavingSentences();
        $this->assertEquals($expected, $result);
    }

    function testGetSeveralRandomIds() {
        $expected = [ 20, 19, 18, 17, 16, 5, 4, 3, 2, 1 ];

        $result = $this->Sentence->getSeveralRandomIds('nch');

        $this->assertEquals($expected, $result);
    }

    public function testNewSentence_UpdatesLastContributionField() {
        $user = $this->Sentence->Users->get(1);
        CurrentUser::store($user);
        
        Time::setTestNow(new Time('2019-02-01 00:00:00')); 
        $this->Sentence->saveNewSentence('This is my new English sentence.', 'eng', 1);
        $user = $this->Sentence->Users->get(1);
        $previousLastContribution = $user->last_contribution;

        Time::setTestNow(new Time('2019-02-02 00:00:00')); 
        $this->Sentence->saveNewSentence('This is my newer English sentence.', 'eng', 1);
        $user = $this->Sentence->Users->get(1);
        $newLastContribution = $user->last_contribution;

        $this->assertGreaterThan($previousLastContribution, $newLastContribution);
    }

    public function testEditSentence_UpdatesLastContributionField() {
        $user = $this->Sentence->Users->get(7);
        CurrentUser::store($user);
        
        Time::setTestNow(new Time('2019-02-01 00:00:00')); 
        $this->Sentence->saveNewSentence('This is my new English sentence.', 'eng', 1);
        $user = $this->Sentence->Users->get(7);
        $previousLastContribution = $user->last_contribution;

        Time::setTestNow(new Time('2019-02-02 00:00:00')); 
        $before = $this->Sentence->get(7);
        $data = ['id' => '7', 'text' => 'This is the new text of sentence #7.'];
        $after = $this->Sentence->editSentence($data);
        $user = $this->Sentence->Users->get(7);
        $newLastContribution = $user->last_contribution;

        $this->assertGreaterThan($previousLastContribution, $newLastContribution);
    }
}
