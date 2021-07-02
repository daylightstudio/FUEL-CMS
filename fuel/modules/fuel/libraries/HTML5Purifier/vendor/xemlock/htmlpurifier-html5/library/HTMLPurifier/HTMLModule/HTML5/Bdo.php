<?php

/**
 * HTML5 Bi-directional text
 */
class HTMLPurifier_HTMLModule_HTML5_Bdo extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'HTML5_Bdo';

    /**
     * @type array
     */
    public $attr_collections = array(
        'I18N' => array(
            'dir' => 'Enum#ltr,rtl,auto',
        ),
    );

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        // Bidirectional Text Override element
        // https://www.w3.org/TR/html50/text-level-semantics.html#the-bdo-element
        $bdo = $this->addElement(
            'bdo',
            'Inline',
            'Inline',
            array('Core', 'Lang'),
            array(
                'dir' => 'Enum#ltr,rtl', // required, the 'auto' value must not be specified
            )
        );
        $bdo->attr_transform_post[] = new HTMLPurifier_AttrTransform_BdoDir();

        // Bidirectional Isolate element
        // https://www.w3.org/TR/html50/text-level-semantics.html#the-bdi-element
        $this->addElement('bdi', 'Inline', 'Inline', 'Common');
    }
}
