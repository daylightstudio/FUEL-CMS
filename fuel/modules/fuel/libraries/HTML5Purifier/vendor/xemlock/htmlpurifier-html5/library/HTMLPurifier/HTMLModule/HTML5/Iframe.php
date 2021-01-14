<?php

/**
 * HTML5 compliant replacement for {@link HTMLPurifier_HTMLModule_Iframe}
 *
 * This module is not considered safe unless an Iframe whitelisting mechanism
 * is specified. Currently, the only such mechanism is %URL.SafeIframeRegexp
 */
class HTMLPurifier_HTMLModule_HTML5_Iframe extends HTMLPurifier_HTMLModule
{
    public $name = 'HTML5_Iframe';

    /**
     * @type bool
     */
    public $safe = false;

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        if ($config->get('HTML.SafeIframe')) {
            $this->safe = true;
        }

        // HTML Living Standard does not allow content in iframes, whereas W3C
        // spec does. On the other hand W3C validator follows WHATWG spec.
        // See:
        // - https://html.spec.whatwg.org/multipage/iframe-embed-object.html#the-iframe-element
        // - https://www.w3.org/TR/html52/semantics-embedded-content.html#the-iframe-element
        // - https://www.w3.org/TR/html50/embedded-content-0.html#the-iframe-element

        // type must not be 'empty', otherwise <iframe> will not have an end tag
        $iframeContents = new HTMLPurifier_ChildDef_Empty();
        $iframeContents->type = 'iframe';

        $iframe = $this->addElement(
            'iframe',
            'Inline',
            $iframeContents,
            'Common',
            array(
                'src'    => 'URI#embedded',
                'width'  => 'Length',
                'height' => 'Length',
                'name'   => 'ID',
                // other attributes that are present in HTML4 / XHTML spec were
                // declared as non-conforming, and as such are not included here
                // https://www.w3.org/TR/2016/WD-html52-20161206/obsolete.html#non-conforming-features
            )
        );

        if (isset($config->def->info['HTML.IframeAllowFullscreen']) &&
            $config->get('HTML.IframeAllowFullscreen')
        ) {
            $iframe->attr['allowfullscreen'] = 'Bool#allowfullscreen';
        }
    }
}
