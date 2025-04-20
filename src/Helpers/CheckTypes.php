<?php

namespace BaseQueryBuilder\Helpers;

use Carbon\Carbon;
use Carbon\Month;
use DateTime;
use Exception;
use Hamcrest\Type\IsDouble;

class CheckTypes {
    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isInteger(mixed $value): bool
    {
        try {
            return is_int($value);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isFloat(mixed $value): bool
    {
        try {
            return is_float($value);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isDouble(mixed $value): bool
    {
        try {
            return is_double($value);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isNumeric(mixed $value): bool
    {
        try {
            return is_numeric($value);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isString(mixed $value): bool
    {
        try {
            return is_string($value);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * isBool
     *
     * @param mixed $value
     *
     * @return bool
     */
    static public function isBool(mixed $value): bool
    {
        try {
            return is_bool($value);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * isDateFormat
     *
     * @param mixed $value
     *
     * @return bool
     */
    static public function isDateFormat(mixed $value): bool
    {
        $dateFormats = config(
            'query-builder.check-types.date-formats',
            ['Y-m-d', 'd.m.Y', 'm/d/Y', 'd F Y', 'Y-m-d H:i:s']
        );
        foreach ([$dateFormats, DateTime::ATOM] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                return $date && $date->format($format) === $value;
            } catch (Exception) {
                continue;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isTimeFormat(mixed $value): bool {
        $timeFormats = config(
            'query-builder.check-types.time-formats',
            ['H:i', 'H:i:s', 'H:i:s.u']
        );
        foreach ($timeFormats as $format) {
            try {
                $time = Carbon::createFromFormat($format, $value);
                return $time && $time->format($format) === $value;
            } catch (Exception) {
                continue;
            }
        }

        return false;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isIntegerArray(mixed $data): bool
    {
        if (!is_array($data)) return false;

        foreach ($data as $el) {
            if (!CheckTypes::isInteger($el)) return false;
        }

        return true;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isFloatArray(mixed $data): bool
    {
        if (!is_array($data)) return false;

        foreach ($data as $el) {
            if (!CheckTypes::isFloat($el)) return false;
        }

        return true;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isDoubleArray(mixed $data): bool
    {
        if (!is_array($data)) return false;

        foreach ($data as $el) {
            if (!CheckTypes::isDouble($el)) return false;
        }

        return true;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isNumericArray(mixed $data): bool
    {
        if (!is_array($data)) return false;

        foreach ($data as $el) {
            if (!CheckTypes::isNumeric($el)) return false;
        }

        return true;
    }
    /**
     * isNumberRange
     *
     * @param mixed $data
     *
     * @return bool
     */
    static public function isNumberRange(mixed $data): bool
    {
        return self::isNumericArray($data) && count($data) === 2;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isStringArray(mixed $data): bool
    {
        if (!is_array($data)) return false;

        foreach ($data as $el) {
            if (!CheckTypes::isString($el)) return false;
        }

        return true;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isDateFormatArray(mixed $data): bool
    {
        if (!is_array($data)) return false;

        foreach ($data as $el) {
            if (!CheckTypes::isDateFormat($el)) return false;
        }

        return true;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isDateFormatRange(mixed $data): bool
    {
        return self::isDateFormatArray($data) && count($data) === 2;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isTimeFormatArray(mixed $data): bool
    {
        if (!is_array($data)) return false;

        foreach ($data as $el) {
            if (!CheckTypes::isTimeFormat($el)) return false;
        }

        return true;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isTimeFormatRange(mixed $data): bool
    {
        return self::isTimeFormatArray($data) && count($data) === 2;
    }

    /**
     * isYear
     *
     * @param  mixed $value
     *
     * @return bool
     */
    static public function isYear(mixed $value): bool
    {
        try {
            return self::isInteger($value) && $value > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * isMonth
     *
     * @param mixed $value
     *
     * @return bool
     */
    static public function isMonth(mixed $value): bool
    {
        try {
            return self::isInteger($value)
                && $value >= Month::January
                && $value <= Month::December;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isDay(mixed $value): bool
    {
        try {
            return self::isInteger($value)
                && $value >= 1 && $value <= 31;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isMinute(mixed $value): bool
    {
        try {
            return self::isInteger($value)
                && $value >= 0 && $value <= 59;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isHour(mixed $value): bool
    {
        try {
            return self::isInteger($value)
                && $value >= 0 && $value <= 23;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $data

     * @return bool
     */
    static public function isRange(mixed $data): bool
    {
        try {
            return self::isNumericArray($data) && count($data) === 2;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isArrayWithElements(mixed $data): bool
    {
        try {
            return is_array($data) && count($data) > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}
