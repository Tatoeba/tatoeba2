<?php
/* SentencesSentencesList Fixture generated on: 2014-09-14 16:11:58 : 1410711118 */
class SentencesSentencesListFixture extends CakeTestFixture {
	public $name = 'SentencesSentencesLists';

	public $fields = array(
		'sentences_list_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('list_id' => array('column' => array('sentences_list_id', 'sentence_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'sentences_list_id' => '1',
			'sentence_id' => '4'
		),
		array(
			'sentences_list_id' => '1',
			'sentence_id' => '8'
		),
	);
}
