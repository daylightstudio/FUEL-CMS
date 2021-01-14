<?php

/**
 * HTML5 extension to {@link HTMLPurifier_HTMLModule_List}
 *
 * @property HTMLPurifier_ElementDef[] $info
 */
class HTMLPurifier_HTMLModule_HTML5_List extends HTMLPurifier_HTMLModule_List
{
    /**
     * @type string
     */
    public $name = 'HTML5_List';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        parent::setup($config);

        // https://html.spec.whatwg.org/multipage/grouping-content.html#the-ol-element
        $ol = $this->info['ol'];
        $ol->attr['reversed'] = 'Bool#reversed';

        // Attributes that were deprecated in HTML4, but reintroduced in HTML5
        $ol->attr['start'] = new HTMLPurifier_AttrDef_Integer();
        $ol->attr['type'] = 'Enum#s:1,a,A,i,I';
    }
}
