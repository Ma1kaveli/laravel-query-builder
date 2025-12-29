<?php

namespace QueryBuilder\Helpers;

use Carbon\Carbon;
use Carbon\Month;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Config;

class CheckTypes {
    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isInteger(mixed $value): bool
    {
        try {
            return is_int($value) || (is_string($value) && (string)(int)$value === $value);
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
            if (is_float($value)) {
                return true;
            }

            if (is_int($value)) {
                return false;
            }

            if (!is_string($value) || $value === '') {
                return false;
            }

            if (self::isInteger($value)) {
                return false;
            }

            return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
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
        return self::isFloat($value);
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
        try {
            $dateFormats = Config::get(
                'query-builder.check-types.date-formats',
                ['Y-m-d', 'd.m.Y', 'm/d/Y', 'd F Y', 'Y-m-d H:i:s']
            );

            foreach (array_merge($dateFormats, [DateTime::ATOM]) as $format) {
                try {
                    $date = Carbon::createFromFormat($format, $value);

                    return $date && $date->format($format) === $value;
                } catch (Exception) {
                    continue;
                }
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isTimeFormat(mixed $value): bool {
        try {
            $timeFormats = Config::get(
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
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isIntegerArray(mixed $data): bool
    {
        if (!is_array($data) || count($data) === 0) return false;

        foreach ($data as $el) {
            if (!self::isInteger($el)) return false;
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
        if (!is_array($data) || count($data) === 0) return false;

        foreach ($data as $el) {
            if (!self::isFloat($el)) return false;
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
        return self::isFloatArray($data);
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    static public function isNumericArray(mixed $data): bool
    {
        if (!is_array($data) || count($data) === 0) return false;

        foreach ($data as $el) {
            if (!self::isNumeric($el)) return false;
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
        if (!is_array($data) || count($data) === 0) return false;

        foreach ($data as $el) {
            if (!self::isString($el)) return false;
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
        if (!is_array($data) || count($data) === 0) return false;

        foreach ($data as $el) {
            if (!self::isDateFormat($el)) return false;
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
        if (!is_array($data) || count($data) === 0) return false;

        foreach ($data as $el) {
            if (!self::isTimeFormat($el)) return false;
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
        return self::isInteger($value) && $value >= 1 && $value <= 12;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isDay(mixed $value): bool
    {
        return self::isInteger($value) && $value >= 1 && $value <= 31;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isMinute(mixed $value): bool
    {
        return self::isInteger($value) && $value >= 0 && $value <= 59;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    static public function isHour(mixed $value): bool
    {
        return self::isInteger($value) && $value >= 0 && $value <= 23;
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
