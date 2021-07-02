<?php

class HTMLPurifier_ChildDef_HTML5_Figure extends HTMLPurifier_ChildDef
{
    public $type = 'figure';

    public $elements = array(
        'figcaption' => true,
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
            // strategy moving them outside 'figure' element
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
        $hasFigcaption = false;
        $figcaptionPos = -1;

        $result = array();

        // Content model:
        // Either: one figcaption element followed by flow content.
        // Or: flow content followed by one figcaption element.
        // Or: flow content.

        // Scan through children, accept at most one figcaption.
        foreach ($children as $node) {
            if ($node->name === 'figcaption') {
                if (!$hasFigcaption) {
                    $hasFigcaption = true;
                    $figcaptionPos = count($result);
                    $result[] = $node;
                }
                continue;
            }

            // Figcaption must be a first or last child of a figure element.
            // If it's not first, then we ignore all siblings that come after.
            if ($hasFigcaption && $figcaptionPos > 0) {
                break;
            }

            $result[] = $node;
        }

        return $result;
    }
}
