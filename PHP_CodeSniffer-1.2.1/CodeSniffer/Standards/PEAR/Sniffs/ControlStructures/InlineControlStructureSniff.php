<?php
/**
 * PEAR_Sniffs_ControlStructures_InlineControlStructureSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: InlineControlStructureSniff.php 258843 2008-05-01 00:49:32Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('Generic_Sniffs_ControlStructures_InlineControlStructureSniff', true) === false) {
    $error = 'Class Generic_Sniffs_ControlStructures_InlineControlStructureSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * PEAR_Sniffs_ControlStructures_InlineControlStructureSniff.
 *
 * Verifies that inline control statements are not present.
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
class PEAR_Sniffs_ControlStructures_InlineControlStructureSniff extends Generic_Sniffs_ControlStructures_InlineControlStructureSniff
{

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    protected $error = false;

}//end class

?>
