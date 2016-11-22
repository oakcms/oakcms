<?php

namespace YOOtheme\Framework\View\Asset\Filter;

use YOOtheme\Framework\View\Asset\AssetInterface;

class CssRtlFilter implements FilterInterface
{
    public static $propertyMap = array(
        'margin-left'                => 'margin-right',
        'margin-right'               => 'margin-left',

        'padding-left'               => 'padding-right',
        'padding-right'              => 'padding-left',

        'border-left'                => 'border-right',
        'border-right'               => 'border-left',

        'border-left-color'          => 'border-right-color',
        'border-right-color'         => 'border-left-color',
        'border-left-width'          => 'border-right-width',
        'border-right-width'         => 'border-left-width',
        'border-left-style'          => 'border-right-style',
        'border-right-style'         => 'border-left-style',

        'border-bottom-right-radius' => 'border-bottom-left-radius',
        'border-bottom-left-radius'  => 'border-bottom-right-radius',

        'border-top-right-radius'    => 'border-top-left-radius',
        'border-top-left-radius'     => 'border-top-right-radius',

        'left'                       => 'right',
        'right'                      => 'left'
    );

    public static $valueMap = array(
        'padding'             => 'quad',
        'margin'              => 'quad',
        'text-align'          => 'rtltr',
        'float'               => 'rtltr',
        'clear'               => 'rtltr',
        'direction'           => 'direction',
        'border-radius'       => 'quad_radius',
        'border-color'        => 'quad',
        'border-width'        => 'quad',
        'border-style'        => 'quad',
        'background-position' => 'bgPosition',
        'box-shadow'          => 'boxShadow',
        'background'          => 'background',
        'background-image'    => 'backgroundImage'
    );

    /**
     * {@inheritdoc}
     */
    public function filterContent(AssetInterface $asset)
    {
        $asset->setContent($this->process($asset->getContent()));
    }

    /**
     * Convert CSS to be right to left.
     *
     * @param  string $css
     * @return string
     */
    public function process($css)
    {
        $css = trim($css); // give it a solid trimming to start

        $css = preg_replace('/\/\*[\s\S]+?\*\//', '', $css);      // comments
        $css = preg_replace('/[\n\r]/', '', $css);                // line breaks and carriage returns
        $css = preg_replace('/\s*([:;,{}])\s*/', '$1', $css);     // space between selectors, declarations, properties and values
        $css = preg_replace('/\s+/', ' ', $css);                  // replace multiple spaces with single spaces

        $that = $this;

        $css = preg_replace_callback('/([^;:\{\}]+?)\:([^;:\{\}]+?)([;}])/i', function ($arr) use ($that) {

            list($all, $prop, $val, $end) = $arr;

            $isImportant = strpos($val, '!important');

            if (isset($that::$propertyMap[$prop])) {
                $prop = $that::$propertyMap[$prop];
            }

            if (array_key_exists($prop, $that::$valueMap)) {
                $method = $that::$valueMap[$prop];
                $val    = $that->{$method}($val);
            }

            if (strpos($val, '!important') === false && $isImportant) $val .= '!important';

            return $prop.':'.$val.$end;
        }, $css);

        return $css;
    }

    public function quad($v)
    {
        // 1px 2px 3px 4px => 1px 4px 3px 2px
        $m = explode(' ', trim($v));
        if ($m && count($m) == 4) {
            return implode(' ', array($m[0], $m[3], $m[2], $m[1]));
        }
        return $v;
    }


    public function quad_radius($v)
    {
        $m = explode('/\s+/', trim($v));
        // 1px 2px 3px 4px => 1px 2px 4px 3px
        // since border-radius: top-left top-right bottom-right bottom-left
        // will be border-radius: top-right top-left bottom-left bottom-right
        if ($m && count($m) == 4) {
            return implode(' ', array($m[1], $m[0], $m[3], $m[2]));
        } else if ($m && count($m) == 3) {
            // 5px 10px 20px => 10px 5px 10px 20px
            return implode(' ', array($m[1], $m[0], $m[1], $m[2]));
        }
        return $v;
    }

    public function direction($v)
    {
        return (preg_match('/ltr/', $v) ? 'rtl' : (preg_match('/rtl/', $v) ? 'ltr' : $v));
    }

