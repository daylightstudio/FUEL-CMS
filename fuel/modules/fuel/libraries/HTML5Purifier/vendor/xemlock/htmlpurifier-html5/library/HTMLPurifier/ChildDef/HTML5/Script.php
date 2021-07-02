<?php

class HTMLPurifier_ChildDef_HTML5_Script extends HTMLPurifier_ChildDef
{
    public $type = 'script';

    /**
     * Whether children (text contents) are allowed
     * @var bool
     */
    public $allow_children = true;

    /**
     * @param HTMLPurifier_Node[] $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return HTMLPurifier_Node[]|bool
     */
    public function validateChildren($children, $config, $context)
    {
        $node = $context->exists('CurrentNode')
            ? $context->get('CurrentNode')
            : null;

        // Content model:
        //   If there is no src attribute, depends on the value of the type
        //   attribute, but must match script content restrictions.
        //   If there is a src attribute, the element must be either empty
        //   or contain only script documentation that also matches script
        //   content restrictions.
        // https://html.spec.whatwg.org/multipage/scripting.html#the-script-element

        if ($node instanceof HTMLPurifier_Node_Element) {
            // must validate src attribute here, because children validation is
            // executed before attribute validation

            // This part I don't like, but currently it's unavoidable because
            // of how HTMLPurifier works internally. Attribute transformations
            // and validations are done after children validation. So there is
            // no way of knowing whether src attribute is valid other than
            // do the validation here as well.
            $src = $this->getSrc($node, $config, $context);

            if (strlen($src)) {
                return array();
            }

            // Remove <script> if there is no 'src' attribute and no children
            // or if children are explicitly forbidden
            if (empty($children) || !$this->allow_children) {
                return false;
            }
        }

        return $this->allow_children ? true : array();
    }

    /**
     * @param HTMLPurifier_Node_Element $element
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     */
    protected function getSrc(HTMLPurifier_Node_Element $element, HTMLPurifier_Config $config, HTMLPurifier_Context $context)
    {
        $src = isset($element->attr['src']) ? trim($element->attr['src']) : '';

        if (strlen($src)) {
            $info = $config->getHTMLDefinition()->info['script'];
            if (isset($info->attr['src'])) {
                /** @var HTMLPurifier_AttrDef $srcAttrDef */
                $srcAttrDef = $info->attr['src'];

                $result = $srcAttrDef->validate($src, $config, $context);
                $src = $result === true ? $src : $result;
            }
        }

        return $src;
    }
}
