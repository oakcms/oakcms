<?php


namespace app\helpers;


abstract class StringHelper
{
    /**
     * Increment styles.
     *
     * @var    array
     */
    protected static $incrementStyles = [
        'dash'    => [
            '#-(\d+)$#',
            '-%d',
        ],
        'dash_'    => [
            '#_(\d+)$#',
            '_%d',
        ],
        'default' => [
            ['#\((\d+)\)$#', '#\(\d+\)$#'],
            [' (%d)', '(%d)'],
        ],
    ];

    /**
     * Increments a trailing number in a string.
     *
     * Used to easily create distinct labels when copying objects. The method has the following styles:
     *
     * default: "Label" becomes "Label (2)"
     * dash:    "Label" becomes "Label-2"
     *
     * @param   string  $string The source string.
     * @param   string  $style  The the style (default|dash).
     * @param   integer $n      If supplied, this number is used for the copy, otherwise it is the 'next' number.
     *
     * @return  string  The incremented string.
     */
    public static function increment($string, $style = 'default', $n = 0)
    {
        $styleSpec = isset(static::$incrementStyles[$style]) ? static::$incrementStyles[$style] : static::$incrementStyles['default'];

        if (is_array($styleSpec[0])) {
            $rxSearch = $styleSpec[0][0];
            $rxReplace = $styleSpec[0][1];
        } else {
            $rxSearch = $rxReplace = $styleSpec[0];
        }

        if (is_array($styleSpec[1])) {
            $newFormat = $styleSpec[1][0];
            $oldFormat = $styleSpec[1][1];
        } else {
            $newFormat = $oldFormat = $styleSpec[1];
        }

        if (preg_match($rxSearch, $string, $matches)) {
            $n = empty($n) ? ($matches[1] + 1) : $n;
            $string = preg_replace($rxReplace, sprintf($oldFormat, $n), $string);
        } else {
            $n = empty($n) ? 2 : $n;
            $string .= sprintf($newFormat, $n);
        }

        return $string;
    }
}
