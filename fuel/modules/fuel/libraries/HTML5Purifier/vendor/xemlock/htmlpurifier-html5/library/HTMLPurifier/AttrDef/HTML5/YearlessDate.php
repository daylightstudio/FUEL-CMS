<?php

/**
 * Validates HTML5 yearless date string according to the spec
 * https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#yearless-dates
 */
class HTMLPurifier_AttrDef_HTML5_YearlessDate extends HTMLPurifier_AttrDef
{
    const REGEX = '/^(?P<month>[01]\d)-(?P<day>[0-3]\d)$/';

    protected static $daysInMonths = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);

        if (!preg_match(self::REGEX, $string, $match)) {
            return false;
        }

        $month = (int) $match['month'];

        if ($month < 1 || $month > 12) {
            return false;
        }

        $day = (int) $match['day'];

        if ($day < 1 || $day > self::$daysInMonths[$month - 1]) {
            return false;
        }

        return $string;
    }
}
