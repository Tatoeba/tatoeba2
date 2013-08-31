<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/*
 * Wiki for this API can be found here:
 * https://github.com/trang/tatoeba-api/wiki/_pages
 */
class JsonrpcApiController extends AppController
{
    
    /**
     * Name of this controller
     * 
     * @var string
     */
    public $name = "JsonrpcApi";
    
    
    /**
     * Models will be loaded as needed by individial methods
     * 
     * @var array
     */
    public $uses = array();
    
    
    /**
     * Add helpers here if needed
     * 
     * @var array
     */
    public $helpers = array('Cache');
    
    
    /**
     * Initialize the jsonrpc component here by listing all the api methods
     * 
     * @var array
     */
    public $components = array(
        'Jsonrpc' => array(
            'listen' => array(
                'search',
                'getSentenceDetails',
                'getCommentDetails',
                'getUsers',
                'getUserDetails',
                'fetchWall',
                'fetchWallThread'
            )
        )
    );
    
    /**
     * Scheme for minifying request
     * 
     * @var array
     */
    private $_minifyRequestMap = array(
        'common' => array(
            'version 1' => array(
                'v' => 'version',
                'i' => 'id',
                'o' => 'options',
                'p' => 'page',
                'q' => 'query',
            )
        ),
        'search' => array(
            'version 1' => array(
                't' => 'to',
                'f' => 'from',
            )
        ),
        'getUsers' => array(
            'version 1' => array(
                't' => 'type',
            )
        )
    );
    
    /**
     * Scheme for minifying response
     * 
     * @var array
     */
    private $_minifyResponseMap = array(
        'common' => array(
            'version 1' => array(
                'version' => 'v',
            )
        ),
        'search' => array(
            'version 1' => array(
                'total' => 't',
                'sentences' => 's',
                'sentences' => array(
                    'id' => 'i',
                    'text' => 't',
                    'lang' => 'l',
                    'tags' => 'tg',
                    'audio' => 'a',
                    'user_id' => 'ui',
                    'username' => 'un',
                    'created' => 'c',
                    'modified' => 'm',
                    'comments' => 'c',
                    'direct' => 'd',
                    'indirect' => 'in'
                )
            )
        ),
        'getSentenceDetails' => array(
            'version 1' => array(
                'sentence' => 's',
                'comments' => 'c',
                'common' => array(
                    'id' => 'i',
                    'user_id' => 'ui',
                    'username' => 'un',
                    'created' => 'c',
                    'modified' => 'm',
                    'text' => 't'
                ),
                'sentence' => array(
                    'audio' => 'a',
                    'tags' => 'tg'
                ),
                'comments' => array(
                    'sentence_id' => 'si'
                )
            )
        ),
        'getComments' => array(
            'version 1' => array(
                'comments' => array(
                    'id' => 'i',
                    'user_id' => 'ui',
                    'username' => 'un',
                    'created' => 'c',
                    'modified' => 'm',
                    'text' => 't',
                    'lang' => 'l'
                )
            )
        ),
        'getCommentDetails' => array(
            'version 1' => array(
                'comments' => array(
                    'id' => 'i',
                    'user_id' => 'ui',
                    'username' => 'un',
                    'created' => 'c',
                    'modified' => 'm',
                    'text' => 't',
                    'lang' => 'l'
                )
            )
        ),
        'getUsers' => array(
            'version 1' => array(
                "id" => "i",
                "group_id" => "gi",
                "username" => "un",
                "since" => "s",
                "img" => "im",
            )
        ),
        'getUserDetails' => array(
            'version 1' => array(
                'user' => 'u',
                'user' => array(
                    "id" => "i",
                    "group_id" => "gi",
                    "username" => "un",
                    "name" => "n",
                    "lang" => "l",
                    "country" => "c",
                    "since" => "s",
                    "last_active" => "la",
                    "desc" => "d",
                    "birthday" => "b",
                    "homepage" => "h",
                    "img" => "im",
                    "send_notifications" => "sn",
                    "level" => "lv"
                )
            )
        ),
        'fetchWall' => array(
            'version 1' => array(
                "wallPosts" => "w",
                "wallPosts" => array(
                    "id" => "i",
                    "user_id" => "ui",
                    "username" => "un",
                    "created" => "c",
                    "modified" => "m",
                    "text" => "t",
                    "replies" => "r"
                )
            )
        ),
        'fetchWallThread' => array(
            'version 1' => array(
                "wallPosts" => "w",
                "wallPosts" => array(
                    "id" => "i",
                    "user_id" => "ui",
                    "username" => "un",
                    "created" => "c",
                    "modified" => "m",
                    "text" => "t",
                    "replies" => "r"
                )
            )
        )
    );
    
