<?php

/**
 * HTML5 compliant replacement for {@link HTMLPurifier_HTMLModule_Hypertext},
 * defining block-level hypertext links.
 */
class HTMLPurifier_HTMLModule_HTML5_Hypertext extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'HTML5_Hypertext';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        // https://html.spec.whatwg.org/dev/text-level-semantics.html#the-a-element
        $a = $this->addElement('a', 'Flow', 'Flow', 'Common', array(
            'download' => 'Text',
            'href'     => 'URI',
            'hreflang' => 'Text', // 'LanguageCode',
            'rel'      => new HTMLPurifier_AttrDef_HTML5_ARel(),
            'target'   => new HTMLPurifier_AttrDef_HTML_FrameTarget(),
            'type'     => 'Text',
        ));
        $a->excludes = array('a' => true);
        $this->addElementToContentSet('a', 'Inline');
    }
}
