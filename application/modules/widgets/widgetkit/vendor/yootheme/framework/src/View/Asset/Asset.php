<?php

namespace YOOtheme\Framework\View\Asset;

abstract class Asset implements AssetInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $dependencies;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $source
     * @param array  $dependencies
     * @param array  $options
     */
    public function __construct($name, $source, array $dependencies = array(), array $options = array())
    {
        $this->name = $name;
        $this->source = $source;
        $this->dependencies = $dependencies;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(array $filters = array())
    {
        $asset = clone $this;

        foreach ($filters as $filter) {
            $filter->filterContent($asset);
        }

        return $asset->getContent();
    }
}