    /**
     * Minify function, compress data
     * 
     * @param string $context     The mapping context (the method name)
     * @param array $jsonArray  The JSON data to compress
     * 
     * @return object compressed JSON data
     */
    private function _minifyCompress($context, $jsonArray)
    {
        
    }
    
    /**
     * Minify function, expand data
     * 
     * @param string $context     The mapping context (the method name)
     * @param array $jsonArray  The JSON data to expand
     * 
     * @return array expanded JSON data
     */
    private function _minifyExpand($context, $jsonArray)
    {
        
    }
    
    
    /**
     * Search sentences optionally with translations and comments
     * 
     * @param $jsonArray array JSON request
     * 
     * @return array Search results
     */
    public function search($jsonArray)
    {
        $jsonObject = $this->_minifyExpand("search",$jsonArray);
    }
    
    /**
     * Retrieve sentences for individual display
     * 
     * @param $jsonArray array JSON request
     * 
     * @return array Sentences
     */
    public function getSentenceDetails($jsonArray)
    {
        $jsonObject = $this->_minifyExpand("",$jsonObject);
    }
    
    
    /**
     * Retrieve comments for individual display
     * 
     * @param $jsonArray array JSON request
     * 
     * @return array A single comment
     */
    public function getCommentDetails($jsonArray)
    {
        $jsonObject = $this->_minifyExpand("",$jsonObject);
    }
    
    
    /**
     * Retrieve user for individual display
     * 
     * @param $jsonArray array JSON request
     * 
     * @return array Details of a single user
     */
    public function getUserDetails($jsonArray)
    {
        $jsonObject = $this->_minifyExpand("",$jsonObject);
    }
    
    
    /**
     * Retrieve list of users
     * 
     * @param $jsonArray array JSON request
     * 
     * @return array A List of users
     */
    public function getUsers($jsonArray)
    {
        $jsonObject = $this->_minifyExpand("",$jsonObject);
    }
    
    
    /**
     * Retrieve a wall post with all its replies
     * 
     * @param $jsonArray array JSON request
     * 
     * @return array Wall messages with reply structure
     */
    public function fetchWallThread($jsonArray)
    {
        $jsonObject = $this->_minifyExpand("",$jsonObject);
    }
    
    
    /**
     * Retrieve wall posts
     * 
     * @param $jsonArray array JSON request
     * 
     * @return array Wall messages with reply structure
     */
    public function fetchWall($jsonArray)
    {
        $jsonObject = $this->_minifyExpand("",$jsonObject);
    }
    
    
    /**
     * Search sentences
     * 
     * @param  $query    string  The query string
     * @param  $from     string  The source language
     * @param  $to       string  The target language
     * @param  $page     string  Pagination details
     * @param  $options  array   Options for query
     * @version 1
     * 
     * @return array
     */
    private function _search_v1($query, $from, $to, $page, $options)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Find sentence
     * 
     * @param  $id       int    Id of sentence
     * @param  $options  array  Options for query
     * @version 1
     * 
     * @return array
     */
    private function _getSentenceDetails_v1($id, $options)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Get list of comment
     * 
     * @param  $id  array Id's of comments
     * @version 1
     * 
     * @return array
     */
    private function _getComments_v1($ids)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Find comment
     * 
     * @param  $id  int Id of comment
     * @version 1
     * 
     * @return array
     */
    private function _getCommentDetails_v1($id)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Get list of users or single user
     * 
     * @param  $query   mixed   Either a search string or array of id's
     * @version 1
     * 
     * @return array
     */
    private function _getUsers_v1($query)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Get a User's profile
     * 
     * @param   $query   mixed   Either a search string or array of id's
     * @version 1
     * 
     * @return array
     */
    private function _getUserDetails_v1($query)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Get wall messages
     * 
     * @param  $page     array  Pagination options
     * @param  $options  array  Options for query 
     * @version 1
     * 
     * @return array
     */
    private function _fetchWall_v1($page, $options)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    /**
     * Get message and replies
     * 
     * @param   $id   Id of wall message
     * @version 1
     * 
     * @return array
     */
    private function _fetchWallThread_v1($id)
    {
        $this->cacheAction = true;
        $results = null;
    }
}

?>