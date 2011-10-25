<?php
/**
 * This file provides a base class for messages from a W3C validator.
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
 * The message class holds a response from the W3 validator.
 * 
 * @category Services
 * @package  Services_W3C_HTMLValidator
 * @author   Brett Bieber <brett.bieber@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @link     http://pear.php.net/package/Services_W3C_HTMLValidator
 */ 
class Services_W3C_HTMLValidator_Message
{
    /**
     * line corresponding to the message
     * 
     * Within the source code of the validated document, refers to the line which
     * caused this message.
     * @var int
     */
    public $line;
    
    /**
     * column corresponding to the message
     * 
     * Within the source code of the validated document, refers to the column within
     * the line for the message.
     * @var int
     */
    public $col;
    
    /**
     * The actual message
     * @var string
     */
    public $message;
    
    /**
     * Unique ID for this message
     * 
     * not implemented yet. should be the number of the error, as addressed
     * internally by the validator
     * @var int
     */
    public $messageid;
    
    /**
     * Explanation for this message.
     * 
     * HTML snippet which describes the message, usually with information on
     * how to correct the problem.
     * @var string
     */
    public $explanation;
    
    /**
     * Source which caused the message.
     * 
     * the snippet of HTML code which invoked the message to give the 
     * context of the e
     * @var string
     */
    public $source;
    
    /**
     * Constructor for a response message
     *
     * @param object $node A dom document node.
     */
    function __construct($node = null)
    {
        if (isset($node)) {
            foreach (get_class_vars('Services_W3C_HTMLValidator_Message') as
                $var => $val) {
                $element = $node->getElementsByTagName($var);
                if ($element->length) {
                    $this->$var = $element->item(0)->nodeValue;
                }
            }
        }
    }
}

?>