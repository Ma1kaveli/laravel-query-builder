<?php

namespace Tests\Unit\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use PhpParser\Node\Scalar\Float_;
use Tests\TestCase;
use QueryBuilder\Helpers\CheckTypes;

class CheckTypesTest extends TestCase
{
    /**
     * Проверяем isInteger, что он принимает только целые числа
     */
    public function test_is_integer()
    {
        $this->assertTrue(CheckTypes::isInteger(1));
        $this->assertTrue(CheckTypes::isInteger('1'));
        $this->assertFalse(CheckTypes::isInteger('a1'));
        $this->assertFalse(CheckTypes::isInteger(1.1));
        $this->assertFalse(CheckTypes::isInteger('1.1'));
        $this->assertFalse(CheckTypes::isInteger('a1.1'));
        $this->assertFalse(CheckTypes::isInteger(''));
        $this->assertFalse(CheckTypes::isInteger(null));
        $this->assertFalse(CheckTypes::isInteger([]));
        $this->assertFalse(CheckTypes::isInteger(new Carbon()));
    }

    /**
     * Проверяем isFloat, что он принимает только числа с точкой
     */
    public function test_is_float()
    {
        $this->assertTrue(CheckTypes::isFloat('1.1'));
        $this->assertTrue(CheckTypes::isFloat((new Float_(1.1))->value));
        $this->assertTrue(CheckTypes::isFloat(1.1));
        $this->assertFalse(CheckTypes::isFloat('a1.1'));
        $this->assertFalse(CheckTypes::isFloat(1));
        $this->assertFalse(CheckTypes::isFloat('1'));
        $this->assertFalse(CheckTypes::isFloat('a1'));
        $this->assertFalse(CheckTypes::isFloat(''));
        $this->assertFalse(CheckTypes::isFloat(null));
        $this->assertFalse(CheckTypes::isFloat([]));
        $this->assertFalse(CheckTypes::isFloat(new Carbon()));
    }

    /**
     * Проверяем isDouble, что он принимает только числа с точкой
     */
    public function test_is_double()
    {
        $this->assertTrue(CheckTypes::isDouble('1.1'));
        $this->assertTrue(CheckTypes::isDouble((new Float_(1.1))->value));
        $this->assertTrue(CheckTypes::isDouble(1.1));
        $this->assertFalse(CheckTypes::isDouble('a1.1'));
        $this->assertFalse(CheckTypes::isDouble(1));
        $this->assertFalse(CheckTypes::isDouble('1'));
        $this->assertFalse(CheckTypes::isDouble('a1'));
        $this->assertFalse(CheckTypes::isDouble(''));
        $this->assertFalse(CheckTypes::isDouble(null));
        $this->assertFalse(CheckTypes::isDouble([]));
        $this->assertFalse(CheckTypes::isDouble(new Carbon()));
        $this->assertSame(
            CheckTypes::isFloat('1.1'),
            CheckTypes::isDouble('1.1')
        );
    }

    /**
     * Проверяем isNumeric, что он принимает только числа
     */
    public function test_is_numeric()
    {
        $this->assertTrue(CheckTypes::isNumeric('1.1'));
        $this->assertTrue(CheckTypes::isNumeric(1.1));
        $this->assertTrue(CheckTypes::isNumeric('1'));
        $this->assertTrue(CheckTypes::isNumeric(1));
        $this->assertFalse(CheckTypes::isNumeric('a1.1'));
        $this->assertFalse(CheckTypes::isNumeric('a1'));
        $this->assertFalse(CheckTypes::isNumeric(''));
        $this->assertFalse(CheckTypes::isNumeric(null));
        $this->assertFalse(CheckTypes::isNumeric([]));
        $this->assertFalse(CheckTypes::isNumeric(new Carbon()));
    }

    /**
     * Проверяем isString, что он принимает только строки
     */
    public function test_is_string()
    {
        $this->assertTrue(CheckTypes::isString('a'));
        $this->assertTrue(CheckTypes::isString(''));
        $this->assertFalse(CheckTypes::isString(1));
        $this->assertFalse(CheckTypes::isString(1.1));
        $this->assertFalse(CheckTypes::isString(null));
        $this->assertFalse(CheckTypes::isString([]));
        $this->assertFalse(CheckTypes::isString(new Carbon()));
    }

