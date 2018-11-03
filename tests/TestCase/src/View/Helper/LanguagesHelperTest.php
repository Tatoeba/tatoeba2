<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\LanguagesHelper;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class LanguagesHelperTest extends TestCase {
	public $fixtures = array(
		'app.users_languages'
	);

	function setUp() {
        parent::setUp();
        $Controller = new Controller();
        $View = new View($Controller);
        $this->Languages = new LanguagesHelper($View);
		Configure::write('Config.language', 'eng');
		CurrentUser::store(null);
	}

	function tearDown() {
		parent::tearDown();
		unset($this->Languages);
		ClassRegistry::flush();
	}

	function _beRegularUser() {
		$admin = array(
			'id' => 7,
			'group_id' => 4,
			'settings' => array(
				'lang' => 'jpn,epo,ara,deu'
			)
		);
		CurrentUser::store($admin);
	}

	function testUserLanguagesArray_returnsManyManyLanguages() {
		$result = $this->Languages->userLanguagesArray();
		$this->assertTrue(count($result) > 100);
	}

	function testUserLanguagesArray_returnsPreferredLanguagesIfAny() {
		$this->_beRegularUser();
		$expectedUserLanguages = array('jpn', 'epo', 'ara', 'deu');

		$result = $this->Languages->userLanguagesArray();
		$result = array_keys($result);

		$this->assertEqual($expectedUserLanguages, $result);
	}

	function testTranslationsArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->translationsArray();
		$this->assertEqual('Japanese', $result['jpn']);
	}

	function testTranslationsArray_returnsLocalizedLangagesNames() {
		Configure::write('Config.language', 'jpn');
		$result = $this->Languages->translationsArray();
		$this->assertEqual('日本語', $result['jpn']);
	}

	function testLanguagesArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->languagesArrayAlone();
		$this->assertEqual('Japanese', $result['jpn']);
	}

	function testLanguagesArray_returnsLocalizedLangagesNames() {
		Configure::write('Config.language', 'jpn');
		$result = $this->Languages->languagesArrayAlone();
		$this->assertEqual('日本語', $result['jpn']);
	}

	function testUnknownLanguagesArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->unknownLanguagesArray();
		$this->assertEqual('Japanese', $result['jpn']);
	}

	function testUnknownLanguagesArray_returnsLocalizedLangagesNames() {
		Configure::write('Config.language', 'jpn');
		$result = $this->Languages->unknownLanguagesArray();
		$this->assertEqual('日本語', $result['jpn']);
	}

	function testOtherLanguagesArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->otherLanguagesArray();
		$this->assertEqual('Japanese', $result['jpn']);
	}

	function testOtherLanguagesArray_returnsLocalizedLangagesNames() {
		Configure::write('Config.language', 'jpn');
		$result = $this->Languages->otherLanguagesArray();
		$this->assertEqual('日本語', $result['jpn']);
	}

	function testLanguagesArrayForLists_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->languagesArrayForPositiveLists();
		$this->assertEqual('Japanese', $result['jpn']);
	}

	function testLanguagesArrayForLists_returnsLocalizedLangagesNames() {
		Configure::write('Config.language', 'jpn');
		$result = $this->Languages->languagesArrayForPositiveLists();
		$this->assertEqual('日本語', $result['jpn']);
	}

	function testLanguagesArrayWithNone_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->languagesArrayWithNone();
		$this->assertEqual('Japanese', $result['jpn']);
	}

	function testLanguagesArrayWithNone_returnsLocalizedLangagesNames() {
		Configure::write('Config.language', 'jpn');
		$result = $this->Languages->languagesArrayWithNone();
		$this->assertEqual('日本語', $result['jpn']);
	}

	function testGetSearchableLanguagesArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->getSearchableLanguagesArray();
		$this->assertEqual('Japanese', $result['jpn']);
	}

	function testGetSearchableLanguagesArray_returnsLocalizedLangagesNames() {
		Configure::write('Config.language', 'jpn');
		$result = $this->Languages->getSearchableLanguagesArray();
		$this->assertEqual('日本語', $result['jpn']);
	}

	function testCodeToName_returnsUnlocalizedName() {
		$result = $this->Languages->codeToNameAlone('jpn');
		$this->assertEqual('Japanese', $result);
	}

	function testCodeToName_returnsLocalizedName() {
		Configure::write('Config.language', 'jpn');
		$result = $this->Languages->codeToNameAlone('jpn');
		$this->assertEqual('日本語', $result);
	}

	function testCodeToName_returnsUnknownForUndefinedLanguage() {
		$result = $this->Languages->codeToNameAlone('und');
		$this->assertEqual('unknown', $result);
	}
}
