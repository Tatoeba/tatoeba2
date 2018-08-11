<?php

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