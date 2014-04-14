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
     * Components for this controller
     *
     * @var array
     */
    public $components = array('Jsonrpc');


    /**
     * Variable to store map for minification/deminification
     *
     * @var array
     */
    private $_context = array();


    /**
     * CakePHP function
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allowedActions = array("*");
        $this->Jsonrpc->listen = array(
            'search',
            'getSentenceDetails'
        );
    }


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
        return $jsonArray;
    }


    /**
     * Minify function, expand data
     *
     * @param array $contex     The mapping context (the method name)
     * @param array $jsonArray  The JSON data to expand
     *
     * @return array expanded JSON data
     */
    private function _minifyExpand($context, $jsonArray)
    {
        foreach($jsonArray as $letter=>$value) {
            if (array_key_exists($letter, $context)) {
                $jsonArray[$context[$letter]] = $jsonArray[$letter];
                unset($jsonArray[$letter]);
            }
        }

        return $jsonArray;
    }


    /**
     * This function is a callback used by the component. It is supplied with an array whose
     * first index contains the method name and second index contains the arguments
     *
     * @return array  The return value of whichever method is invoked
     */
    public function invokeAPIMethod()
    {
        $jsonRequest = func_get_args();
        $version = $verName = $methName = "";
        $params = array();

        if (empty($jsonRequest[1]['v'])) {
            throw new Exception("Method version not specified.", 0);
        } else {
            $version = $jsonRequest[1]['v'];
        }

        if (!method_exists(get_class($this), "_{$jsonRequest[0]}_v{$version}")) {
            throw new Exception("Method version does not exist.", 0);
        } else {
            unset($jsonRequest[1]['v']);
            $methName = $jsonRequest[0];
            $verName = "_{$jsonRequest[0]}_v{$version}";
        }

        if (empty($jsonRequest[1])) {
            throw new Exception("No params supplied to method.", 0);
        } else {
            $params = $jsonRequest[1];
        }

        return  call_user_func_array(array($this, $methName), array($verName, $version, $params));
    }


    /**
     * Parent function for seach method.
     *
     * @param $verName  The versioned name of the method requested
     * @param $version  The version number
     * @param $params   The params supplied
     *
     * @return array Search results
     */
    public function search()
    {
        $this->_context = array(
            'request' => array(
                '1' => array(
                    'q' => 'query',
                    't' => 'to',
                    'f' => 'from',
                    'p' => 'page',
                    'o' => 'options'
                )
            ),
            'response' => array(
                '1' => array(

                )
            )
        );

        $request = func_get_args();
        $verName = $request[0];
        $version = $request[1];
        $params = $request[2];
        $request = $this->_minifyExpand($this->_context['request'][$version], $params);

        return call_user_func_array(array($this, $verName), array($request));
    }


    /**
     * Parent function for sentence method
     *
     * @param $jsonArray array JSON request
     *
     * @return array Sentences
     */
    public function getSentenceDetails($jsonArray)
    {
    }


    /**
     * Parent function for comment method
     *
     * @param $jsonArray array JSON request
     *
     * @return array A single comment
     */
    public function getComments($jsonArray)
    {
    }


    /**
     * Parent function for user profile method
     *
     * @param $jsonArray array JSON request
     *
     * @return array Details of a single user
     */
    public function getUserProfile($jsonArray)
    {
    }


    /**
     * Parent function for users method
     *
     * @param $jsonArray array JSON request
     *
     * @return array A List of users
     */
    public function getUsers($jsonArray)
    {
    }


    /**
     * Parent function for search users method
     *
     * @param $jsonArray array JSON request
     *
     * @return array A List of users
     */
    public function searchUsers($jsonArray)
    {
    }


    /**
     * Parent function for fetch wall method
     *
     * @param $jsonArray array JSON request
     *
     * @return array Wall messages with reply structure
     */
    public function fetchWall($jsonArray)
    {
    }


    /**
     * Parent function for fetch wall thread method
     *
     * @param $jsonArray array JSON request
     *
     * @return array Wall messages with reply structure
     */
    public function fetchWallThread($jsonArray)
    {
    }


    /**
     * Parent function for fetch wall replies method
     *
     * @param $jsonArray array JSON request
     *
     * @return array Wall messages with reply structure
     */
    public function fetchWallReplies($jsonArray)
    {
    }


    /**
     * Search sentences.
     * Don't call this function directly.
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
    private function _search_v1()
    {
        $this->cacheAction = true;
        $args = func_get_args();
        $args = $args[0];

        //Check to make sure all the arguments are supplied
        $requiredArgs = array('query', 'to', 'from', 'page', 'options');
        foreach ($requiredArgs as $key) {
            if (!array_key_exists($key, $args) || empty($args[$key])) {
                throw new Exception("Failed to specify $key argument.", 0);
            }
        }

        $this->loadModel('Sentence');
        $results = null;


        return $results;
    }


    /**
     * Find sentence
     * Don't call this function directly.
     *
     * @param  $id       int    Id of sentence
     * @param  $options  array  Options for query
     * @version 1
     *
     * @return array
     */
    private function _getSentenceDetails_v1()
    {
        $this->cacheAction = true;
        $results = null;
    }


    /**
     * Get list of comment
     * Don't call this function directly.
     *
     * @param  $id  array Id's of comments
     * @version 1
     *
     * @return array
     */
    private function _getComments_v1()
    {
        $this->cacheAction = true;
        $results = null;
    }


    /**
     * Find comment
     * Don't call this function directly.
     *
     * @param  $id  int Id of comment
     * @version 1
     *
     * @return array
     */
    private function _getCommentDetails_v1()
    {
        $this->cacheAction = true;
        $results = null;
    }


    /**
     * Get list of users or single user
     * Don't call this function directly.
     *
     * @param  $query   mixed   Either a search string or array of id's
     * @version 1
     *
     * @return array
     */
    private function _getUsers_v1()
    {
        $this->cacheAction = true;
        $results = null;
    }


    /**
     * Get a User's profile
     * Don't call this function directly.
     *
     * @param   $query   mixed   Either a search string or array of id's
     * @version 1
     *
     * @return array
     */
    private function _getUserDetails_v1()
    {
        $this->cacheAction = true;
        $results = null;
    }


    /**
     * Get wall messages
     * Don't call this function directly.
     *
     * @param  $page     array  Pagination options
     * @param  $options  array  Options for query
     * @version 1
     *
     * @return array
     */
    private function _fetchWall_v1()
    {
        $this->cacheAction = true;
        $results = null;
    }

    /**
     * Get message and replies
     * Don't call this function directly.
     *
     * @param   $id   Id of wall message
     * @version 1
     *
     * @return array
     */
    private function _fetchWallThread_v1()
    {
        $this->cacheAction = true;
        $results = null;
    }
}

?>