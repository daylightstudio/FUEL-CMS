<?php

/**
 * Validates HTML5 date and time strings according to spec
 * https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#dates-and-times
 *
 * This validator tries to parse as much data as possible and then tries to
 * render it in the desired format. It fails if either no datetime data can
 * be extracted from the input, or the extracted data is insufficient for
 * the desired format (with the exception of DatetimeGlobal format, which
 * uses server timezone offset if none is detected).
 */
class HTMLPurifier_AttrDef_HTML5_Datetime extends HTMLPurifier_AttrDef
{
    const REGEX = '/^
        (
            (?P<year>\d{4,})
            (
                -
                (?P<month>[01]\d)
                (
                    -
                    (?P<day>[0-3]\d)
                )?
            )?
        )?
        (
            (^|(\s+|T))
            (?P<hour>[0-2]\d)
            :
            (?P<minute>[0-5]\d)
            (
                :
                (?P<second>[0-5]\d(\.\d+)?)
            )?
        )?
        (
            (?P<tzZulu>Z)
            |
            (
                (?P<tzHour>[+-][0-2]\d)
                :?
                (?P<tzMinute>[0-5]\d)
            )
        )?
    $/xi';

    /**
     * Lookup table for supported formats and if they are enabled by default
     * @var array
     */
    protected static $formats = array(
        'Datetime'       => true,
        'DatetimeGlobal' => false,
        'DatetimeLocal'  => false,
        'Date'           => true,
        'Month'          => true,
        'Year'           => true,
        'Time'           => true,
        'TimezoneOffset' => true,
    );

    /**
     * Lookup table for allowed formats
     * @var array
     */
    protected $allowedFormats = array();

