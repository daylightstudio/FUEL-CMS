<?php

/**
 * Post-transform performing validations for <progress> elements ensuring
 * that if value is present, it is within a valid range (0..1) or (0..max)
 *
 * Implementation is based on sanitization performed by browsers (compared
 * against Chrome 68 and Firefox 61).
 */
class HTMLPurifier_AttrTransform_HTML5_Progress extends HTMLPurifier_AttrTransform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        $max = isset($attr['max']) ? (float) $attr['max'] : 1;

        if ($max <= 0) {
            $this->confiscateAttr($attr, 'max');
        }

        if (isset($attr['value'])) {
            $value = (float) $attr['value'];

            if ($value < 0) {
                $this->confiscateAttr($attr, 'value');
            } elseif ($value > $max) {
                $attr['value'] = isset($attr['max']) ? $attr['max'] : 1;
            }
        }

        return $attr;
    }
}
