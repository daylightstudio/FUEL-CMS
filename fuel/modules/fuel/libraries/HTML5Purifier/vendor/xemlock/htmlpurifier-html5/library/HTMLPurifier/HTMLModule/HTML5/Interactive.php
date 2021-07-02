<?php

/**
 * HTML5 Interactive elements module
 * https://html.spec.whatwg.org/dev/interactive-elements.html
 */
class HTMLPurifier_HTMLModule_HTML5_Interactive extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'HTML5_Interactive';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        // https://html.spec.whatwg.org/dev/interactive-elements.html#the-details-element
        $this->addElement('details', 'Flow', new HTMLPurifier_ChildDef_HTML5_Details(), 'Common', array(
            'open' => 'Bool',
        ));

        // https://html.spec.whatwg.org/dev/interactive-elements.html#the-summary-element
        $this->addElement('summary', false, 'Flow', 'Common');

        // https://html.spec.whatwg.org/dev/interactive-elements.html#the-dialog-element
        $dialog = $this->addElement('dialog', 'Flow', 'Flow', 'Common', array(
            'open' => 'Bool',
        ));

        $dialog->attr_transform_pre[] = new HTMLPurifier_AttrTransform_HTML5_Dialog();
    }
}
