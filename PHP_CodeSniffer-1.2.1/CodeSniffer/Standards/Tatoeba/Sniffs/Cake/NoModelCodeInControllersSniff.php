<?php
/**
 * Tatoeba_Sniffs_Cake_NoModelCodeInControllersSniff.
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
 * Tatoeba_Sniffs_Cake_NoModelCodeInControllersSniff
 *
 * Forbid use of this->MyModel->read , find ,findBy
 * from the controller, as they should be encapsulated in the model
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
class Tatoeba_Sniffs_Cake_NoModelCodeInControllersSniff implements PHP_CodeSniffer_Sniff
{

    protected $forbiddenMethods = array (
        'read',
        'unbindmodel',
        'habtmadd',
        'habtmdelete',
    ); 


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


        $prevClass = $phpcsFile->findPrevious(T_CLASS,  ($stackPtr - 1));
        $classNameToken = $phpcsFile->findNext(T_WHITESPACE, ($prevClass + 1), null, true);

        $className = $tokens[$classNameToken]['content'];

        if (preg_match('|Controller$|', $className) === 0) {
            // Not in a Controller Class
            return;
        }

        if ($tokens[$prevToken]['code'] !== T_OBJECT_OPERATOR ) {
            // Not a call to a PHP method.
            return;
        }

        if ($tokens[$nextToken]['content'] !== '(' ) {
            // Not a call to a PHP method.
            return;
        }

        // content is a method
        $content = strtolower($tokens[$stackPtr]['content']);
        if (in_array($content, $this->forbiddenMethods ) === false) {
            if (preg_match('|^findBy|', $content) === 0) {
                // Not in a Controller Class
                return;
            }
        }

        
        $error = "The use of ::$content() in the controller is forbidden";

        $phpcsFile->addError($error, $stackPtr);


    }//end process()


}//end class

?>
