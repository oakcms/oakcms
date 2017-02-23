<?php

namespace YOOtheme\Framework\View\Asset\Filter;

use YOOtheme\Framework\Routing\Request;
use YOOtheme\Framework\View\Asset\AssetInterface;

class CssImageBase64Filter implements FilterInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->baseUrl  = parse_url($request->getBaseUrl(), PHP_URL_PATH);
        $this->basePath = $request->getBasePath();
    }

    /**
     * {@inheritdoc}
     */
    public function filterContent(AssetInterface $asset)
    {
        $images  = array();
        $content = $asset->getContent();

        // get images and the related path
        if (preg_match_all('/url\(\s*[\'"]?([^\'"]+)[\'"]?\s*\)/Ui', $asset->getContent(), $matches)) {
            foreach ($matches[0] as $i => $url) {

                if (strpos($path = $matches[1][$i], $this->baseUrl) !== 0) {
                    continue;
                }

                if ($path = realpath($this->basePath.'/'.ltrim(substr($path, strlen($this->baseUrl)), '/'))) {
                    $images[$url] = $path;
                }
            }
        }

        // check if image exists and filesize < 10kb
        foreach ($images as $url => $path) {
            if (filesize($path) <= 10240 && preg_match('/\.(gif|png|jpg)$/i', $path, $extension)) {
                $content = str_replace($url, sprintf('url(data:image/%s;base64,%s)', str_replace('jpg', 'jpeg', strtolower($extension[1])), base64_encode(file_get_contents($path))), $content);
            }
        }

        $asset->setContent($content);
    }
}
