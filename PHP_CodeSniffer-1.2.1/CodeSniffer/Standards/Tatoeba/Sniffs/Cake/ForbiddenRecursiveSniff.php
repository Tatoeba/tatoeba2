<?php
/**
 * Tatoeba_Sniffs_PHP_ForbiddenRecursive.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ForbiddenFunctionsSniff.php 290853 2009-11-17 03:17:17Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tatoeba_Sniffs_PHP_ForbiddenRecursiveSniff.
 *
 * Forbid use of "MyModel->recursive" 
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Tatoeba_Sniffs_Cake_ForbiddenRecursiveSniff implements PHP_CodeSniffer_Sniff
{



    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        //echo $prevToken;
        if ($tokens[$prevToken]['code'] !== T_OBJECT_OPERATOR ) {
            // Not a call to a PHP function.
            return;
        }

        // content is either a method or a public attribute
        $content = strtolower($tokens[$stackPtr]['content']);
        if (in_array($content, array('recursive')) === false) {
            return;
        }

        $error = "The use of recursive is forbidden";

        $phpcsFile->addError($error, $stackPtr);


    }//end process()


}//end class

?>
