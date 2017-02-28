<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */
namespace Awf\Utils;

/**
 * Parses an PHP string and extract the requests tokens from it
 *
 * @package Awf\Utils
 */
class PhpTokenizer
{
    /** @var  string    PHP code that will be analyzed */
    private $code;

    /**
     * Class constructor
     *
     * @param   string  $code   PHP code that will be analyzed
     */
    public function __construct($code = null)
    {
        $this->code = $code;
    }

    /**
     * Sets the code that will be analyzed
     *
     * @param   string  $code   PHP code that will be analyzed
     *
     * @return  $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function searchToken($type, $name, $skip = 0)
    {
        if(!$this->code)
        {
            throw new \RuntimeException('Please set some code before trying to analyze it');
        }

        $startLine  = $this->findToken($type, $name, $skip);

        // Token not found? Let's raise an exception
        if(!$startLine)
        {
            throw new \RuntimeException('Token '.$type.' with value '.$name.' not found');
        }

        $endLine = $this->findToken('AWF_SEMICOLON', ';', $startLine);
        $data    = $this->extractData($startLine, $endLine);

        return array(
            'endLine' => $endLine,
            'data'    => $data
        );
    }

    protected function findToken($type, $name, $skip = 0)
    {
        $code   = $this->processCode($skip);

        // Simply sanity check. If the "string" is not present (even in commented code), there's
        // no need to loop on every token: we can simply assume that the variable is not there
        if(strpos($code, $name) === false)
        {
            return null;
        }

        $tokens = token_get_all($code);

        $iterator   = new Collection($tokens);
        $collection = $iterator->getCachingIterator();
        $offset     = $skip ? $skip - 1 : 0;

        // Ok let's start looking for the requested token
        foreach($collection as $token)
        {
            if(is_string($token))
            {
                $info['token'] = $this->tokenChar($token);
                $info['value'] = $token;
            }
            else
            {
                $info['token'] = token_name($token[0]);
                $info['value'] = $token[1];
            }

            // Ok token found, let's get the line (we have to add the skip count since we processed the whole code string)
            if($info['token'] == $type && $info['value'] == $name)
            {
                // If it's an array, that's easy
                if(is_array($token))
                {
                    return $token[2] + $offset;
                }
                else
                {
                    // It's a string, I have to fetch the next token so I'll have the proper line number
                    // To be sure, I have to iterate until I finally get an array for the token
                    $next = null;

                    while(!is_array($next))
                    {
                        $next = $collection->getInnerIterator()->current();

                        // The next token is not an array (ie it's a char like ;.=?)? Move the iterator forward and fetch
                        // the next token
                        if(!is_array($next))
                        {
                            $collection->getInnerIterator()->next();
                            continue;
                        }

                        return $next[2] + $offset;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Processes the current code snippet, removing the lines we have to skip
     *
     * @param   int     $skip
     *
     * @return  string  The part of the code we're interested in
     */
    protected function processCode($skip)
    {
        if(!$skip)
        {
            return $this->code;
        }

        $lines  = explode("\n", $this->code);

        // If the line is not defined, let's return the whole code
        if(!isset($lines[$skip]))
        {
            return $this->code;
        }

        // I have to add the opening tag, otherwise token_get_all() won't find anything
        $result = '<?php'."\n";

        for($i = ($skip - 1); $i < count($lines); $i++)
        {
            if(!isset($lines[$i]))
            {
                break;
            }

            $result .= $lines[$i]."\n";
        }

        return $result;
    }

    protected function extractData($start, $end)
    {
        $result = '';
        $lines  = explode("\n", $this->code);

        if(!isset($lines[$start]))
        {
            return $result;
        }

        for($i = ($start - 1); $i < $end; $i++)
        {
            if(!isset($lines[$i]))
            {
                break;
            }

            $result .= $lines[$i]."\n";
        }

        return $result;
    }

    /**
     * PHP doesn't have a token for single chars as (),;= and so on.
     * This function sets some custom tokens for consistency
     *
     * @param   string  $char
     *
     * @return  string  Our custom token
     */
    private function tokenChar($char)
    {
        switch($char)
        {
            case ';':
                return 'AWF_SEMICOLON';
        }

        return 'AWF_UNKNOWN';
    }
}