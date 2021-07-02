<?php

class HTMLPurifier_AttrTransform_HTML5_Script extends HTMLPurifier_AttrTransform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        // If 'src' is specified, it must be a valid non-empty URL potentially
        // surrounded by spaces.
        // If 'src' is present, regardless it's empty or not, script text is
        // ignored by browsers.
        if (isset($attr['src']) && trim($attr['src']) === '') {
            unset($attr['src']);
        }

        return $attr;
    }
}
