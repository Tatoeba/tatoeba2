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
                'action1',
                'action2',
                'action3',
                'etc...'
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
        'action1' => array(
            'key' => 'value',
            'key' => 'value'
        ),
        'action2' => array(
            'key' => 'value',
            'key' => 'value'
        ),
        'action3' => array(
            'key' => 'value',
            'key' => 'value'
        ),
        'etc...'
    );
    
    /**
     * Minify function, compress data
     * 
     * @param string $context     The mapping context (the method name)
     * @param object $jsonObject  The JSON data to compress
     * 
     * @return object compressed JSON data
     */
    private function _minifyCompress($context, $jsonObject)
    {
        
    }
    
    /**
     * Minify function, expand data
     * 
     * @param string $context     The mapping context (the method name)
     * @param object $jsonObject  The JSON data to expand
     * 
     * @return expanded JSON data
     */
    private function _minifyExpand($context, $jsonObject)
    {
        
    }
    
    
    /**
     * Search sentences optionally with translations and comments
     * 
     * @param $jsonObject object JSON request
     * 
     * @return object Search results
     */
    public function search($jsonObject)
    {
        $this->cacheAction = true;
        $results = null;
        $jsonObject = $this->_minifyExpand("",$jsonObject);
    }
    
}

?>