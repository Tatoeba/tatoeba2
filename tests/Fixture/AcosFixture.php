<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AcosFixture
 *
 */
class AcosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'parent_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'model' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'foreign_key' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'alias' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'lft' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'rght' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'idx_acos_lft_rght' => ['type' => 'index', 'columns' => ['lft', 'rght'], 'length' => []],
            'idx_acos_alias' => ['type' => 'index', 'columns' => ['alias'], 'length' => []],
            'idx_acos_model_foreign_key' => ['type' => 'index', 'columns' => ['model', 'foreign_key'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'parent_id' => null,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'controllers',
                'lft' => 1,
                'rght' => 206
            ],
            [
                'id' => 2,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Favorites',
                'lft' => 2,
                'rght' => 7
            ],
            [
                'id' => 3,
                'parent_id' => 2,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add_favorite',
                'lft' => 3,
                'rght' => 4
            ],
            [
                'id' => 4,
                'parent_id' => 2,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'remove_favorite',
                'lft' => 5,
                'rght' => 6
            ],
            [
                'id' => 5,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Imports',
                'lft' => 8,
                'rght' => 13
            ],
            [
                'id' => 6,
                'parent_id' => 5,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'import_single_sentences',
                'lft' => 9,
                'rght' => 10
            ],
            [
                'id' => 7,
                'parent_id' => 5,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'import_sentences_with_translation',
                'lft' => 11,
                'rght' => 12
            ],
            [
                'id' => 8,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Links',
                'lft' => 14,
                'rght' => 19
            ],
            [
                'id' => 9,
                'parent_id' => 8,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add',
                'lft' => 15,
                'rght' => 16
            ],
            [
                'id' => 10,
                'parent_id' => 8,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete',
                'lft' => 17,
                'rght' => 18
            ],
            [
                'id' => 11,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'PrivateMessages',
                'lft' => 20,
                'rght' => 39
            ],
            [
                'id' => 12,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'index',
                'lft' => 21,
                'rght' => 22
            ],
            [
                'id' => 13,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'folder',
                'lft' => 23,
                'rght' => 24
            ],
            [
                'id' => 14,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'send',
                'lft' => 25,
                'rght' => 26
            ],
            [
                'id' => 15,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'show',
                'lft' => 27,
                'rght' => 28
            ],
            [
                'id' => 16,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete',
                'lft' => 29,
                'rght' => 30
            ],
            [
                'id' => 17,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'restore',
                'lft' => 31,
                'rght' => 32
            ],
            [
                'id' => 18,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'mark',
                'lft' => 33,
                'rght' => 34
            ],
            [
                'id' => 19,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'write',
                'lft' => 35,
                'rght' => 36
            ],
            [
                'id' => 20,
                'parent_id' => 11,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'empty_folder',
                'lft' => 37,
                'rght' => 38
            ],
            [
                'id' => 21,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'SentenceAnnotations',
                'lft' => 40,
                'rght' => 53
            ],
            [
                'id' => 22,
                'parent_id' => 21,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'index',
                'lft' => 41,
                'rght' => 42
            ],
            [
                'id' => 23,
                'parent_id' => 21,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'show',
                'lft' => 43,
                'rght' => 44
            ],
            [
                'id' => 24,
                'parent_id' => 21,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save',
                'lft' => 45,
                'rght' => 46
            ],
            [
                'id' => 25,
                'parent_id' => 21,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete',
                'lft' => 47,
                'rght' => 48
            ],
            [
                'id' => 26,
                'parent_id' => 21,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'search',
                'lft' => 49,
                'rght' => 50
            ],
            [
                'id' => 27,
                'parent_id' => 21,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'replace',
                'lft' => 51,
                'rght' => 52
            ],
            [
                'id' => 28,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'SentenceComments',
                'lft' => 54,
                'rght' => 65
            ],
            [
                'id' => 29,
                'parent_id' => 28,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save',
                'lft' => 55,
                'rght' => 56
            ],
            [
                'id' => 30,
                'parent_id' => 28,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit',
                'lft' => 57,
                'rght' => 58
            ],
            [
                'id' => 31,
                'parent_id' => 28,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete_comment',
                'lft' => 59,
                'rght' => 60
            ],
            [
                'id' => 32,
                'parent_id' => 28,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'hide_message',
                'lft' => 61,
                'rght' => 62
            ],
            [
                'id' => 33,
                'parent_id' => 28,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'unhide_message',
                'lft' => 63,
                'rght' => 64
            ],
            [
                'id' => 34,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Sentences',
                'lft' => 66,
                'rght' => 91
            ],
            [
                'id' => 35,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add',
                'lft' => 67,
                'rght' => 68
            ],
            [
                'id' => 36,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete',
                'lft' => 69,
                'rght' => 70
            ],
            [
                'id' => 37,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add_an_other_sentence',
                'lft' => 71,
                'rght' => 72
            ],
            [
                'id' => 38,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit_sentence',
                'lft' => 73,
                'rght' => 74
            ],
            [
                'id' => 39,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'adopt',
                'lft' => 75,
                'rght' => 76
            ],
            [
                'id' => 40,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'let_go',
                'lft' => 77,
                'rght' => 78
            ],
            [
                'id' => 41,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_translation',
                'lft' => 79,
                'rght' => 80
            ],
            [
                'id' => 42,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'change_language',
                'lft' => 81,
                'rght' => 82
            ],
            [
                'id' => 43,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'import',
                'lft' => 83,
                'rght' => 84
            ],
            [
                'id' => 44,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit_correctness',
                'lft' => 85,
                'rght' => 86
            ],
            [
                'id' => 45,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit_audio',
                'lft' => 87,
                'rght' => 88
            ],
            [
                'id' => 46,
                'parent_id' => 34,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit_license',
                'lft' => 89,
                'rght' => 90
            ],
            [
                'id' => 47,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'SentencesLists',
                'lft' => 92,
                'rght' => 111
            ],
            [
                'id' => 48,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit',
                'lft' => 93,
                'rght' => 94
            ],
            [
                'id' => 49,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add',
                'lft' => 95,
                'rght' => 96
            ],
            [
                'id' => 50,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_name',
                'lft' => 97,
                'rght' => 98
            ],
            [
                'id' => 51,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete',
                'lft' => 99,
                'rght' => 100
            ],
            [
                'id' => 52,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add_sentence_to_list',
                'lft' => 101,
                'rght' => 102
            ],
            [
                'id' => 53,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'remove_sentence_from_list',
                'lft' => 103,
                'rght' => 104
            ],
            [
                'id' => 54,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add_new_sentence_to_list',
                'lft' => 105,
                'rght' => 106
            ],
            [
                'id' => 55,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'set_as_public',
                'lft' => 107,
                'rght' => 108
            ],
            [
                'id' => 56,
                'parent_id' => 47,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'set_option',
                'lft' => 109,
                'rght' => 110
            ],
            [
                'id' => 57,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Tags',
                'lft' => 112,
                'rght' => 121
            ],
            [
                'id' => 58,
                'parent_id' => 57,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add_tag_post',
                'lft' => 113,
                'rght' => 114
            ],
            [
                'id' => 59,
                'parent_id' => 57,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add_tag',
                'lft' => 115,
                'rght' => 116
            ],
            [
                'id' => 60,
                'parent_id' => 57,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'remove_tag_from_sentence',
                'lft' => 117,
                'rght' => 118
            ],
            [
                'id' => 61,
                'parent_id' => 57,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'remove_tag_of_sentence_from_tags_show',
                'lft' => 119,
                'rght' => 120
            ],
            [
                'id' => 62,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'User',
                'lft' => 122,
                'rght' => 141
            ],
            [
                'id' => 63,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_image',
                'lft' => 123,
                'rght' => 124
            ],
            [
                'id' => 64,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'remove_image',
                'lft' => 125,
                'rght' => 126
            ],
            [
                'id' => 65,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_description',
                'lft' => 127,
                'rght' => 128
            ],
            [
                'id' => 66,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_basic',
                'lft' => 129,
                'rght' => 130
            ],
            [
                'id' => 67,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_settings',
                'lft' => 131,
                'rght' => 132
            ],
            [
                'id' => 68,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_password',
                'lft' => 133,
                'rght' => 134
            ],
            [
                'id' => 69,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit_profile',
                'lft' => 135,
                'rght' => 136
            ],
            [
                'id' => 70,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'settings',
                'lft' => 137,
                'rght' => 138
            ],
            [
                'id' => 71,
                'parent_id' => 62,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'language',
                'lft' => 139,
                'rght' => 140
            ],
            [
                'id' => 72,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Users',
                'lft' => 142,
                'rght' => 149
            ],
            [
                'id' => 73,
                'parent_id' => 72,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'index',
                'lft' => 143,
                'rght' => 144
            ],
            [
                'id' => 74,
                'parent_id' => 72,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit',
                'lft' => 145,
                'rght' => 146
            ],
            [
                'id' => 75,
                'parent_id' => 72,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete',
                'lft' => 147,
                'rght' => 148
            ],
            [
                'id' => 76,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'UsersLanguages',
                'lft' => 150,
                'rght' => 155
            ],
            [
                'id' => 77,
                'parent_id' => 76,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save',
                'lft' => 151,
                'rght' => 152
            ],
            [
                'id' => 78,
                'parent_id' => 76,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete',
                'lft' => 153,
                'rght' => 154
            ],
            [
                'id' => 79,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Collections',
                'lft' => 156,
                'rght' => 161
            ],
            [
                'id' => 80,
                'parent_id' => 79,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add_sentence',
                'lft' => 157,
                'rght' => 158
            ],
            [
                'id' => 81,
                'parent_id' => 79,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete_sentence',
                'lft' => 159,
                'rght' => 160
            ],
            [
                'id' => 82,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Wall',
                'lft' => 162,
                'rght' => 175
            ],
            [
                'id' => 83,
                'parent_id' => 82,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save',
                'lft' => 163,
                'rght' => 164
            ],
            [
                'id' => 84,
                'parent_id' => 82,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_inside',
                'lft' => 165,
                'rght' => 166
            ],
            [
                'id' => 85,
                'parent_id' => 82,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'edit',
                'lft' => 167,
                'rght' => 168
            ],
            [
                'id' => 86,
                'parent_id' => 82,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'delete_message',
                'lft' => 169,
                'rght' => 170
            ],
            [
                'id' => 87,
                'parent_id' => 82,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'hide_message',
                'lft' => 171,
                'rght' => 172
            ],
            [
                'id' => 88,
                'parent_id' => 82,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'unhide_message',
                'lft' => 173,
                'rght' => 174
            ],
            [
                'id' => 89,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Transcriptions',
                'lft' => 176,
                'rght' => 183
            ],
            [
                'id' => 90,
                'parent_id' => 89,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save',
                'lft' => 177,
                'rght' => 178
            ],
            [
                'id' => 91,
                'parent_id' => 89,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'view',
                'lft' => 179,
                'rght' => 180
            ],
            [
                'id' => 92,
                'parent_id' => 89,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'reset',
                'lft' => 181,
                'rght' => 182
            ],
            [
                'id' => 93,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Audio',
                'lft' => 184,
                'rght' => 189
            ],
            [
                'id' => 94,
                'parent_id' => 93,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'import',
                'lft' => 185,
                'rght' => 186
            ],
            [
                'id' => 95,
                'parent_id' => 93,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_settings',
                'lft' => 187,
                'rght' => 188
            ],
            [
                'id' => 96,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Vocabulary',
                'lft' => 190,
                'rght' => 201
            ],
            [
                'id' => 97,
                'parent_id' => 96,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add',
                'lft' => 191,
                'rght' => 192
            ],
            [
                'id' => 98,
                'parent_id' => 96,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'add_sentences',
                'lft' => 193,
                'rght' => 194
            ],
            [
                'id' => 99,
                'parent_id' => 96,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save',
                'lft' => 195,
                'rght' => 196
            ],
            [
                'id' => 100,
                'parent_id' => 96,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'remove',
                'lft' => 197,
                'rght' => 198
            ],
            [
                'id' => 101,
                'parent_id' => 96,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'save_sentence',
                'lft' => 199,
                'rght' => 200
            ],
            [
                'id' => 102,
                'parent_id' => 1,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'Licensing',
                'lft' => 202,
                'rght' => 205
            ],
            [
                'id' => 103,
                'parent_id' => 102,
                'model' => null,
                'foreign_key' => null,
                'alias' => 'switch_my_sentences',
                'lft' => 203,
                'rght' => 204
            ],
        ];
        parent::init();
    }
}
