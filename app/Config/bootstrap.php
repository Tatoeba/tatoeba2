<?php
/* SVN FILE: $Id: bootstrap.php 6311 2008-01-02 06:33:52Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.10.8.2117
 * @version			$Revision: 6311 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-01 22:33:52 -0800 (Tue, 01 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */

/* Support gettext contexts
 * Inspired by http://php.net/manual/en/book.gettext.php#89975
 */
if (!function_exists('__p')) {
    function __p($context, $msgid, $return = true) {
         $contextMsgid = "{$context}\004{$msgid}";
         $translation = __($contextMsgid);
         $result = ($translation == $contextMsgid) ? $msgid : $translation;
         if ($return)
             return $result;
         else
             echo $result;
    }
}

if (!function_exists('__np')) {
    function __np($context, $singular, $plural, $count, $return = false) {
         $contextSingular = "{$context}\004{$singular}";
         $contextPlural = "{$context}\004{$plural}";
         $translation = __n($contextSingular, $contextPlural, $count);
         if ($translation == $contextSingular)
             $result = $singular;
         elseif ($translation == $contextPlural)
             $result = $plural;
         else
             $result = $translation;
         if ($return)
             return $result;
         else
             echo $result;
    }
}

/**
 * Printf-like function that supports:
 *   {n} params, n integer starting at zero
 *   {n.key} params passing array('key' => 'string')
 *   {n.key} params passing '; key: string; ...' strings
 *   {key.subkey} params passing array('key' => '; subkey: string; ...')
 *   if n is not specified, it is assumed starting at zero
 *
 * If a key is not found within the '; ...' list, it takes the first one
 *
 * See app/tests/cases/bootstrap.test.php for more infos.
 */
if (!function_exists('format')) {
    function format() {
        $args = func_get_args();
        $format = array_shift($args);
        if (count($args) && is_array($args[0]))
            $args = $args[0];

        return preg_replace_callback('/\{([^}.]+)?(\.([^}]+))?\}/', function($matches) use($args) {
            static $i = 0;
            $key    = isset($matches[1]) && $matches[1] != '' ? $matches[1] : $i++;
            $subkey = isset($matches[3]) && $matches[3] != '' ? $matches[3] : null;
            $res = '';
            if (array_key_exists($key, $args)) {
                $res = $args[$key];
                $list = __format_decompose_list($res);
                if (is_array($list)) {
                    reset($list);
                    if (!$subkey || !array_key_exists($subkey, $list))
                        $subkey = key($list);
                    $res = array_key_exists($subkey, $list) ? $list[$subkey] : '';
                }
            }
            return $res;
        }, $format);
    }
}
if (!function_exists('__format_decompose_list')) {
    function __format_decompose_list($string) {
        $result = $string;
        if ($string[0] == ';') {
            $list = explode(';', $string);
            $result = array();
            array_shift($list);
            foreach ($list as $kv_str) {
                $keyvalue = explode(':', $kv_str, 2);
                if (count($keyvalue) == 2) {
                    $result[trim($keyvalue[0])] = trim($keyvalue[1]);
                }
            }
        }
        return $result;
    }
}

?>
