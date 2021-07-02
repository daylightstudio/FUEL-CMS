<?php

class HTMLPurifier_ChildDef_HTML5_Fieldset extends HTMLPurifier_ChildDef
{
    public $type = 'fieldset';

    public $elements = array(
        'legend' => true,
    );

    protected $allowedElements;

    /**
     * @param HTMLPurifier_Config $config
     * @return array
     */
    public function getAllowedElements($config)
    {
        if (null === $this->allowedElements) {
            // Add Flow content to allowed elements to prevent MakeWellFormed
            // strategy moving them outside details element
            $def = $config->getHTMLDefinition();

            $this->allowedElements = array_merge(
                $def->info_content_sets['Flow'],
                $this->elements
            );
        }
        return $this->allowedElements;
    }

    /**
     * @param array $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array|bool
     */
    public function validateChildren($children, $config, $context)
    {
        $children = (array) $children;

        // Content model:
        // An optional <legend> element, followed by flow content.

        // Only one legend element should occur in the content and if present
        // should only be preceded by whitespace.
        // https://www.w3.org/TR/xhtml1/dtds.html

        $legend = array();
        $spaces = array();
        $others = array();

        while ($children) {
            $child = reset($children);
            if ($child instanceof HTMLPurifier_Node_Text && $child->is_whitespace) {
                $spaces[] = array_shift($children);
            } else {
                break;
            }
        }

        foreach ($children as $node) {
            if (!$legend && $node->name === 'legend') {
                $legend[] = $node;
                continue;
            }
            if ($node->name === 'legend') {
                // duplicated <legend>, add only its children
                $others = array_merge($others, (array) $node->children);
            } else {
                $others[] = $node;
            }
        }

        return array_merge($spaces, $legend, $others);
    }
}
