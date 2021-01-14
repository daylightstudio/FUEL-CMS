<?php

/**
 * Validates HTML5 duration string according to spec
 * https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#durations
 */
class HTMLPurifier_AttrDef_HTML5_Duration extends HTMLPurifier_AttrDef
{
    const REGEX_ISO8601 = '/^
        P
        (?P<w>\d+W)?
        (?P<d>\d+D)?
        (
            T
            (?P<h>\d+H)?
            (?P<m>\d+M)?
            (?P<s>\d+(\.\d+)?S)?
        )?
    $/xi';

    const REGEX_HUMAN = '/(\d+(\s*[WDHMS]|\.\d+\s*S))/i';

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);

        if (($result = $this->validateISODuration($string)) !== false) {
            return $result;
        }

        if (($result = $this->validateHumanDuration($string)) !== false) {
            return $result;
        }

        return false;
    }

    /**
     * Validate ISO-8601 duration string
     *
     * Note: duration as defined in the HTML5 spec cannot include months or years
     *
     * @param string $string
     * @return boolean
     */
    protected function validateISODuration($string)
    {
        if (!preg_match(self::REGEX_ISO8601, $string, $match)) {
            return false;
        }

        $parts = array(
            'w' => 0,
            'd' => 0,
            'h' => 0,
            'm' => 0,
            's' => 0,
        );

        foreach ($parts as $unit => $_) {
            if (!isset($match[$unit])) {
                continue;
            }

            $value = substr($match[$unit], 0, -1);
            $value = $unit === 's' ? (float) $value : (int) $value;

            $parts[$unit] = $value;
        }

        // The spec self-contradicts itself in disallowing weeks in ISO-8601
        // format, but allowing them in Human format - each week being equal
        // to 604800 seconds (7 days).

        if ($parts['w'] > 0) {
            $parts['d'] += $parts['w'] * 7;
            $parts['w'] = 0;
        }

        $duration = 'P';
        foreach ($parts as $unit => $value) {
            if ($unit === 'h') {
                $duration .= 'T';
            }

            if ($value > 0) {
                $duration .= ($unit === 's' ? $this->formatSeconds($value) : $value) . strtoupper($unit);
            }
        }
        $duration = rtrim($duration, 'T');

        // At least one element must be present, thus "P" is not a valid
        // representation for a duration of 0 seconds. "PT0S" or "P0D" are both
        // valid and represent the same duration.
        // https://en.wikipedia.org/wiki/ISO_8601#Durations

        if ($duration === 'P') {
            $duration = 'PT0S';
        }

        return $duration;
    }

    /**
     * Validate human readable HTML5 duration string
     *
     * @param string $string
     * @return boolean|string
     */
    protected function validateHumanDuration($string)
    {
        if (!preg_match_all(self::REGEX_HUMAN, $string, $matches)) {
            return false;
        }

        // One or more duration time components, each with a different duration
        // time component scale, in any order.
        $parts = array(
            'w' => false,
            'd' => false,
            'h' => false,
            'm' => false,
            's' => false,
        );

        foreach ($matches[0] as $match) {
            $unit = strtolower(substr($match, -1));

            $value = rtrim(substr($match, 0, -1));
            $value = $unit === 's' ? (float) $value : (int) $value;

            if ($value > 0 && $parts[$unit] === false) {
                $parts[$unit] = $value;
            }
        }

        $duration = array();
        foreach ($parts as $unit => $value) {
            if ($value === false) {
                continue;
            }
            $duration[] = ($unit === 's' ? $this->formatSeconds($value) : $value) . $unit;
        }
        $duration = implode(' ', $duration);

        if ($duration === '') {
            $duration = '0s';
        }

        return $duration;
    }

    /**
     * Formats seconds without leading zero and at most 3 non-zero decimals
     *
     * @param float $sec
     * @return string
     */
    protected function formatSeconds($sec)
    {
        $msec = round(($sec - (int) $sec) * 1000);
        if ($msec > 0) {
            return rtrim(sprintf('%d.%03d', $sec, $msec), '0');
        }
        return sprintf('%d', $sec);
    }
}
