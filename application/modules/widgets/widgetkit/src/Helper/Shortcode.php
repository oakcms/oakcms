<?php

namespace YOOtheme\Widgetkit\Helper;

class Shortcode
{
    /**
     * Applies callback to shortcode tags.
     *
     * @param  string   $tag
     * @param  string   $text
     * @param  callable $callback
     * @return mixed|string
     */
    public function parse($tag, $text, $callback)
    {
        if (false === strpos($text, '[')) {
            return $text;
        }

        $self = $this;
        return preg_replace_callback($this->getRegexp($tag), function($matches) use ($self, $callback) {

            // allow [[foo]] syntax for escaping a tag
            if ($matches[1] == '[' && $matches[7] == ']') {
                return substr($matches[0], 1, -1);
            }

            $tag  = $matches[2];
            $attrs = $self->attrs($matches[3]);
            $content = $matches[5];

            return $matches[1].call_user_func($callback, $attrs, $content, $tag, $matches[0]).$matches[7];

        }, $text);
    }

    /**
     * Retrieve attributes from the shortcode tag.
     *
     * @param  string $text
     * @return array
     */
    public function attrs($text)
    {
        $attrs   = array();
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text    = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if ($match[1]) {
                    $attrs[strtolower($match[1])] = $match[2];
                } else if ($match[3]) {
                    $attrs[strtolower($match[3])] = $match[4];
                } else if ($match[5] && $match[6] === 'true') {
                    $attrs[strtolower($match[5])] = true;
                } else if ($match[5] && $match[6] === 'false') {
                    $attrs[strtolower($match[5])] = false;
                } else if ($match[5]) {
                    $attrs[strtolower($match[5])] = $match[6];
                } else if ($match[7]) {
                    $attrs[$match[7]] = true;
                } else if ($match[8]) {
                    $attrs[$match[8]] = true;
                }
            }
        } else {
            $attrs = ltrim($text);
        }
        return $attrs;
    }

    /**
     * Gets the shortcode regular expression pattern.
     *
     * @param  string $tag
     * @return string
     */
    protected function getRegexp($tag)
    {
        return '/
                \[                               # Opening bracket
                    (\[?)                        # 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                    (' . $tag . ')               # 2: Shortcode name
                    (?![\w-])                    # Not followed by word character or hyphen
                    (                            # 3: Unroll the loop: Inside the opening shortcode tag
                        [^\]\/]*                 # Not a closing bracket or forward slash
                        (?:
                            \/(?!\])             # A forward slash not followed by a closing bracket
                            [^\]\/]*             # Not a closing bracket or forward slash
                        )*?
                    )
                    (?:
                        (\/)                     # 4: Self closing tag ...
                        \]                       # ... and closing bracket
                        |
                        \]                       # Closing bracket
                        (?:
                            (                    # 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                                [^\[]*           # Not an opening bracket
                                (?:
                                    \[(?!\/\2\]) # An opening bracket not followed by the closing shortcode tag
                                    [^\[]*       # Not an opening bracket
                                )*
                            )
                            (\[\/\2\])           # Closing shortcode tag
                        )?
                    )
                (\]?)                            # 6: Optional second closing brocket for escaping shortcodes: [[tag]]
                /sx';
    }
}
