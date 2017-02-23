<?php

namespace YOOtheme\Framework\Routing;

class Response
{
	/**
	 * @var  array
	 */
	public $headers = array();

	/**
	 * @var string
	 */
	public $content;

	/**
	 * @var int
	 */
	public $status;

	/**
	 * @var  array
	 * @link http://www.iana.org/assignments/http-status-codes/
	 */
	public static $statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a Teapot',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
	);

    /**
     * Constructor.
     *
	 * @param string     $content
	 * @param string|int $status
	 * @param array      $headers
     */
	public function __construct($content = '', $status = 200, array $headers = array())
	{
		foreach ($headers as $name => $value) {
			$this->setHeader($name, $value);
		}

		$this->setContent($content);
		$this->setStatus($status);
	}

    /**
     * Returns a header value by name.
     *
     * @param  string $name
     * @param  bool   $first
     * @return mixed
     */
	public function getHeader($name, $first = true)
	{
		if (isset($this->headers[$name])) {
	        return $first ? $this->headers[$name][0] : $this->headers[$name];
		}
	}

	/**
	 * Sets a header by name.
	 *
	 * @param  string $name
	 * @param  string $values
	 * @param  bool   $replace
	 * @return self
	 */
	public function setHeader($name, $values, $replace = true)
	{
        $values = array_values((array) $values);

        if ($replace || !isset($this->headers[$name])) {
            $this->headers[$name] = $values;
        } else {
            $this->headers[$name] = array_merge($this->headers[$name], $values);
        }

		return $this;
	}

    /**
     * Gets the response content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the response content.
     *
     * @param  mixed $content
     * @return self
     */
    public function setContent($content)
    {
        if ($content !== null && !is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))) {
            throw new \UnexpectedValueException(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
        }

        $this->content = (string) $content;

        return $this;
    }

    /**
     * Gets the response status code.
     *
     * @return string
     */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Sets the response status code.
	 *
	 * @param  string|int $status
	 * @return self
	 */
	public function setStatus($status = 200)
	{
        if (!isset(self::$statuses[$status])) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $status));
        }

		$this->status = $status;

		return $this;
	}

    /**
     * Sends HTTP headers.
     *
     * @return Response
     */
	public function sendHeaders()
	{
        if (headers_sent()) {
            return $this;
        }

        header(sprintf('HTTP/%s %s %s', 'HTTP/1.0' == $_SERVER['SERVER_PROTOCOL'] ? '1.0' : '1.1', $this->status, static::$statuses[$this->status]), true, $this->status);

        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                header("{$name}: {$value}", true, $this->status);
            }
        }

		return $this;
	}

    /**
     * Sends content for the current web response.
     *
     * @return self
     */
    public function sendContent()
    {
        echo $this->content;

        return $this;
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return self
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();

        if ('cli' !== PHP_SAPI) {
            flush();
        }

        return $this;
    }

	/**
	 * Returns the content as a string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return (string) $this->content;
	}
}
