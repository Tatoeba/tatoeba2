<?php
/* Aco Fixture generated on: 2014-10-20 07:39:12 : 1413790752 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class AcosFixture extends TestFixture {
	public $name = 'Aco';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'model' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
		'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'alias' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'_indexes' => ['idx_acos_lft_rght' => ['unique' => 0, 'columns' => ['lft', 'rght']], 'idx_acos_alias' => ['unique' => 0, 'columns' => 'alias'], 'idx_acos_model_foreign_key' => ['unique' => 0, 'columns' => ['model', 'foreign_key']]],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM']
	);

	public $records = array(
		array(
			'id' => '314',
			'parent_id' => NULL,
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'controllers',
			'lft' => '1',
			'rght' => '374'
		),
		array(
			'id' => '315',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Pages',
			'lft' => '2',
			'rght' => '31'
		),
		array(
			'id' => '316',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '3',
			'rght' => '4'
		),
		array(
			'id' => '317',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'home',
			'lft' => '5',
			'rght' => '6'
		),
		array(
			'id' => '318',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'contribute',
			'lft' => '7',
			'rght' => '8'
		),
		array(
			'id' => '319',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'about',
			'lft' => '9',
			'rght' => '10'
		),
		array(
			'id' => '320',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'search',
			'lft' => '11',
			'rght' => '12'
		),
		array(
			'id' => '321',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'contact',
			'lft' => '13',
			'rght' => '14'
		),
		array(
			'id' => '322',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'help',
			'lft' => '15',
			'rght' => '16'
		),
		array(
			'id' => '323',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'how_to_contribute',
			'lft' => '17',
			'rght' => '18'
		),
		array(
			'id' => '324',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'tatoeba_team_and_credits',
			'lft' => '19',
			'rght' => '20'
		),
		array(
			'id' => '325',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'download_tatoeba_example_sentences',
			'lft' => '21',
			'rght' => '22'
		),
		array(
			'id' => '326',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'terms_of_use',
			'lft' => '23',
			'rght' => '24'
		),
		array(
			'id' => '327',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'whats_new',
			'lft' => '25',
			'rght' => '26'
		),
		array(
			'id' => '328',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'faq',
			'lft' => '27',
			'rght' => '28'
		),
		array(
			'id' => '329',
			'parent_id' => '315',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '29',
			'rght' => '30'
		),
		array(
			'id' => '330',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Activities',
			'lft' => '32',
			'rght' => '47'
		),
		array(
			'id' => '331',
			'parent_id' => '330',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add_sentences',
			'lft' => '33',
			'rght' => '34'
		),
		array(
			'id' => '332',
			'parent_id' => '330',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'adopt_sentences',
			'lft' => '35',
			'rght' => '36'
		),
		array(
			'id' => '333',
			'parent_id' => '330',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'improve_sentences',
			'lft' => '37',
			'rght' => '38'
		),
		array(
			'id' => '334',
			'parent_id' => '330',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'link_sentences',
			'lft' => '39',
			'rght' => '40'
		),
		array(
			'id' => '335',
			'parent_id' => '330',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'translate_sentences',
			'lft' => '41',
			'rght' => '42'
		),
		array(
			'id' => '336',
			'parent_id' => '330',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'translate_sentences_of',
			'lft' => '43',
			'rght' => '44'
		),
		array(
			'id' => '337',
			'parent_id' => '330',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '45',
			'rght' => '46'
		),
		array(
			'id' => '338',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Autocompletions',
			'lft' => '48',
			'rght' => '53'
		),
		array(
			'id' => '339',
			'parent_id' => '338',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'request',
			'lft' => '49',
			'rght' => '50'
		),
		array(
			'id' => '340',
			'parent_id' => '338',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '51',
			'rght' => '52'
		),
		array(
			'id' => '341',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Contributions',
			'lft' => '54',
			'rght' => '67'
		),
		array(
			'id' => '342',
			'parent_id' => '341',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '55',
			'rght' => '56'
		),
		array(
			'id' => '343',
			'parent_id' => '341',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'latest',
			'lft' => '57',
			'rght' => '58'
		),
		array(
			'id' => '344',
			'parent_id' => '341',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'statistics',
			'lft' => '59',
			'rght' => '60'
		),
		array(
			'id' => '345',
			'parent_id' => '341',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'activity_timeline',
			'lft' => '61',
			'rght' => '62'
		),
		array(
			'id' => '346',
			'parent_id' => '341',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'of_user',
			'lft' => '63',
			'rght' => '64'
		),
		array(
			'id' => '347',
			'parent_id' => '341',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '65',
			'rght' => '66'
		),
		array(
			'id' => '348',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Favorites',
			'lft' => '68',
			'rght' => '77'
		),
		array(
			'id' => '349',
			'parent_id' => '348',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'of_user',
			'lft' => '69',
			'rght' => '70'
		),
		array(
			'id' => '350',
			'parent_id' => '348',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add_favorite',
			'lft' => '71',
			'rght' => '72'
		),
		array(
			'id' => '351',
			'parent_id' => '348',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'remove_favorite',
			'lft' => '73',
			'rght' => '74'
		),
		array(
			'id' => '352',
			'parent_id' => '348',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '75',
			'rght' => '76'
		),
		array(
			'id' => '353',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Groups',
			'lft' => '78',
			'rght' => '87'
		),
		array(
			'id' => '354',
			'parent_id' => '353',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'init_aro_groups',
			'lft' => '79',
			'rght' => '80'
		),
		array(
			'id' => '355',
			'parent_id' => '353',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'init_aro_users',
			'lft' => '81',
			'rght' => '82'
		),
		array(
			'id' => '356',
			'parent_id' => '353',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'update_rights',
			'lft' => '83',
			'rght' => '84'
		),
		array(
			'id' => '357',
			'parent_id' => '353',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '85',
			'rght' => '86'
		),
		array(
			'id' => '358',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Imports',
			'lft' => '88',
			'rght' => '95'
		),
		array(
			'id' => '359',
			'parent_id' => '358',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'import_single_sentences',
			'lft' => '89',
			'rght' => '90'
		),
		array(
			'id' => '360',
			'parent_id' => '358',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'import_sentences_with_translation',
			'lft' => '91',
			'rght' => '92'
		),
		array(
			'id' => '361',
			'parent_id' => '358',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '93',
			'rght' => '94'
		),
		array(
			'id' => '362',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Links',
			'lft' => '96',
			'rght' => '103'
		),
		array(
			'id' => '363',
			'parent_id' => '362',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add',
			'lft' => '97',
			'rght' => '98'
		),
		array(
			'id' => '364',
			'parent_id' => '362',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'delete',
			'lft' => '99',
			'rght' => '100'
		),
		array(
			'id' => '365',
			'parent_id' => '362',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '101',
			'rght' => '102'
		),
		array(
			'id' => '366',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'PrivateMessages',
			'lft' => '104',
			'rght' => '125'
		),
		array(
			'id' => '367',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '105',
			'rght' => '106'
		),
		array(
			'id' => '368',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'folder',
			'lft' => '107',
			'rght' => '108'
		),
		array(
			'id' => '369',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'send',
			'lft' => '109',
			'rght' => '110'
		),
		array(
			'id' => '370',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show',
			'lft' => '111',
			'rght' => '112'
		),
		array(
			'id' => '371',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'delete',
			'lft' => '113',
			'rght' => '114'
		),
		array(
			'id' => '372',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'restore',
			'lft' => '115',
			'rght' => '116'
		),
		array(
			'id' => '373',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'mark',
			'lft' => '117',
			'rght' => '118'
		),
		array(
			'id' => '374',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'write',
			'lft' => '119',
			'rght' => '120'
		),
		array(
			'id' => '375',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'join',
			'lft' => '121',
			'rght' => '122'
		),
		array(
			'id' => '376',
			'parent_id' => '366',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '123',
			'rght' => '124'
		),
		array(
			'id' => '377',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Sentences',
			'lft' => '126',
			'rght' => '171'
		),
		array(
			'id' => '378',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '127',
			'rght' => '128'
		),
		array(
			'id' => '379',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show',
			'lft' => '129',
			'rght' => '130'
		),
		array(
			'id' => '380',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'go_to_sentence',
			'lft' => '131',
			'rght' => '132'
		),
		array(
			'id' => '381',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add',
			'lft' => '133',
			'rght' => '134'
		),
		array(
			'id' => '382',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'delete',
			'lft' => '135',
			'rght' => '136'
		),
		array(
			'id' => '383',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add_an_other_sentence',
			'lft' => '137',
			'rght' => '138'
		),
		array(
			'id' => '384',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'edit_sentence',
			'lft' => '139',
			'rght' => '140'
		),
		array(
			'id' => '385',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'adopt',
			'lft' => '141',
			'rght' => '142'
		),
		array(
			'id' => '386',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'let_go',
			'lft' => '143',
			'rght' => '144'
		),
		array(
			'id' => '387',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save_translation',
			'lft' => '145',
			'rght' => '146'
		),
		array(
			'id' => '388',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'search',
			'lft' => '147',
			'rght' => '148'
		),
		array(
			'id' => '389',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show_all_in',
			'lft' => '149',
			'rght' => '150'
		),
		array(
			'id' => '390',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'random',
			'lft' => '151',
			'rght' => '152'
		),
		array(
			'id' => '391',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'several_random_sentences',
			'lft' => '153',
			'rght' => '154'
		),
		array(
			'id' => '392',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'set_languages',
			'lft' => '155',
			'rght' => '156'
		),
		array(
			'id' => '393',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'of_user',
			'lft' => '157',
			'rght' => '158'
		),
		array(
			'id' => '394',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'map',
			'lft' => '159',
			'rght' => '160'
		),
		array(
			'id' => '395',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'change_language',
			'lft' => '161',
			'rght' => '162'
		),
		array(
			'id' => '396',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'get_neighbors_for_ajax',
			'lft' => '163',
			'rght' => '164'
		),
		array(
			'id' => '397',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'import',
			'lft' => '165',
			'rght' => '166'
		),
		array(
			'id' => '398',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'with_audio',
			'lft' => '167',
			'rght' => '168'
		),
		array(
			'id' => '399',
			'parent_id' => '377',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '169',
			'rght' => '170'
		),
		array(
			'id' => '400',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'SentencesLists',
			'lft' => '172',
			'rght' => '201'
		),
		array(
			'id' => '401',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '173',
			'rght' => '174'
		),
		array(
			'id' => '402',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show',
			'lft' => '175',
			'rght' => '176'
		),
		array(
			'id' => '403',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'edit',
			'lft' => '177',
			'rght' => '178'
		),
		array(
			'id' => '404',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add',
			'lft' => '179',
			'rght' => '180'
		),
		array(
			'id' => '405',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save_name',
			'lft' => '181',
			'rght' => '182'
		),
		array(
			'id' => '406',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'delete',
			'lft' => '183',
			'rght' => '184'
		),
		array(
			'id' => '407',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add_sentence_to_list',
			'lft' => '185',
			'rght' => '186'
		),
		array(
			'id' => '408',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'remove_sentence_from_list',
			'lft' => '187',
			'rght' => '188'
		),
		array(
			'id' => '409',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'of_user',
			'lft' => '189',
			'rght' => '190'
		),
		array(
			'id' => '410',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add_new_sentence_to_list',
			'lft' => '191',
			'rght' => '192'
		),
		array(
			'id' => '411',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'set_as_public',
			'lft' => '193',
			'rght' => '194'
		),
		array(
			'id' => '412',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'download',
			'lft' => '195',
			'rght' => '196'
		),
		array(
			'id' => '413',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'export_to_csv',
			'lft' => '197',
			'rght' => '198'
		),
		array(
			'id' => '414',
			'parent_id' => '400',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '199',
			'rght' => '200'
		),
		array(
			'id' => '415',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'SentenceAnnotations',
			'lft' => '202',
			'rght' => '219'
		),
		array(
			'id' => '416',
			'parent_id' => '415',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '203',
			'rght' => '204'
		),
		array(
			'id' => '417',
			'parent_id' => '415',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show',
			'lft' => '205',
			'rght' => '206'
		),
		array(
			'id' => '418',
			'parent_id' => '415',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save',
			'lft' => '207',
			'rght' => '208'
		),
		array(
			'id' => '419',
			'parent_id' => '415',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'delete',
			'lft' => '209',
			'rght' => '210'
		),
		array(
			'id' => '420',
			'parent_id' => '415',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'search',
			'lft' => '211',
			'rght' => '212'
		),
		array(
			'id' => '421',
			'parent_id' => '415',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'replace',
			'lft' => '213',
			'rght' => '214'
		),
		array(
			'id' => '422',
			'parent_id' => '415',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'last_modified',
			'lft' => '215',
			'rght' => '216'
		),
		array(
			'id' => '423',
			'parent_id' => '415',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '217',
			'rght' => '218'
		),
		array(
			'id' => '424',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'SentenceComments',
			'lft' => '220',
			'rght' => '235'
		),
		array(
			'id' => '425',
			'parent_id' => '424',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '221',
			'rght' => '222'
		),
		array(
			'id' => '426',
			'parent_id' => '424',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show',
			'lft' => '223',
			'rght' => '224'
		),
		array(
			'id' => '427',
			'parent_id' => '424',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save',
			'lft' => '225',
			'rght' => '226'
		),
		array(
			'id' => '428',
			'parent_id' => '424',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'delete_comment',
			'lft' => '227',
			'rght' => '228'
		),
		array(
			'id' => '429',
			'parent_id' => '424',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'of_user',
			'lft' => '229',
			'rght' => '230'
		),
		array(
			'id' => '430',
			'parent_id' => '424',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'on_sentences_of_user',
			'lft' => '231',
			'rght' => '232'
		),
		array(
			'id' => '431',
			'parent_id' => '424',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '233',
			'rght' => '234'
		),
		array(
			'id' => '432',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Sinograms',
			'lft' => '236',
			'rght' => '251'
		),
		array(
			'id' => '433',
			'parent_id' => '432',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '237',
			'rght' => '238'
		),
		array(
			'id' => '434',
			'parent_id' => '432',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'search',
			'lft' => '239',
			'rght' => '240'
		),
		array(
			'id' => '435',
			'parent_id' => '432',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'explode',
			'lft' => '241',
			'rght' => '242'
		),
		array(
			'id' => '436',
			'parent_id' => '432',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'load_sinogram_informations',
			'lft' => '243',
			'rght' => '244'
		),
		array(
			'id' => '437',
			'parent_id' => '432',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'load_example_sentence',
			'lft' => '245',
			'rght' => '246'
		),
		array(
			'id' => '438',
			'parent_id' => '432',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'load_radicals',
			'lft' => '247',
			'rght' => '248'
		),
		array(
			'id' => '439',
			'parent_id' => '432',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '249',
			'rght' => '250'
		),
		array(
			'id' => '440',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Stats',
			'lft' => '252',
			'rght' => '257'
		),
		array(
			'id' => '441',
			'parent_id' => '440',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'sentences_by_language',
			'lft' => '253',
			'rght' => '254'
		),
		array(
			'id' => '442',
			'parent_id' => '440',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '255',
			'rght' => '256'
		),
		array(
			'id' => '443',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Tags',
			'lft' => '258',
			'rght' => '275'
		),
		array(
			'id' => '444',
			'parent_id' => '443',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add_tag_post',
			'lft' => '259',
			'rght' => '260'
		),
		array(
			'id' => '445',
			'parent_id' => '443',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'add_tag',
			'lft' => '261',
			'rght' => '262'
		),
		array(
			'id' => '446',
			'parent_id' => '443',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'view_all',
			'lft' => '263',
			'rght' => '264'
		),
		array(
			'id' => '447',
			'parent_id' => '443',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'remove_tag_from_sentence',
			'lft' => '265',
			'rght' => '266'
		),
		array(
			'id' => '448',
			'parent_id' => '443',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'remove_tag_of_sentence_from_tags_show',
			'lft' => '267',
			'rght' => '268'
		),
		array(
			'id' => '449',
			'parent_id' => '443',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show_sentences_with_tag',
			'lft' => '269',
			'rght' => '270'
		),
		array(
			'id' => '450',
			'parent_id' => '443',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'for_moderators',
			'lft' => '271',
			'rght' => '272'
		),
		array(
			'id' => '451',
			'parent_id' => '443',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '273',
			'rght' => '274'
		),
		array(
			'id' => '452',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Tools',
			'lft' => '276',
			'rght' => '291'
		),
		array(
			'id' => '453',
			'parent_id' => '452',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '277',
			'rght' => '278'
		),
		array(
			'id' => '454',
			'parent_id' => '452',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'kakasi',
			'lft' => '279',
			'rght' => '280'
		),
		array(
			'id' => '455',
			'parent_id' => '452',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'romaji_furigana',
			'lft' => '281',
			'rght' => '282'
		),
		array(
			'id' => '456',
			'parent_id' => '452',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'conversion_simplified_traditional_chinese',
			'lft' => '283',
			'rght' => '284'
		),
		array(
			'id' => '457',
			'parent_id' => '452',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'pinyin_converter',
			'lft' => '285',
			'rght' => '286'
		),
		array(
			'id' => '458',
			'parent_id' => '452',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'shanghainese_to_ipa',
			'lft' => '287',
			'rght' => '288'
		),
		array(
			'id' => '459',
			'parent_id' => '452',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '289',
			'rght' => '290'
		),
		array(
			'id' => '460',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Users',
			'lft' => '292',
			'rght' => '325'
		),
		array(
			'id' => '461',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '293',
			'rght' => '294'
		),
		array(
			'id' => '462',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'edit',
			'lft' => '295',
			'rght' => '296'
		),
		array(
			'id' => '463',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'delete',
			'lft' => '297',
			'rght' => '298'
		),
		array(
			'id' => '464',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'login',
			'lft' => '299',
			'rght' => '300'
		),
		array(
			'id' => '465',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'check_login',
			'lft' => '301',
			'rght' => '302'
		),
		array(
			'id' => '466',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'logout',
			'lft' => '303',
			'rght' => '304'
		),
		array(
			'id' => '467',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'register',
			'lft' => '305',
			'rght' => '306'
		),
		array(
			'id' => '468',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'new_password',
			'lft' => '307',
			'rght' => '308'
		),
		array(
			'id' => '469',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'search',
			'lft' => '309',
			'rght' => '310'
		),
		array(
			'id' => '470',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show',
			'lft' => '311',
			'rght' => '312'
		),
		array(
			'id' => '471',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'all',
			'lft' => '313',
			'rght' => '314'
		),
		array(
			'id' => '472',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'captcha_image',
			'lft' => '315',
			'rght' => '316'
		),
		array(
			'id' => '473',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'check_username',
			'lft' => '317',
			'rght' => '318'
		),
		array(
			'id' => '474',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'check_email',
			'lft' => '319',
			'rght' => '320'
		),
		array(
			'id' => '475',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '321',
			'rght' => '322'
		),
		array(
			'id' => '476',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'User',
			'lft' => '326',
			'rght' => '345'
		),
		array(
			'id' => '477',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'profile',
			'lft' => '327',
			'rght' => '328'
		),
		array(
			'id' => '478',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save_image',
			'lft' => '329',
			'rght' => '330'
		),
		array(
			'id' => '479',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save_description',
			'lft' => '331',
			'rght' => '332'
		),
		array(
			'id' => '480',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save_basic',
			'lft' => '333',
			'rght' => '334'
		),
		array(
			'id' => '481',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save_settings',
			'lft' => '335',
			'rght' => '336'
		),
		array(
			'id' => '482',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save_password',
			'lft' => '337',
			'rght' => '338'
		),
		array(
			'id' => '483',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'edit_profile',
			'lft' => '339',
			'rght' => '340'
		),
		array(
			'id' => '484',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'settings',
			'lft' => '341',
			'rght' => '342'
		),
		array(
			'id' => '485',
			'parent_id' => '476',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '343',
			'rght' => '344'
		),
		array(
			'id' => '486',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Visitors',
			'lft' => '346',
			'rght' => '351'
		),
		array(
			'id' => '487',
			'parent_id' => '486',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'online',
			'lft' => '347',
			'rght' => '348'
		),
		array(
			'id' => '488',
			'parent_id' => '486',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '349',
			'rght' => '350'
		),
		array(
			'id' => '489',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Wall',
			'lft' => '352',
			'rght' => '369'
		),
		array(
			'id' => '490',
			'parent_id' => '489',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'index',
			'lft' => '353',
			'rght' => '354'
		),
		array(
			'id' => '491',
			'parent_id' => '489',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save',
			'lft' => '355',
			'rght' => '356'
		),
		array(
			'id' => '492',
			'parent_id' => '489',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'save_inside',
			'lft' => '357',
			'rght' => '358'
		),
		array(
			'id' => '493',
			'parent_id' => '489',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'delete_message',
			'lft' => '359',
			'rght' => '360'
		),
		array(
			'id' => '494',
			'parent_id' => '489',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'update_thread_date',
			'lft' => '361',
			'rght' => '362'
		),
		array(
			'id' => '495',
			'parent_id' => '489',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'show_message',
			'lft' => '363',
			'rght' => '364'
		),
		array(
			'id' => '496',
			'parent_id' => '489',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'messages_of_user',
			'lft' => '365',
			'rght' => '366'
		),
		array(
			'id' => '497',
			'parent_id' => '489',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'getSupportedLanguage',
			'lft' => '367',
			'rght' => '368'
		),
		array(
			'id' => '498',
			'parent_id' => '460',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'update_rights',
			'lft' => '323',
			'rght' => '324'
		),
		array(
			'id' => '499',
			'parent_id' => '314',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'Transcriptions',
			'lft' => '370',
			'rght' => '373'
		),
		array(
			'id' => '500',
			'parent_id' => '499',
			'model' => NULL,
			'foreign_key' => NULL,
			'alias' => 'edit',
			'lft' => '371',
			'rght' => '372'
		),
	);
}
