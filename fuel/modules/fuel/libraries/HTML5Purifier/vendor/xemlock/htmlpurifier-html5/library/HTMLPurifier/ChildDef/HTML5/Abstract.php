<?php

abstract class HTMLPurifier_ChildDef_HTML5_Abstract extends HTMLPurifier_ChildDef
{
    /**
     * @var array
     */
    protected $allowedElements = array();

    /**
     * @var boolean
     */
    protected $init = false;

    /**
     * @param string $type
     * @throws HTMLPurifier_Exception
     */
    public function __construct($type = null)
    {
        if ($type) {
            $this->type = $type;
        }

        // Ensure that the type property is not empty, otherwise the element
        // will be treated as having an Empty content model (closing tag will
        // be omitted) if no children are present.
        if (empty($this->type)) {
            throw new HTMLPurifier_Exception("The 'type' property is not initialized");
        }
    }

    /**
     * @param HTMLPurifier_Config $config
     * @return array
     */
    public function getAllowedElements($config)
    {
        $this->init($config);
        return $this->allowedElements;
    }

    /**
     * @param HTMLPurifier_Config $config
     * @return void
     */
    protected function init(HTMLPurifier_Config $config)
    {
        if ($this->init) {
            return;
        }

        $def = $config->getHTMLDefinition();

        $elements = array();
        foreach ($this->elements as $name => $_) {
            if (is_int($name)) {
                $name = $_;
            }
            if (isset($def->info_content_sets[$name])) {
                $elements = array_merge($elements, $def->info_content_sets[$name]);
            } else {
                $elements[$name] = true;
            }
        }
        $this->allowedElements = $elements;

        $this->init = true;
    }
}
