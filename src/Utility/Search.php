<?php
namespace app\Utility;

class Search {
    static function exactSearchQuery($text)
    {
        $escaped = Search::escapeString($text);
        return '="'.$escaped.'"';
    }

    static function escapeString($string)
	{
		$from = array('\\', '(', ')', '|', '-', '!', '@', '~', '"', '&', '/', '^', '$', '=');
		$to   = array('\\\\', '\(', '\)', '\|', '\-', '\!', '\@', '\~', '\"', '\&', '\/', '\^', '\$', '\=' );

		return str_replace($from, $to, $string);
	}
}