    public function bracketCommaSplit($str)
    {
        /* <prop1>(<args1>), <prop2>(<args2>) -> ["<prop1>(<args1>)", "<prop2>(<args2>)"]*/
        $parenthesisCount = 0;
        $lastSplit        = 0;
        $arr              = array();
        for ($i = 0; $i < strlen($str); ++$i) {
            $c = $str[$i];
            $parenthesisCount += ($c == '(' ? 1 : ($c == ')' ? -1 : 0));
            if (($c == ',' && $parenthesisCount == 0) || $i == strlen($str) - 1) {
                $arr[]     = trim(trim(trim(substr($str, $lastSplit, $i - $lastSplit + 1)), ','));
                $lastSplit = $i + 1; // +1 to get rid of the comma
            }
        }
        return $arr;
    }

    public function rtltr($v)
    {
        if (preg_match('/left/', $v)) return 'right';
        if (preg_match('/right/', $v)) return 'left';
        return $v;
    }

    public function bgPosition($v)
    {
        if (preg_match('/\bleft\b/', $v)) {
            $v = preg_replace('/\bleft\b/', 'right', $v);
        } else if (preg_match('/\bright\b/', $v)) {
            $v = preg_replace('/\bright\b/', 'left', $v);
        }
        $m = explode(' ', trim($v));
        if ($m && (count($m) == 1) && preg_match('/(\d+)([a-z]{2}|%)/', $v)) {
            $v = 'right '.$v;
        }
        if ($m && count($m) == 2 && preg_match('/\d+%/', $m[0])) {
            // 30% => 70% (100 - x)
            $v = (100 - (int)$m[0]).'% '.$m[1];
        }
        preg_match('/(\-?\d+)px/', $m[0], $pxmatch);
        if ($m && count($m) == 2 && $pxmatch) {
            $x      = $pxmatch[1];
            $minusX = ($x == '0' ? '0' : ((int)$x < 0 ? substr($x, 1).'px' : '-'.$x.'px'));
            $v      = $minusX.' '.$m[1];
        }
        return $v;
    }

    public function boxShadow($v)
    {
        $shadowRtl = function ($shadow) {
            // multiplies <left> offset with -1
            $found = false;
            $parts = explode(' ', $shadow);
            for ($i = 0; $i < count($parts); ++$i) {
                $el = $parts[$i];
                if (!$found && preg_match('/\d/', $el)) {
                    $found     = true;
                    $parts[$i] = ($el[0] == "0" ? 0 : ($el[0] == "-" ? substr($el, 1) : "-".$el));
                }
            }
            return implode(' ', $parts);
        };
        $arr       = $this->bracketCommaSplit($v);

        $v = implode(',', array_map($shadowRtl, $arr));

        return $v;
    }

    public function backgroundImage($val)
    {
        $parseSingle = function ($v) {
            if (substr($v, 0, 4) == "url(") {
                // dont mess with background image paths for now
                return $v;
            }
            if (strpos($v, "gradient")) {
                $v = preg_replace_callback('/(left|right)/', function ($dir) {
                    return $dir === 'left' ? 'right' : 'left';
                }, $v);
                $v = preg_replace_callback('/(\d+deg)/', function ($el) {

                    $num = (int)preg_replace('/deg/', '', $el[0]);

                    return (180 - $num).'deg';
                }, $v);
            }
            return $v;
        };
        $arr         = $this->bracketCommaSplit($val);
        return implode(',', array_map($parseSingle, $arr));
    }

    public function background($v)
    {
        // FIXME: split several background layers (divided by comma)

        $that        = $this;
        $parseSingle = function ($v) use ($that) {
            // background-image
            $method = function ($val) use ($that) {
                $str = $val[0];
                return $that->backgroundImage($str);
            };
            $v      = preg_replace_callback('/url\((.*?)\)|none|([^\s]*?gradient.*?\(.+\))/i', $method, $v);

            // background-position
            $v = preg_replace_callback('/\s(left|right|center|top|bottom|-?\d+([a-zA-Z]{2}|%?))\s(left|right|center|top|bottom|-?\d+([a-zA-Z]{2}|%?))[;\s]?/i',
                function ($arr) use ($that) {
                    return ' '.$that->bgPosition(trim($arr[0], ';')).(strpos($arr[0], ';') !== false ? ';' : ' ');
                }, $v);
            return $v;
        };
        $arr         = $this->bracketCommaSplit($v);
        return implode(',', array_map($parseSingle, $arr));
    }
}
