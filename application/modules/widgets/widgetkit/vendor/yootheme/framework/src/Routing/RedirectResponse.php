<?php

namespace YOOtheme\Framework\Routing;

class RedirectResponse extends Response
{
    protected $targetUrl;

    /**
     * Constructor.
     *
     * @param string $url
     * @param int    $status
     * @param array  $headers
     */
    public function __construct($url, $status = 302, $headers = array())
    {
        parent::__construct('', $status, $headers);

        $this->setTargetUrl($url);
    }

    /**
     * Gets the target URL.
     *
     * @return string target URL
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * Sets the redirect target URL of this response.
     *
     * @param  string $url
     * @return RedirectResponse
     */
    public function setTargetUrl($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }

        $this->targetUrl = $url;

        $this->setHeader('Location', $url);
        $this->setContent(
            sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="refresh" content="1;url=%1$s" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        return $this;
    }
}
