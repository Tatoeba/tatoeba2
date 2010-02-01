<?php
/**
 * MyStandard Coding Standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Allan SIMON <allan.simon@supinfo.com>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   SVN: $Id: coding-standard-tutorial.xml,v 1.9 2008-10-09 15:16:47 cweiske Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * MyStandard Coding Standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Allan SIMON <allan.simon@supinfo.com>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_Tatoeba_TatoebaCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{

    public function getIncludedSniffs()
    {
        return array(

            'Squiz/Sniffs/Scope/MethodScopeSniff.php',
            'Squiz/Sniffs/Scope/MemberVarScopeSniff.php',
            'PEAR',
            'Squiz/Sniffs/CSS',
            'Squiz/Sniffs/PHP/DisallowSizeFunctionsInLoopsSniff.php',
            'Squiz/Sniffs/PHP/EmbeddedPhpSniff.php',
            'Squiz/Sniffs/PHP/NonExecutableCodeSniff.php'

        );

    }

    public function getExcludedSniffs()
    {
        return array(

            'PEAR/Sniffs/NamingConventions/ValidFunctionNameSniff.php',
            'PEAR/Sniffs/Files/LineLengthSniff.php',
            'PEAR/Sniffs/WhiteSpace/ScopeIndentSniff.php',

        );

    }



}//end class
?> 
