<?php

namespace YOOtheme\Framework\Routing\Exception;

/**
 * @author Kris Wallsmith <kris@symfony.com>
 */
class HttpException extends \RuntimeException implements HttpExceptionInterface
{
    private $status;

    public function __construct($status, $message = null, \Exception $previous = null, $code = 0)
    {
        $this->status = $status;

        parent::__construct($message, $code, $previous);
    }

    public function getStatus()
    {
        return $this->status;
    }
}
