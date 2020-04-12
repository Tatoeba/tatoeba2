<?php
namespace App\Test\TestCase\View\Helper;

use App\Model\CurrentUser;
use App\View\Helper\LanguagesHelper;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\I18n\I18n;

class LanguagesHelperTest extends TestCase {
	public $fixtures = array(
		'app.users_languages'
	);

	function setUp() {
		parent::setUp();
        $View = new View();
		$this->Languages = new LanguagesHelper($View);
		I18N::setLocale('en');
		CurrentUser::store(null);
	}

	function tearDown() {
		I18N::setLocale('en');
	}

	function _beRegularUser() {
		$admin = array(
			'id' => 7,
			'role' => \App\Model\Entity\User::ROLE_CONTRIBUTOR,
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

		$this->assertEquals($expectedUserLanguages, $result);
	}

	function testTranslationsArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->translationsArray();
		$this->assertEquals('Japanese', $result['jpn']);
	}

	function testTranslationsArray_returnsLocalizedLangagesNames() {
		I18N::setLocale('ja');
		$result = $this->Languages->translationsArray();
		$this->assertEquals('日本語', $result['jpn']);
	}

	function testLanguagesArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->languagesArrayAlone();
		$this->assertEquals('Japanese', $result['jpn']);
	}

	function testLanguagesArray_returnsLocalizedLangagesNames() {
		I18N::setLocale('ja');
		$result = $this->Languages->languagesArrayAlone();
		$this->assertEquals('日本語', $result['jpn']);
	}

	function testUnknownLanguagesArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->unknownLanguagesArray();
		$this->assertEquals('Japanese', $result['jpn']);
	}

	function testUnknownLanguagesArray_returnsLocalizedLangagesNames() {
		I18N::setLocale('ja');
		$result = $this->Languages->unknownLanguagesArray();
		$this->assertEquals('日本語', $result['jpn']);
	}

	function testOtherLanguagesArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->otherLanguagesArray();
		$this->assertEquals('Japanese', $result['jpn']);
	}

	function testOtherLanguagesArray_returnsLocalizedLangagesNames() {
		I18N::setLocale('ja');
		$result = $this->Languages->otherLanguagesArray();
		$this->assertEquals('日本語', $result['jpn']);
	}

	function testLanguagesArrayForLists_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->languagesArrayForPositiveLists();
		$this->assertEquals('Japanese', $result['jpn']);
	}

	function testLanguagesArrayForLists_returnsLocalizedLangagesNames() {
		I18N::setLocale('ja');
		$result = $this->Languages->languagesArrayForPositiveLists();
		$this->assertEquals('日本語', $result['jpn']);
	}

	function testLanguagesArrayWithNone_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->languagesArrayWithNone();
		$this->assertEquals('Japanese', $result['jpn']);
	}

	function testLanguagesArrayWithNone_returnsLocalizedLangagesNames() {
		I18N::setLocale('ja');
		$result = $this->Languages->languagesArrayWithNone();
		$this->assertEquals('日本語', $result['jpn']);
	}

	function testGetSearchableLanguagesArray_returnsUnlocalizedLangagesNames() {
		$result = $this->Languages->getSearchableLanguagesArray();
		$this->assertEquals('Japanese', $result['jpn']);
	}

	function testGetSearchableLanguagesArray_returnsLocalizedLangagesNames() {
		I18N::setLocale('ja');
		$result = $this->Languages->getSearchableLanguagesArray();
		$this->assertEquals('日本語', $result['jpn']);
	}

	function testCodeToName_returnsUnlocalizedName() {
		$result = $this->Languages->codeToNameAlone('jpn');
		$this->assertEquals('Japanese', $result);
	}

	function testCodeToName_returnsLocalizedName() {
		I18N::setLocale('ja');
		$result = $this->Languages->codeToNameAlone('jpn');
		$this->assertEquals('日本語', $result);
	}
}
