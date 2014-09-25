<?php
/**
 * Tatoeba_Sniffs_CTP_ScopeIndentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ScopeIndentSniff.php 254092 2008-03-03 02:51:39Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('Generic_Sniffs_WhiteSpace_ScopeIndentSniff', true) === false) {
    $error = 'Class Generic_Sniffs_WhiteSpace_ScopeIndentSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Tatoeba_Sniffs_CTP_ScopeIndentSniff.
 *
 * Checks that control structures are structured correctly, and their content
 * is indented correctly.
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
class Tatoeba_Sniffs_CTP_ScopeIndentSniff extends Generic_Sniffs_WhiteSpace_ScopeIndentSniff
{
    /**
     * The number of spaces used to indent the containing <?php
     *
     * @var int
     */
    public  $indentOfOpenTag = 1;


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $fileName = $phpcsFile->getFilename();

        if (preg_match('|ctp$|', $fileName) === 0) {
            parent::process($phpcsFile, $stackPtr);
        } else {
            $tokens = $phpcsFile->getTokens();
            $prevOpenTag = $phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1));
            //var_dump($tokens[$prevOpenTag]);
            if ( $tokens[$prevOpenTag]['column'] === 1 ) {
                $this->indentOfOpenTag = 1;
            } else {
                $this->indentOfOpenTag = $tokens[$prevOpenTag]['column']  ;
            }

            parent::process($phpcsFile, $stackPtr);

        }
    }

    /**
     * Calculates the expected indent of a token.
     *
     * @param array $tokens   The stack of tokens for this file.
     * @param int   $stackPtr The position of the token to get indent for.
     *
     * @return int
     */
    protected function calculateExpectedIndent(array $tokens, $stackPtr)
    {
        $conditionStack = array();

        // Empty conditions array (top level structure).
        if (empty($tokens[$stackPtr]['conditions']) === true) {
            return  $this->indentOfOpenTag ;
        }

        $tokenConditions = $tokens[$stackPtr]['conditions'];
        foreach ($tokenConditions as $id => $condition) {
            // If it's an indenting scope ie. it's not in our array of
            // scopes that don't indent, add it to our condition stack.
            if (in_array($condition, $this->nonIndentingScopes) === false) {
                $conditionStack[$id] = $condition;
            }
        }

        return  ( (count($conditionStack)) *$this->indent ) + $this->indentOfOpenTag;

    }//end calculateExpectedIndent()

}//end class

?>
