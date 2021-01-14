<?php

/**
 * Validates a floating point number
 */
class HTMLPurifier_AttrDef_Float extends HTMLPurifier_AttrDef
{
    /**
     * @var int|float
     */
    protected $min;

    /**
     * @var int|float
     */
    protected $max;

    /**
     * @var bool
     */
    protected $minInclusive = true;

    /**
     * @var bool
     */
    protected $maxInclusive = true;

    /**
     * Supported options:
     *
     * - 'min'          => int|float
     * - 'max'          => int|float
     * - 'minInclusive' => bool
     * - 'maxInclusive' => bool
     *
     * @param array $options OPTIONAL
     */
    public function __construct($options = null)
    {
        $options = is_array($options) ? $options : array();

        $this->min = isset($options['min']) ? floatval($options['min']) : null;
        $this->max = isset($options['max']) ? floatval($options['max']) : null;

        if (isset($options['minInclusive'])) {
            $this->minInclusive = (bool) $options['minInclusive'];
        }

        if (isset($options['maxInclusive'])) {
            $this->maxInclusive = (bool) $options['maxInclusive'];
        }
    }

    /**
     * @param string $number
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     */
    public function validate($number, $config, $context)
    {
        $number = $this->parseCDATA($number);

        if ($number === '') {
            return false;
        }

        // Up to PHP 5.6 is_numeric() returns TRUE for hex strings
        // http://php.net/manual/en/function.is-numeric.php
        if (!preg_match('/^[-+.0-9Ee]+$/', $number) || !is_numeric($number)) {
            return false;
        }

        // HTML numbers cannot start with '+' character
        // https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#valid-floating-point-number
        if (substr($number, 0, 1) === '+') {
            $number = substr($number, 1);
        }

        $value = floatval($number);

        if (($this->min !== null) &&
            (($this->minInclusive && $value < $this->min) || (!$this->minInclusive && $value <= $this->min))
        ) {
            return false;
        }

        if (($this->max !== null) &&
            (($this->maxInclusive && $this->max < $value) || (!$this->maxInclusive && $this->max <= $value))
        ) {
            return false;
        }

        return $number;
    }

    /**
     * Factory function
     *
     * @param string $string A comma-delimited list of key:value pairs. Example: "min:0,max:10".
     * @return HTMLPurifier_AttrDef_Float
     */
    public function make($string)
    {
        $options = array();
        foreach (explode(',', $string) as $pair) {
            $parts = explode(':', $pair, 2);
            if (count($parts) === 2) {
                list($key, $value) = $parts;
                $options[$key] = $value;
            }
        }
        $class = get_class($this);
        return new $class($options);
    }
}
