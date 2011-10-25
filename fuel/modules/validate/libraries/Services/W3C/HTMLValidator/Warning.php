<?php
/**
 * This file contains a simple class for warning messages returned from a validator.
 * 
 * PHP versions 5
 * 
 * @category Services
 * @package  Services_W3C_HTMLValidator
 * @author   Brett Bieber <brett.bieber@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @version  CVS: $id$
 * @link     http://pear.php.net/package/Services_W3C_HTMLValidator
 * @since    File available since Release 0.2.0
 */

/**
 * Extends from the Message class
 */
require_once 'Services/W3C/HTMLValidator/Message.php';

/**
 * The message class holds an error response from the W3 validator.
 *
 * @category Services
 * @package  Services_W3C_HTMLValidator
 * @author   Brett Bieber <brett.bieber@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @link     http://pear.php.net/package/Services_W3C_HTMLValidator
 */
class Services_W3C_HTMLValidator_Warning extends Services_W3C_HTMLValidator_Message
{
    
}

?>