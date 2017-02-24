<?php

namespace YOOtheme\Framework\Translation;

/**
 * @link https://github.com/symfony/Translation
 * @copyright Copyright (c) Fabien Potencier <fabien@symfony.com>
 */
class Interval
{
    /**
     * Tests if the given number is in the math interval.
     *
     * @param int    $number   A number
     * @param string $interval An interval
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public static function test($number, $interval)
    {
        $interval = trim($interval);

        if (!preg_match('/^'.self::getIntervalRegexp().'$/x', $interval, $matches)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid interval.', $interval));
        }

        if ($matches[1]) {
            foreach (explode(',', $matches[2]) as $n) {
                if ($number == $n) {
                    return true;
                }
            }
        } else {
            $leftNumber = self::convertNumber($matches['left']);
            $rightNumber = self::convertNumber($matches['right']);

            return
                ('[' === $matches['left_delimiter'] ? $number >= $leftNumber : $number > $leftNumber)
                && (']' === $matches['right_delimiter'] ? $number <= $rightNumber : $number < $rightNumber)
            ;
        }

        return false;
    }

    /**
     * Returns a Regexp that matches valid intervals.
     *
     * @return string A Regexp (without the delimiters)
     */
    public static function getIntervalRegexp()
    {
        return <<<EOF
        ({\s*
            (\-?\d+(\.\d+)?[\s*,\s*\-?\d+(\.\d+)?]*)
        \s*})

            |

        (?P<left_delimiter>[\[\]])
            \s*
            (?P<left>-Inf|\-?\d+(\.\d+)?)
            \s*,\s*
            (?P<right>\+?Inf|\-?\d+(\.\d+)?)
            \s*
        (?P<right_delimiter>[\[\]])
EOF;
    }

    private static function convertNumber($number)
    {
        if ('-Inf' === $number) {
            return log(0);
        } elseif ('+Inf' === $number || 'Inf' === $number) {
            return -log(0);
        }

        return (float) $number;
    }
}