    /**
     * Проверяем isBool, что он принимает только bool
     */
    public function test_is_bool()
    {
        $this->assertTrue(CheckTypes::isBool(true));
        $this->assertTrue(CheckTypes::isBool(false));
        $this->assertFalse(CheckTypes::isBool('true'));
        $this->assertFalse(CheckTypes::isBool('false'));
        $this->assertFalse(CheckTypes::isBool(1));
        $this->assertFalse(CheckTypes::isBool(0));
        $this->assertFalse(CheckTypes::isBool('1'));
        $this->assertFalse(CheckTypes::isBool('0'));
        $this->assertFalse(CheckTypes::isBool(1.1));
        $this->assertFalse(CheckTypes::isBool(null));
        $this->assertFalse(CheckTypes::isBool([]));
        $this->assertFalse(CheckTypes::isBool(new Carbon()));
    }

    /**
     * Проверяем isDateFormat, что он принимает только даты в определенном формате
     */
    public function test_is_date_format()
    {
        $this->setDateTimeFormat();

        $this->assertTrue(CheckTypes::isDateFormat('2022-01-01'));
        $this->assertTrue(CheckTypes::isDateFormat('01.01.2022'));
        $this->assertTrue(CheckTypes::isDateFormat('01/01/2022'));
        $this->assertTrue(CheckTypes::isDateFormat('2022-01-01 01:01:01'));
        $this->assertFalse(CheckTypes::isDateFormat('1 January 2022'));
        $this->assertFalse(CheckTypes::isDateFormat('01 Jan 2022'));
        $this->assertFalse(CheckTypes::isDateFormat('a1.1'));
        $this->assertFalse(CheckTypes::isDateFormat('01 Jan 2022'));
        $this->assertFalse(CheckTypes::isDateFormat(''));
        $this->assertFalse(CheckTypes::isDateFormat(null));
        $this->assertFalse(CheckTypes::isDateFormat(new Carbon()));
        $this->assertFalse(CheckTypes::isDateFormat([]));
    }

    /**
     * Проверяем isDateFormatArray, что он принимает только массив дат в определенном формате
     */
    public function test_is_time_format()
    {
        $this->setDateTimeFormat();

        $this->assertTrue(CheckTypes::isTimeFormat('12:00'));
        $this->assertTrue(CheckTypes::isTimeFormat('12:00:00'));
        $this->assertTrue(CheckTypes::isTimeFormat('12:00:00.000000'));
        $this->assertFalse(CheckTypes::isTimeFormat('12:00:00.0000'));
        $this->assertFalse(CheckTypes::isTimeFormat('a1.1'));
        $this->assertFalse(CheckTypes::isTimeFormat(''));
        $this->assertFalse(CheckTypes::isTimeFormat(null));
        $this->assertFalse(CheckTypes::isTimeFormat(new Carbon()));
        $this->assertFalse(CheckTypes::isTimeFormat([]));
    }

    /**
     * Проверяем isIntegerArray, что он принимает только массив целых чисел
     */
    public function test_is_integer_array()
    {
        $this->assertTrue(CheckTypes::isIntegerArray([1, 2, 3]));
        $this->assertFalse(CheckTypes::isIntegerArray([1.1, 2.2, 3.3]));
        $this->assertFalse(CheckTypes::isIntegerArray(['a', 'b', 'c']));
        $this->assertFalse(CheckTypes::isIntegerArray([]));
        $this->assertFalse(CheckTypes::isIntegerArray(null));
        $this->assertFalse(CheckTypes::isIntegerArray(new Carbon()));
    }

    /**
     * Проверяем isFloatArray, что он принимает только массив чисел с точкой
     */
    public function test_is_float_array()
    {
        $this->assertTrue(CheckTypes::isFloatArray([1.1, 2.2, 3.3]));
        $this->assertFalse(CheckTypes::isFloatArray([1, 2, 3]));
        $this->assertFalse(CheckTypes::isFloatArray(['a', 'b', 'c']));
        $this->assertFalse(CheckTypes::isFloatArray([]));
        $this->assertFalse(CheckTypes::isFloatArray(null));
        $this->assertFalse(CheckTypes::isFloatArray(new Carbon()));
    }

    /**
     * Проверяем isDoubleArray, что он принимает только массив чисел с точкой
     */
    public function test_is_double_array()
    {
        $this->assertTrue(CheckTypes::isDoubleArray([1.1, 2.2, 3.3]));
        $this->assertFalse(CheckTypes::isDoubleArray([1, 2, 3]));
        $this->assertFalse(CheckTypes::isDoubleArray(['a', 'b', 'c']));
        $this->assertFalse(CheckTypes::isDoubleArray([]));
        $this->assertFalse(CheckTypes::isDoubleArray(null));
        $this->assertFalse(CheckTypes::isDoubleArray(new Carbon()));
        $this->assertSame(
            CheckTypes::isFloatArray([1.1, 2.2, 3.3]),
            CheckTypes::isDoubleArray([1.1, 2.2, 3.3])
        );
    }

