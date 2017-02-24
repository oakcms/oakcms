<?php

namespace YOOtheme\Framework\Routing;

class JsonResponse extends Response
{
    protected $data;
    protected $options;

    /**
     * Constructor.
     *
     * @param mixed  $data
     * @param int    $status
     * @param array  $headers
     */
    public function __construct($data = null, $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);

        if ($data === null) {
            $data = new \ArrayObject();
        }

        $this->setData($data);
        $this->options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param  mixed $data
     * @return JsonResponse
     */
    public function setData($data = array())
    {
        $this->data = @json_encode($data, $this->options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('Invalid JSON data.');
        }

        $this->setHeader('Content-Type', 'application/json');
        $this->setContent($this->data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function sendContent()
    {
        $this->clearBuffer();

        return parent::sendContent();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $this->clearBuffer();

        return parent::__toString();
    }

    /**
     * Clear output buffer.
     */
    protected function clearBuffer()
    {
        if (ob_get_length()) {
            ob_clean();
        }
    }
}
