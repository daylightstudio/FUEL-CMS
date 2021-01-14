<?php

class HTMLPurifier_ChildDef_HTML5_Details extends HTMLPurifier_ChildDef
{
    public $type = 'details';

    public $elements = array(
        'summary' => true,
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
        // if summary is not allowed, delete parent node
        if (!isset($config->getHTMLDefinition()->info['summary'])) {
            trigger_error("Cannot allow details without allowing summary", E_USER_WARNING);
            return false;
        }

        $summary = array();
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

        // Content model:
        // One summary element followed by flow content
        foreach ($children as $node) {
            if (!$summary && $node->name === 'summary') {
                $summary[] = $node;
                continue;
            }
            if ($node->name === 'summary') {
                // duplicated summary, add only its children
                $others = array_merge($others, (array) $node->children);
            } else {
                $others[] = $node;
            }
        }

        if (!$summary) {
            // remove empty <details> without <summary>
            if (!$others) {
                return false;
            }

            $summary[] = new HTMLPurifier_Node_Element('summary');
        }

        return array_merge($spaces, $summary, $others);
    }
}
