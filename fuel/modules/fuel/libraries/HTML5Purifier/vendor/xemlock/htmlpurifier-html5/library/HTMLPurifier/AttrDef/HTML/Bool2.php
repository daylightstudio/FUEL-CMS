<?php

/**
 * Validates a boolean attribute
 *
 * HTMLPurifier 4.6.0 has broken support for boolean attributes, as reported
 * in: http://htmlpurifier.org/phorum/read.php?3,7631,7631

 * This issue has almost been fixed in 4.7.0, boolean attributes are properly
 * parsed, but their values are not validated.
 */
class HTMLPurifier_AttrDef_HTML_Bool2 extends HTMLPurifier_AttrDef_HTML_Bool
{
    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool
     */
    public function validate($string, $config, $context)
    {
        $name = strlen($this->name) ? $this->name : $context->get('CurrentAttr');
        // boolean attribute validates if its value is either empty
        // or case-insensitively equal to attribute name
        return $string === '' || strcasecmp($name, $string) === 0;
    }

    /**
     * @param string $string Name of attribute
     * @return HTMLPurifier_AttrDef_HTML_Bool2
     */
    public function make($string)
    {
        return new self($string);
    }
}

// vim: et sw=4 sts=4
