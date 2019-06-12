<?php
use App\Model\Entity\User;

$config = [
    // actions available to everyone, even guests
    'public_actions' => [
        'activities' => '*',
        'audio' => [ 'of', 'index' ],
        'autocompletions' => '*',
        'collections' => [ 'of' ],
        'contributions' => '*',
        'pages' => '*',
        'collections' => [ 'of' ],
        'favorites' => [ 'of_user' ],
        's' => [ 's' ],
        'sentence_annotations' => [ 'last_modified' ],
        'sentences' => [
            'index',
            'show',
            'search',
            'advanced_search',
            'of_user',
            'random',
            'go_to_sentence',
            'several_random_sentences',
            'get_neighbors_for_ajax',
            'show_all_in',
            'with_audio'
        ],
        'sentences_lists' => [
            'index',
            'show',
            'export_to_csv',
            'of_user',
            'download',
            'search',
            'collaborative',
        ],
        'stats' => '*',
        'tags' => [
            'show_sentences_with_tag',
            'view_all',
            'search'
        ],
        'tools' => '*',
        'transcriptions' => [ 'view', 'of' ],
        'user' => [
            'profile',
            'accept_new_terms_of_use',
        ],
        'users' => [
            'all',
            'search',
            'show',
            'login',
            'check_login',
            'logout',
            'register',
            'new_password',
            'check_username',
            'check_email',
            'for_language',
        ],
        'vocabulary' => [ 'of' ],
    ],

    // actions not available for guests or some users
    'auth_actions' => [
    ],
];