    /**
     * @param array $allowedFormats OPTIONAL
     * @throws HTMLPurifier_Exception If an invalid format is provided
     */
    public function __construct(array $allowedFormats = array())
    {
        // Validate allowed formats
        $allowedFormatsLookup = array();
        foreach ($allowedFormats as $format) {
            if (!isset(self::$formats[$format])) {
                throw new HTMLPurifier_Exception("'$format' is not a valid format");
            }
            $allowedFormatsLookup[$format] = true;
        }

        // Formats must be set in the same order as in self::$formats, so that
        // in default mode the result will be the longest matching format
        foreach (self::$formats as $format => $_) {
            if (isset($allowedFormatsLookup[$format])) {
                $this->allowedFormats[$format] = true;
            }
        }

        if (empty($this->allowedFormats)) {
            $this->allowedFormats = array_filter(self::$formats, 'intval');
        }
    }

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        if (($data = $this->parse($string)) === false) {
            return false;
        }
        return $this->format($data);
    }

    /**
     * @param string $string
     * @return array|bool
     */
    public function parse($string)
    {
        $string = $this->parseCDATA($string);

        if ($string === '' || !preg_match(self::REGEX, $string, $match)) {
            return false;
        }

        // Make sure all named patterns are present in the match array
        $match += array(
            'year'     => '',
            'month'    => '',
            'day'      => '',
            'hour'     => '',
            'minute'   => '',
            'second'   => '',
            'tzZulu'   => '',
            'tzHour'   => '',
            'tzMinute' => '',
        );

        $year = $month = $day = null;

        if ($match['year'] !== '') {
            $year = (int) $match['year'];

            // Dates before the year one can't be represented as a datetime in HTML5
            if ($year <= 0) {
                return false;
            }

            if ($match['month'] !== '') {
                $month = (int) $match['month'];
                if ($month < 1 || $month > 12) {
                    return false;
                }

                if ($match['day'] !== '') {
                    $day = (int) $match['day'];
                    if (!checkdate($month, $day, $year)) {
                        return false;
                    }
                }
            }
        }

        $hour = $minute = $second = null;

        if ($match['hour'] !== '') {
            $hour = (int) $match['hour'];
            $minute = (int) $match['minute'];
            $second = $match['second'] !== '' ? (float) $match['second'] : null;

            if ($hour > 23) {
                return false;
            }
        }

        $tzHour = $tzMinute = null;

        if ($match['tzZulu'] !== '') {
            $tzHour = 'Z';

        } elseif ($match['tzHour'] !== '') {
            $tzHour = (int) $match['tzHour'];
            $tzMinute = (int) $match['tzMinute'];

            if ($tzHour < -23 || $tzHour > 23) {
                return false;
            }
        }

        return compact(
            'year', 'month', 'day', 'hour', 'minute', 'second', 'tzHour', 'tzMinute'
        );
    }

    /**
     * @param array $data
     * @return bool|string
     */
    protected function format(array $data)
    {
        foreach ($this->allowedFormats as $format => $_) {
            if (($result = call_user_func(array($this, 'format' . $format), $data)) !== false) {
                return $result;
            }
        }
        return false;
    }

    /**
     * @param array $data
     * @return bool|string
     */
    protected function formatYear(array $data)
    {
        if (($year = $data['year']) === null) {
            return false;
        }
        return sprintf('%04d', $year);
    }

    /**
     * @see https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#months
     * @param array $data
     * @return bool|string
     */
    protected function formatMonth(array $data)
    {
        if (($year = $data['year']) === null ||
            ($month = $data['month']) === null
        ) {
            return false;
        }
        return sprintf('%04d-%02d', $year, $month);
    }

    /**
     * @see https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#dates
     * @param array $data
     * @return bool|string
     */
    protected function formatDate(array $data)
    {
        if (($year = $data['year']) === null ||
            ($month = $data['month']) === null ||
            ($day = $data['day']) === null
        ) {
            return false;
        }
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    /**
     * @see https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#times
     * @param array $data
     * @return bool|string
     */
    protected function formatTime(array $data)
    {
        if (($hour = $data['hour']) === null ||
            ($minute = $data['minute']) === null
        ) {
            return false;
        }
        $time = sprintf('%02d:%02d', $hour, $minute);
        if (($second = $data['second']) !== null) {
            $sec = (int) $second;
            $time .= sprintf(':%02d', $sec);

            $msec = round(($second - $sec) * 1000);
            if ($msec > 0) {
                $time .= rtrim(sprintf('.%03d', $msec), '0');
            }
        }
        return $time;
    }

    /**
     * @see https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#local-dates-and-times
     * @param array $data
     * @return bool|string
     */
    protected function formatDatetimeLocal(array $data)
    {
        if (($date = $this->formatDate($data)) === false ||
            ($time = $this->formatTime($data)) === false
        ) {
            return false;
        }
        // Use 'T' as normalized date/time separator, see:
        // https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#valid-normalised-local-date-and-time-string
        // Also it's the only separator recognized by input[type=datetime-local]
        return $date . 'T' . $time;
    }

    /**
     * Formats data as datetime with timezone offset. If no timezone offset
     * is present, the default server timezone offset is used.
     *
     * @see https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#global-dates-and-times
     * @param array $data
     * @return bool|string
     */
    protected function formatDatetimeGlobal(array $data)
    {
        if (($datetime = $this->formatDatetimeLocal($data)) === false) {
            return false;
        }
        if (($timezoneOffset = $this->formatTimezoneOffset($data)) === false) {
            $timezoneOffset = date('P');
        }
        return $datetime . $timezoneOffset;
    }

    /**
     * Formats data as datetime with optional timezone offset.
     *
     * This is used in particular for 'datetime' attribute of <time> element.
     *
     * @param array $data
     * @return bool|string
     */
    protected function formatDatetime(array $data)
    {
        if (($datetime = $this->formatDatetimeLocal($data)) === false) {
            return false;
        }
        if (($timezoneOffset = $this->formatTimezoneOffset($data)) !== false) {
            $datetime .= $timezoneOffset;
        }
        return $datetime;
    }

    /**
     * @see https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#time-zones
     * @param array $data
     * @return string
     */
    protected function formatTimezoneOffset(array $data)
    {
        if (($tzHour = $data['tzHour']) === null) {
            return false;
        }

        if ($tzHour === 'Z') {
            $tzOffset = 'Z';
        } else {
            $tzMinute = (int) $data['tzMinute'];
            $tzOffset = sprintf('%s%02d:%02d', $tzHour < 0 ? '-' : '+', abs($tzHour), $tzMinute);
        }

        return $tzOffset;
    }

    /**
     * @param string $formats
     * @return HTMLPurifier_AttrDef_HTML5_Datetime
     * @throws HTMLPurifier_Exception If an invalid format is provided
     */
    public function make($formats)
    {
        return new self(explode(',', $formats));
    }
}
