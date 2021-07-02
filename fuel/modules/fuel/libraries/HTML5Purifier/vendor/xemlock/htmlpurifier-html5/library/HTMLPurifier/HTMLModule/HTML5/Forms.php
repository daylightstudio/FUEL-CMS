<?php

/**
 * HTML5 additions to built-in Forms module
 *
 * This module is marked as safe to support static elements like <progress>
 * out of the box. Only elements inherited from parent module are unsafe,
 * and enabled conditionally with %HTML.Trusted flag.
 */
class HTMLPurifier_HTMLModule_HTML5_Forms extends HTMLPurifier_HTMLModule_Forms
{
    public $name = 'HTML5_Forms';

    public $safe = true;

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        if ($config->get('HTML.Trusted')) {
            parent::setup($config);

            // https://html.spec.whatwg.org/multipage/forms.html#the-form-element
            $form = $this->addElement(
                'form',
                'Form',
                'Flow',
                'Common',
                array(
                    'accept-charset' => 'Charsets',
                    'action'  => 'URI',
                    'method'  => 'Enum#get,post',
                    'enctype' => 'Enum#application/x-www-form-urlencoded,multipart/form-data,text/plain',
                    'target'  => new HTMLPurifier_AttrDef_HTML_FrameTarget(),
                )
            );
            $form->excludes = array('form' => true);

            $this->addElement(
                'fieldset',
                'Form',
                new HTMLPurifier_ChildDef_HTML5_Fieldset(),
                'Common',
                array(
                    'name'     => 'CDATA',
                    'disabled' => 'Bool#disabled',
                    // 'form' => 'IDREF', // IDREF not implemented, cannot allow
                )
            );
        }

        // https://html.spec.whatwg.org/dev/form-elements.html#the-progress-element
        $progress = $this->addElement(
            'progress',
            'Flow',
            'Inline',
            'Common',
            array(
                'value' => 'Float#min:0',
                'max'   => 'Float#min:0',
            )
        );
        $progress->excludes = array('progress' => true);
        $this->addElementToContentSet('progress', 'Inline');

        $progress->attr_transform_post[] = new HTMLPurifier_AttrTransform_HTML5_Progress();
    }
}
