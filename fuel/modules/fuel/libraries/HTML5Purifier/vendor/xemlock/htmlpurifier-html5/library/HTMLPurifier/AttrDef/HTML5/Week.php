<?php

/**
 * Validates HTML5 week string according to
 * https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#weeks
 */
class HTMLPurifier_AttrDef_HTML5_Week extends HTMLPurifier_AttrDef
{
    const REGEX = '/^
        (?P<year>\d{4,})
        -W
        (?P<week>[0-5]\d)
    $/xi';

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

        $year = (int) $match['year'];
        $week = (int) $match['week'];

        if ($year <= 0) {
            return false;
        }

        // ISO-8601 specification says that December 28th is always in the last
        // week of its year.
        // https://en.wikipedia.org/wiki/ISO_8601#Week_dates
        $time = mktime(0, 0, 0, 12, 28, $year);

        if ($week < 1 || $week > date('W', $time)) {
            return false;
        }

        return $string;
    }
}
