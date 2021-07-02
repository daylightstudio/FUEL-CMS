<?php

/**
 * HTML 5.2 Ruby markup
 * https://html.spec.whatwg.org/multipage/text-level-semantics.html#the-ruby-element
 *
 * Note: {@link HTMLPurifier_HTMLModule_Ruby} implementation is based on
 * XHTML 1.1 Ruby Annotation module which differs from HTML5 spec.
 */
class HTMLPurifier_HTMLModule_HTML5_Ruby extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'HTML5_Ruby';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $this->addElement(
            'ruby',
            'Inline',
            'Custom: ((rb | Inline | #PCDATA)*, (rt | (rp, rt, rp) | rtc))+',
            'Common'
        );
        $this->addElement('rtc', false, 'Custom: (rt | rp | Inline | #PCDATA)*', 'Common');
        $this->addElement('rb', false, 'Custom: (Inline | #PCDATA)*', 'Common');
        $this->addElement('rt', false, 'Custom: (Inline | #PCDATA)*', 'Common');
        $this->addElement('rp', false, 'Custom: (Inline | #PCDATA)*', 'Common');

        // <ruby> elements can be nested as children of <rtc>, <rb>, <rt> and <rp>
        // https://www.w3.org/TR/2014/NOTE-html-ruby-extensions-20140204/#changes-compared-to-the-current-ruby-model
    }
}
