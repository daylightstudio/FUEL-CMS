<?php

/**
 * A "safe" script module. No inline JS is allowed, and pointed to JS
 * files must match whitelist.
 */
class HTMLPurifier_HTMLModule_HTML5_SafeScripting extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'HTML5_SafeScripting';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        // These definitions are not intrinsically safe: the attribute transforms
        // are a vital part of ensuring safety.

        $allowed = $config->get('HTML.SafeScripting');

        $scriptContents = new HTMLPurifier_ChildDef_HTML5_Script();
        $scriptContents->allow_children = false;

        $script = $this->addElement(
            'script',
            'Inline',
            $scriptContents,
            null,
            array(
                'src' => new HTMLPurifier_AttrDef_Enum(array_keys($allowed), true),
                'type' => new HTMLPurifier_AttrDef_Enum(array(
                    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#textjavascript
                    'text/javascript',
                )),
                'async' => new HTMLPurifier_AttrDef_HTML_Bool2(),
                'defer' => new HTMLPurifier_AttrDef_HTML_Bool2(),
                'charset' => 'Enum#utf-8',
            )
        );

        $script->attr_transform_pre[] = new HTMLPurifier_AttrTransform_HTML5_Script();
    }
}
