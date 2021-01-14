<?php

class HTMLPurifier_AttrTransform_HTML5_Dialog extends HTMLPurifier_AttrTransform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        // The tabindex attribute must not be specified on dialog elements.
        // https://html.spec.whatwg.org/dev/interactive-elements.html#the-dialog-element
        unset($attr['tabindex']);

        return $attr;
    }
}
