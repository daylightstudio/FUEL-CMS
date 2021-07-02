<?php

/*
 * HTML5 Scripting
 *
 * WARNING: THIS MODULE IS EXTREMELY DANGEROUS AS IT ENABLES INLINE SCRIPTING
 * INSIDE HTML PURIFIER DOCUMENTS. USE ONLY WITH TRUSTED USER INPUT!!!
 *
 * https://www.w3.org/TR/html50/scripting-1.html
 */
class HTMLPurifier_HTMLModule_HTML5_Scripting extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'HTML5_Scripting';

    /**
     * @type bool
     */
    public $safe = false;

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $noscript = $this->addElement('noscript', 'Flow', 'Required: Flow | #PCDATA', 'Common');
        $noscript->excludes = array('noscript' => true);
        $this->addElementToContentSet('noscript', 'Inline');

        $scriptContents = new HTMLPurifier_ChildDef_HTML5_Script();
        $script = $this->addElement('script', 'Flow', $scriptContents, null, array(
            'src' => new HTMLPurifier_AttrDef_URI(true),
            'type' => new HTMLPurifier_AttrDef_Enum(array(
                // https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types#textjavascript
                'text/javascript',
            )),
            'async' => new HTMLPurifier_AttrDef_HTML_Bool2(),
            'defer' => new HTMLPurifier_AttrDef_HTML_Bool2(),
            // If present, its value must be an ASCII case-insensitive match for "utf-8"
            // Deprecated: https://html.spec.whatwg.org/multipage/scripting.html#the-script-element
            'charset' => 'Enum#utf-8',
        ));
        $this->addElementToContentSet('script', 'Inline');

        $script->attr_transform_pre[] = new HTMLPurifier_AttrTransform_HTML5_Script();
    }
}