    /**
     * Проверяем isNumericArray, что он принимает только массив чисел
     */
    public function test_is_numeric_array()
    {
        $this->assertTrue(CheckTypes::isNumericArray([1.1, 2.2, 3.3]));
        $this->assertTrue(CheckTypes::isNumericArray([1, 2, 3]));
        $this->assertFalse(CheckTypes::isNumericArray(['a', 'b', 'c']));
        $this->assertFalse(CheckTypes::isNumericArray([]));
        $this->assertFalse(CheckTypes::isNumericArray(null));
        $this->assertFalse(CheckTypes::isNumericArray(new Carbon()));
    }

    /**
     * Проверяем isNumberRange, что он принимает только массив чисел
     */
    public function test_is_number_range()
    {
        $this->assertTrue(CheckTypes::isNumberRange([1, 2]));
        $this->assertTrue(CheckTypes::isNumberRange([1.1, 2.2]));
        $this->assertFalse(CheckTypes::isNumberRange([1, 2, 3]));
        $this->assertFalse(CheckTypes::isNumberRange([1.1, 2.2, 3.3]));
        $this->assertFalse(CheckTypes::isNumberRange(['a', 'b', 'c']));
        $this->assertFalse(CheckTypes::isNumberRange([]));
        $this->assertFalse(CheckTypes::isNumberRange(null));
        $this->assertFalse(CheckTypes::isNumberRange(new Carbon()));
    }

    /**
     * Проверяем isStringArray, что он принимает только массив строк
     */
    public function test_is_string_array()
    {
        $this->assertTrue(CheckTypes::isStringArray(['a', 'b', 'c']));
        $this->assertFalse(CheckTypes::isStringArray([1, 2, 3]));
        $this->assertFalse(CheckTypes::isStringArray([1.1, 2.2, 3.3]));
        $this->assertFalse(CheckTypes::isStringArray([]));
        $this->assertFalse(CheckTypes::isStringArray(null));
        $this->assertFalse(CheckTypes::isStringArray(new Carbon()));
    }

    /**
     * Проверяем isDateFormatArray, что он принимает только массив дат в определенном формате
     */
    public function test_is_date_format_array()
    {
        $this->setDateTimeFormat();

        $this->assertTrue(CheckTypes::isDateFormatArray(['2021-01-01', '01.01.2021', '01/01/2021',  '2021-01-01 12:00:00']));
        $this->assertFalse(CheckTypes::isDateFormatArray(['2021-01-01 12:00:00.0000']));
        $this->assertFalse(CheckTypes::isDateFormatArray(['a1.1']));
        $this->assertFalse(CheckTypes::isDateFormatArray([]));
        $this->assertFalse(CheckTypes::isDateFormatArray(null));
        $this->assertFalse(CheckTypes::isDateFormatArray(new Carbon()));
    }

    /**
     * Проверяем isDateFormatRange, что он принимает 2 даты (в массиве) в определенном формате
     */
    public function test_is_date_format_range()
    {
        $this->setDateTimeFormat();

        $this->assertTrue(CheckTypes::isDateFormatRange(['2021-01-01', '2021-01-02']));
        $this->assertFalse(CheckTypes::isDateFormatRange(['2021-01-01 12:00:00.0000']));
        $this->assertFalse(CheckTypes::isDateFormatRange(['a1.1']));
        $this->assertFalse(CheckTypes::isDateFormatRange([]));
        $this->assertFalse(CheckTypes::isDateFormatRange(null));
        $this->assertFalse(CheckTypes::isDateFormatRange(new Carbon()));
    }

    /**
     * Проверяем isTimeFormatArray, что он принимает только массив дат в определенном формате
     */
    public function test_is_time_format_array()
    {
        $this->setDateTimeFormat();

        $this->assertTrue(CheckTypes::isTimeFormatArray(['12:00', '12:00:00', '12:00:00.000000']));
        $this->assertFalse(CheckTypes::isTimeFormatArray(['12:00:00.0000']));
        $this->assertFalse(CheckTypes::isTimeFormatArray(['a1.1']));
        $this->assertFalse(CheckTypes::isTimeFormatArray([]));
        $this->assertFalse(CheckTypes::isTimeFormatArray(null));
        $this->assertFalse(CheckTypes::isTimeFormatArray(new Carbon()));
    }

