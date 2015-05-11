<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2015  Gilles Bedel

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
App::import('Core', 'PluralForms');

/**
 * PluralFormsTest class
 *
 * @package       Cake.Test.Case.I18n
 */
class PluralFormsTest extends CakeTestCase {

/**
 * This function sets up a PluralForms
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PluralForms = new PluralForms();
	}

	public $lastError;

	public function __error_handler($errno, $errstr) {
		$this->lastError = $errstr;
	}

	private function expectErrorTriggered() {
		$this->lastError = null;
		set_error_handler(array($this, '__error_handler'));
	}

	private function assertErrorTriggered($contained) {
		restore_error_handler();
		$this->assertTrue(strstr($this->lastError, $contained));
	}

/**
 * testAllOperators
 *
 * @return void
 */
	public function testAllOperators() {
		$formula = $this->PluralForms->parsePluralForms('nplurals=2; plural=('.
			'(n == 1 || n != 0) && (n > 0 || n < 2) && (n >= 1 || n <= 1) && (n % 2) ? 0 : 1'.
		')');
		$this->assertEqual(1, $this->PluralForms->getPlural($formula, 0));
		$this->assertEqual(0, $this->PluralForms->getPlural($formula, 1));
	}

/**
 * testOperatorsPrecedence
 *
 * @return void
 */
	public function testOperatorsPrecedence() {
		$formula = $this->PluralForms->parsePluralForms('nplurals=2; plural='.
			'0 ? 0 : 0 || 1 && 0 != 1 == 1 >= 1 <= 1 < 2 > 0 % 2'
		);
		$this->assertEqual(1, $this->PluralForms->getPlural($formula, 0));
	}

/**
 * testTwoForms
 *
 * @return void
 */
	public function testTwoForms() {
		$formula = $this->PluralForms->parsePluralForms('nplurals=2; plural=(n != 1)');
		$this->assertEqual(1, $this->PluralForms->getPlural($formula, 0));
		$this->assertEqual(0, $this->PluralForms->getPlural($formula, 1));
		$this->assertEqual(1, $this->PluralForms->getPlural($formula, 2));
		$this->assertEqual(1, $this->PluralForms->getPlural($formula, 3));
	}

/**
 * testThreeForms
 *
 * @return void
 */
	public function testThreeForms() {
		$formula = $this->PluralForms->parsePluralForms('nplurals=3; plural=(n == 0 ? 0 : n > 1 ? 2 : 1');
		$this->assertEqual(0, $this->PluralForms->getPlural($formula, 0));
		$this->assertEqual(1, $this->PluralForms->getPlural($formula, 1));
		$this->assertEqual(2, $this->PluralForms->getPlural($formula, 2));
		$this->assertEqual(2, $this->PluralForms->getPlural($formula, 3));
	}

/**
 * testNoSemicolonInHeader
 *
 * @return void
 */
	public function testNoSemicolonInHeader() {
		$this->expectErrorTriggered();
		$pluralForm = 'nplurals=2 plural=(n != 1)';
		$formula = $this->PluralForms->parsePluralForms($pluralForm);
		$this->assertErrorTriggered($pluralForm);
	}

/**
 * testNoAssignmentInHeader
 *
 * @return void
 */
	public function testNoAssignmentInHeader() {
		$this->expectErrorTriggered();
		$pluralForm = 'nplurals=2; (n > 1)';
		$formula = $this->PluralForms->parsePluralForms($pluralForm);
		$this->assertErrorTriggered($pluralForm);
	}

/**
 * testSyntaxErrorInFormula
 *
 * @return void
 */
	public function testSyntaxErrorInFormula() {
		$formula = $this->PluralForms->parsePluralForms('nplurals=2; plural=(n => 1)');
		$this->expectErrorTriggered();
		$this->PluralForms->getPlural($formula, 0);
		$this->assertErrorTriggered('Syntax error');
	}

/**
 * testDivisionByZeroInFormula
 *
 * @return void
 */
	public function testDivisionByZeroInFormula() {
		$formula = $this->PluralForms->parsePluralForms('nplurals=2; plural=(10 % n)');
		$this->expectErrorTriggered();
		$this->PluralForms->getPlural($formula, 0);
		$this->assertErrorTriggered('Division by zero in plural formula');
	}
}
