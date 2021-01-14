<?php

/**
 * HTML5 Multimedia and embedded content
 *
 * https://html.spec.whatwg.org/dev/media.html
 * https://html.spec.whatwg.org/dev/embedded-content.html
 */
class HTMLPurifier_HTMLModule_HTML5_Media extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'HTML5_Media';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $mediaContent = new HTMLPurifier_ChildDef_HTML5_Media();

        // https://html.spec.whatwg.org/dev/media.html#the-video-element
        $this->addElement('video', 'Flow', $mediaContent, 'Common', array(
            'controls' => 'Bool',
            'height'   => 'Length',
            'poster'   => 'URI',
            'preload'  => 'Enum#auto,metadata,none',
            'src'      => 'URI',
            'width'    => 'Length',
        ));
        $this->addElementToContentSet('video', 'Inline');

        // https://html.spec.whatwg.org/dev/media.html#the-audio-element
        $this->addElement('audio', 'Flow', $mediaContent, 'Common', array(
            'controls' => 'Bool',
            'preload'  => 'Enum#auto,metadata,none',
            'src'      => 'URI',
        ));
        $this->addElementToContentSet('audio', 'Inline');

        // https://html.spec.whatwg.org/dev/embedded-content.html#the-source-element
        $this->addElement('source', false, 'Empty', 'Common', array(
            'media'  => 'Text',
            'sizes'  => 'Text',
            'src'    => 'URI',
            'srcset' => 'Text',
            'type'   => 'Text',
        ));

        // https://html.spec.whatwg.org/dev/media.html#the-track-element
        $this->addElement('track', false, 'Empty', 'Common', array(
            'kind'    => 'Enum#captions,chapters,descriptions,metadata,subtitles',
            'src'     => 'URI',
            'srclang' => 'Text',
            'label'   => 'Text',
            'default' => 'Bool',
        ));

        // https://html.spec.whatwg.org/dev/embedded-content.html#the-picture-element
        $this->addElement('picture', 'Flow', new HTMLPurifier_ChildDef_HTML5_Picture(), 'Common');
        $this->addElementToContentSet('picture', 'Inline');

        // https://html.spec.whatwg.org/dev/embedded-content.html#the-img-element
        $img = $this->addBlankElement('img');
        $img->attr = array(
            'srcset' => 'Text',
            'sizes'  => 'Text',
        );
    }
}
