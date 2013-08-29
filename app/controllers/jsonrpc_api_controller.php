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
                'fetchWall'
            )
        )
    );
    
    /**
     * Scheme for minify operations
     * 
     * @var array
     */
    private $_minifyMappings = array(
        'common' => array(
            'key' => 'value',
            'key' => 'value'
        ),
        'search' => array(
            'key' => 'value',
            'key' => 'value',
            'v1' => array(
                'v1_key' => 'v1_value',
                'v1_key' => 'v1_value'
            )
        ),
        'getSentenceDetails' => array(
            'key' => 'value',
            'key' => 'value',
            'v1' => array(
                'v1_key' => 'v1_value',
                'v1_key' => 'v1_value'
            )
        ),
        'getCommentDetails' => array(
            'key' => 'value',
            'key' => 'value',
            'v1' => array(
                'v1_key' => 'v1_value',
                'v1_key' => 'v1_value'
            )
        ),
        'getUserDetails' => array(
            'key' => 'value',
            'key' => 'value',
            'v1' => array(
                'v1_key' => 'v1_value',
                'v1_key' => 'v1_value'
            )
        ),
        'getUsers' => array(
            'key' => 'value',
            'key' => 'value',
            'v1' => array(
                'v1_key' => 'v1_value',
                'v1_key' => 'v1_value'
            )
        ),
        'fetchWall' => array(
            'key' => 'value',
            'key' => 'value',
            'v1' => array(
                'v1_key' => 'v1_value',
                'v1_key' => 'v1_value'
            )
        ),
        'etc...'
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
        $jsonObject = $this->_minifyExpand("",$jsonArray);
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
     * Search sentences function
     * 
     * @param $jsonArray array JSON request
     * @version 1
     * 
     * @return array
     */
    private function _search_v1($jsonArray)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Find sentence function
     * 
     * @param $jsonArray array JSON request
     * @version 1
     * 
     * @return array
     */
    private function _getSentenceDetails_v1($jsonArray)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Find comment function
     * 
     * @param $jsonArray array JSON request
     * @version 1
     * 
     * @return array
     */
    private function _getCommentDetails_v1($jsonArray)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Users list function
     * 
     * @param $jsonArray array JSON request
     * @version 1
     * 
     * @return array
     */
    private function _getUsers_v1($jsonArray)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Find User function
     * 
     * @param $jsonArray array JSON request
     * @version 1
     * 
     * @return array
     */
    private function _getUserDetails_v1($jsonArray)
    {
        $this->cacheAction = true;
        $results = null;
    }
    
    
    /**
     * Find wall messages function
     * 
     * @param $jsonArray array JSON request
     * @version 1
     * 
     * @return array
     */
    private function _fetchWall_v1($jsonArray)
    {
        $this->cacheAction = true;
        $results = null;
    }
}

?>