    /**
     * Проверяем isTimeFormatRange, что он принимает 2 времени (в массиве) в определенном формате
     */
    public function test_is_time_format_range()
    {
        $this->setDateTimeFormat();

        $this->assertTrue(CheckTypes::isTimeFormatRange(['12:00', '12:00:00']));
        $this->assertFalse(CheckTypes::isTimeFormatRange(['12:00:00.0000']));
        $this->assertFalse(CheckTypes::isTimeFormatRange(['a1.1']));
        $this->assertFalse(CheckTypes::isTimeFormatRange([]));
        $this->assertFalse(CheckTypes::isTimeFormatRange(null));
        $this->assertFalse(CheckTypes::isTimeFormatRange(new Carbon()));
    }

    /**
     * Проверяем isYear, что он принимает только год
     */
    public function test_is_year()
    {
        $this->assertTrue(CheckTypes::isYear(2021));
        $this->assertTrue(CheckTypes::isYear('2021'));
        $this->assertFalse(CheckTypes::isYear(2021.1));
        $this->assertFalse(CheckTypes::isYear([]));
        $this->assertFalse(CheckTypes::isYear(null));
        $this->assertFalse(CheckTypes::isYear(new Carbon()));
    }

    /**
     * Проверяем isMonth, что он принимает только месяц
     */
    public function test_is_month()
    {
        $this->assertTrue(CheckTypes::isMonth(1));
        $this->assertTrue(CheckTypes::isMonth('1'));
        $this->assertFalse(CheckTypes::isMonth(1.1));
        $this->assertFalse(CheckTypes::isMonth([]));
        $this->assertFalse(CheckTypes::isMonth(null));
        $this->assertFalse(CheckTypes::isMonth(new Carbon()));
    }

    /**
     * Проверяем isDay, что он принимает только день
     */
    public function test_is_day()
    {
        $this->assertTrue(CheckTypes::isDay(1));
        $this->assertTrue(CheckTypes::isDay(31));
        $this->assertTrue(CheckTypes::isDay('1'));
        $this->assertFalse(CheckTypes::isDay(1.1));
        $this->assertFalse(CheckTypes::isDay(32));
        $this->assertFalse(CheckTypes::isDay(0));
        $this->assertFalse(CheckTypes::isDay([]));
        $this->assertFalse(CheckTypes::isDay(null));
        $this->assertFalse(CheckTypes::isDay(new Carbon()));
    }

    /**
     * Проверяем isMinute, что он принимает только минуты
     */
    public function test_is_minute()
    {
        $this->assertTrue(CheckTypes::isMinute(0));
        $this->assertTrue(CheckTypes::isMinute(1));
        $this->assertTrue(CheckTypes::isMinute(59));
        $this->assertTrue(CheckTypes::isMinute('1'));
        $this->assertFalse(CheckTypes::isMinute(1.1));
        $this->assertFalse(CheckTypes::isMinute(-1));
        $this->assertFalse(CheckTypes::isMinute(60));
        $this->assertFalse(CheckTypes::isMinute([]));
        $this->assertFalse(CheckTypes::isMinute(null));
        $this->assertFalse(CheckTypes::isMinute(new Carbon()));
    }

    /**
     * Проверяем isHour, что он принимает только час
     */
    public function test_is_hour()
    {
        $this->assertTrue(CheckTypes::isHour(0));
        $this->assertTrue(CheckTypes::isHour(1));
        $this->assertTrue(CheckTypes::isHour(23));
        $this->assertTrue(CheckTypes::isHour('1'));
        $this->assertFalse(CheckTypes::isHour(-1));
        $this->assertFalse(CheckTypes::isHour(1.1));
        $this->assertFalse(CheckTypes::isHour(24));
        $this->assertFalse(CheckTypes::isHour([]));
        $this->assertFalse(CheckTypes::isHour(null));
        $this->assertFalse(CheckTypes::isHour(new Carbon()));
    }

    /**
     * Проверяем isArrayWithElements, что он принимает только массив с элементами
     */
    public function test_is_array_with_elements()
    {
        $this->assertTrue(CheckTypes::isArrayWithElements([1, 2, 3]));
        $this->assertTrue(CheckTypes::isArrayWithElements(['1', '2', '3']));
        $this->assertTrue(CheckTypes::isArrayWithElements(['1.1', '2.1', '3.1']));
        $this->assertTrue(CheckTypes::isArrayWithElements(['foo', 'bar']));
        $this->assertFalse(CheckTypes::isArrayWithElements([]));
        $this->assertFalse(CheckTypes::isArrayWithElements(null));
        $this->assertFalse(CheckTypes::isArrayWithElements(new Carbon()));
    }
}
