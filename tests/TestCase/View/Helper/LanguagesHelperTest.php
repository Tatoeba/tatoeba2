<?php
namespace App\Test\TestCase\View\Helper;

use App\Model\CurrentUser;
use App\View\Helper\LanguagesHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Cake\I18n\I18n;

class LanguagesHelperTest extends TestCase {
	public $fixtures = array(
		'app.users_languages'
	);

	private $prevLocale;

	function setUp() {
		parent::setUp();
        $View = new View();
		$this->Languages = new LanguagesHelper($View);
		$this->prevLocale = I18n::getLocale();
		CurrentUser::store(null);
	}

	function tearDown() {
		I18n::setLocale($this->prevLocale);
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
		$result = $this->Languages->languagesArrayShowTranslationsIn();
		$this->assertEquals('Japanese', $result['jpn']);
	}

	function testLanguagesArrayForLists_returnsLocalizedLangagesNames() {
		I18N::setLocale('ja');
		$result = $this->Languages->languagesArrayShowTranslationsIn();
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

    function testGetInterfaceLanguage() {
        $oldLang = I18n::getLocale();
        I18n::setLocale('en');
        $this->assertEquals('English', $this->Languages->getInterfaceLanguage());
        I18n::setLocale('fr');
        $this->assertEquals('Français', $this->Languages->getInterfaceLanguage());
        I18n::setLocale($oldLang);
    }
}
