<?php

/**
 * Definition for <time> element contents
 *
 * As a side effect for child validation it ensures that 'datetime' attribute
 * is present if text content of the children is not a valid datetime string.
 *
 * @see https://html.spec.whatwg.org/multipage/text-level-semantics.html#the-time-element
 */
class HTMLPurifier_ChildDef_HTML5_Time extends HTMLPurifier_ChildDef_HTML5_Abstract
{
    public $type = 'time';

    public $allow_empty = true;

    public $elements = array(
        'Inline' => true,
    );

    /**
     * @param HTMLPurifier_Node[] $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|HTMLPurifier_Node[]
     */
    public function validateChildren($children, $config, $context)
    {
        $currentNode = $context->get('CurrentNode', true);

        if ($currentNode instanceof HTMLPurifier_Node_Element) {
            // Unfortunately at this point invalid 'datetime' attribute is not
            // yet removed, so we need to validate it here

            /** @var HTMLPurifier_AttrDef $attr */
            $attr = $config->getHTMLDefinition()->info['time']->attr['datetime'];

            $datetime = isset($currentNode->attr['datetime'])
                ? $attr->validate($currentNode->attr['datetime'], $config, $context)
                : false;

            // If datetime attribute is invalid, we need to check whether element's
            // contents are a valid datetime string. If not, add a dummy datetime
            // attribute with UNIX epoch date, to satisfy the spec requirements.
            // This can't be done in the AttrTransform step, because CurrentNode is
            // not available there.

            if ($datetime === false) {
                $textContent = '';

                foreach ($currentNode->children as $child) {
                    if ($child instanceof HTMLPurifier_Node_Element) {
                        $textContent = '';
                        break;
                    } elseif ($child instanceof HTMLPurifier_Node_Text) {
                        $textContent .= $child->data;
                    }
                }

                if (!$attr->validate($textContent, $config, $context)) {
                    $currentNode->attr['datetime'] = '1970-01-01';
                }
            }
        }

        return $children;
    }
}
