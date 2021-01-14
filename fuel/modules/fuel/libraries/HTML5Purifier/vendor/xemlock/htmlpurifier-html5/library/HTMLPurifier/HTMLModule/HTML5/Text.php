<?php

/**
 * Extension to {@link HTMLPurifier_HTMLModule_Text} defining HTML5 text-level
 * and grouping elements.
 */
class HTMLPurifier_HTMLModule_HTML5_Text extends HTMLPurifier_HTMLModule_Text
{
    /**
     * @type string
     */
    public $name = 'HTML5_Text';

    public $content_sets = array(
        'Flow' => 'Heading | Block | Inline | Sectioning'
    );

    /**
     * @param HTMLPurifier_Config $config
     * @throws HTMLPurifier_Exception
     */
    public function setup($config)
    {
        parent::setup($config);

        // http://developers.whatwg.org/sections.html
        $this->addElement('section', 'Sectioning', 'Flow', 'Common');
        $this->addElement('nav', 'Sectioning', 'Flow', 'Common');
        $this->addElement('article', 'Sectioning', 'Flow', 'Common');
        $this->addElement('aside', 'Sectioning', 'Flow', 'Common');

        // https://html.spec.whatwg.org/dev/sections.html#the-header-element
        $header = $this->addElement('header', 'Block', 'Flow', 'Common');
        $header->excludes = $this->makeLookup('header', 'footer', 'main');

        // https://html.spec.whatwg.org/dev/sections.html#the-footer-element
        $footer = $this->addElement('footer', 'Block', 'Flow', 'Common');
        $footer->excludes = $this->makeLookup('header', 'footer', 'main');

        // https://html.spec.whatwg.org/dev/sections.html#the-address-element
        $address = $this->addElement('address', 'Block', 'Flow', 'Common');
        $address->excludes = $this->makeLookup(
            // no heading content
            // https://html.spec.whatwg.org/dev/dom.html#heading-content
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hgroup',
            // no sectioning content
            // https://html.spec.whatwg.org/dev/dom.html#sectioning-content
            'article', 'aside', 'nav', 'section',
            // no header, footer and address
            'address', 'footer', 'header'
        );

        // https://html.spec.whatwg.org/dev/sections.html#the-hgroup-element
        $this->addElement('hgroup', 'Heading', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');

        // https://html.spec.whatwg.org/dev/grouping-content.html#the-main-element
        $this->addElement('main', 'Block', 'Flow', 'Common');

        // https://html.spec.whatwg.org/dev/grouping-content.html#the-figure-element
        $this->addElement('figure', 'Block', new HTMLPurifier_ChildDef_HTML5_Figure(), 'Common');
        $this->addElement('figcaption', false, 'Flow', 'Common');

        // https://html.spec.whatwg.org/multipage/grouping-content.html#the-blockquote-element
        $this->addElement('blockquote', 'Block', 'Flow', 'Common', array(
            'cite' => 'URI',
        ));

        // http://developers.whatwg.org/text-level-semantics.html
        $this->addElement('s', 'Inline', 'Inline', 'Common');
        $this->addElement('u', 'Inline', 'Inline', 'Common');
        $this->addElement('var', 'Inline', 'Inline', 'Common');
        $this->addElement('sub', 'Inline', 'Inline', 'Common');
        $this->addElement('sup', 'Inline', 'Inline', 'Common');
        $this->addElement('mark', 'Inline', 'Inline', 'Common');
        $this->addElement('wbr', 'Inline', 'Empty', 'Core');

        // https://html.spec.whatwg.org/dev/text-level-semantics.html#the-time-element
        // https://w3c.github.io/html-reference/datatypes.html#common.data.time-datetime-def
        // Composite attr def is sufficiently general to be used in non-CSS contexts
        $timeDatetime = new HTMLPurifier_AttrDef_CSS_Composite(array(
            new HTMLPurifier_AttrDef_HTML5_Datetime(),
            new HTMLPurifier_AttrDef_HTML5_YearlessDate(),
            new HTMLPurifier_AttrDef_HTML5_Week(),
            new HTMLPurifier_AttrDef_HTML5_Duration(),
        ));
        $timeContents = new HTMLPurifier_ChildDef_HTML5_Time();
        $this->addElement('time', 'Inline', $timeContents, 'Common', array(
            'datetime' => $timeDatetime,
        ));
    }
}
