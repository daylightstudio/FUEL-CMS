<?php

class HTMLPurifier_ChildDef_HTML5_Media extends HTMLPurifier_ChildDef
{
    public $type = 'media';

    public $elements = array(
        'source' => true,
        'track'  => true,
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
            unset(
                $this->allowedElements['audio'],
                $this->allowedElements['video']
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
        // Content model:
        // If the element has a src attribute: zero or more track elements,
        // then transparent, but with no media element descendants.
        // If the element does not have a src attribute: zero or more source
        // elements, then zero or more track elements, then transparent, but
        // with no media element descendants.

        $allowSource = isset($config->getHTMLDefinition()->info['source']);
        $allowTrack = isset($config->getHTMLDefinition()->info['track']);

        $sources = array();
        $tracks = array();
        $content = array();

        foreach ($children as $node) {
            switch ($node->name) {
                case 'source':
                    if ($allowSource) {
                        $sources[] = $node;
                    }
                    break;

                case 'track':
                    if ($allowTrack) {
                        $tracks[] = $node;
                    }
                    break;

                default:
                    $content[] = $node;
                    break;
            }
        }

        $currentNode = $context->get('CurrentNode');
        $hasSrcAttr = $currentNode instanceof HTMLPurifier_Node_Element && isset($currentNode->attr['src']);

        if ($hasSrcAttr) {
            $result = array_merge($tracks, $content);
        } else {
            $result = array_merge($sources, $tracks, $content);
        }

        if (empty($result) && !$hasSrcAttr) {
            return false;
        }

        return $result;
    }
}
