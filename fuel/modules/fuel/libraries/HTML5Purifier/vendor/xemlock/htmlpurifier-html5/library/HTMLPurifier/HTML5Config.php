<?php

class HTMLPurifier_HTML5Config extends HTMLPurifier_Config
{
    const REVISION = 2019080701;

    /**
     * @param  string|array|HTMLPurifier_Config $config
     * @param  HTMLPurifier_ConfigSchema $schema OPTIONAL
     * @return HTMLPurifier_HTML5Config
     */
    public static function create($config, $schema = null)
    {
        if ($config instanceof HTMLPurifier_Config) {
            $schema = $config->def;
            $config = null;
        }

        if (!$schema instanceof HTMLPurifier_ConfigSchema) {
            $schema = HTMLPurifier_ConfigSchema::instance();
        }

        $configObj = new self($schema);
        $configObj->set('HTML.DefinitionID', __CLASS__);
        $configObj->set('HTML.DefinitionRev', self::REVISION);

        if (is_string($config)) {
            $configObj->loadIni($config);

        } elseif (is_array($config)) {
            $configObj->loadArray($config);
        }

        return $configObj;
    }

    /**
     * Creates a configuration object using the default config schema instance
     *
     * @return HTMLPurifier_HTML5Config
     */
    public static function createDefault()
    {
        return self::create(null);
    }

    /**
     * Creates a new config object that inherits from a previous one
     *
     * @param  HTMLPurifier_Config $config
     * @return HTMLPurifier_HTML5Config
     */
    public static function inherit(HTMLPurifier_Config $config)
    {
        return new self($config->def, $config->plist);
    }

    /**
     * @param HTMLPurifier_ConfigSchema $schema
     * @param HTMLPurifier_PropertyList $parent OPTIONAL
     */
    public function __construct(HTMLPurifier_ConfigSchema $schema, HTMLPurifier_PropertyList $parent = null)
    {
        // ensure 'HTML5' is among allowed 'HTML.Doctype' values
        $doctypeConfig = $schema->info['HTML.Doctype'];

        if (empty($doctypeConfig->allowed['HTML5'])) {
            $allowed = array_merge($doctypeConfig->allowed, array('HTML5' => true));
            $schema->addAllowedValues('HTML.Doctype', $allowed);
        }

        if (empty($schema->info['HTML.IframeAllowFullscreen'])) {
            $schema->add('HTML.IframeAllowFullscreen', false, 'bool', false);
        }

        parent::__construct($schema, $parent);

        $this->set('HTML.Doctype', 'HTML5');
        $this->set('Attr.ID.HTML5', true);
        $this->set('Output.CommentScriptContents', false);
    }

    public function getDefinition($type, $raw = false, $optimized = false)
    {
        // Setting HTML.* keys removes any previously instantiated HTML
        // definition object, so set up HTML5 definition as late as possible
        $needSetup = $type === 'HTML' && !isset($this->definitions[$type]);
        if ($needSetup) {
            if ($def = parent::getDefinition($type, true, true)) {
                /** @var HTMLPurifier_HTMLDefinition $def */
                HTMLPurifier_HTML5Definition::setupDefinition($def);
            }
        }
        return parent::getDefinition($type, $raw, $optimized);
    }
}
