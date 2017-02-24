<?php

namespace YOOtheme\Framework\Yii;

use yii\helpers\Url;
use yii\helpers\VarDumper;
use YOOtheme\Framework\Resource\LocatorInterface;
use YOOtheme\Framework\Routing\Request;
use YOOtheme\Framework\Routing\UrlGenerator as BaseGenerator;

class UrlGenerator extends BaseGenerator
{

    /**
     * @var array
     */
    protected $action;

    /**
     * Constructor.
     */
    public function __construct(Request $request, LocatorInterface $locator, $action)
    {
        parent::__construct($request, $locator);

        $this->action = compact('action');
    }

    /**
     * {@inheritdoc}
     */
    public function route($pattern = '', $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if ($pattern !== '') {

            $search = array();

            foreach ($parameters as $key => $value) {
                $search[] = '#:' . preg_quote($key, '#') . '(?!\w)#';
            }

            $pattern = preg_replace($search, $parameters, $pattern);
            $pattern = preg_replace('#\(/?:.+\)|\(|\)|\\\\#', '', $pattern);

            $parameters = array_merge(array('p' => $pattern), $parameters);
        }

        $route = array_merge($this->action, $parameters);
        $parameters[0] = '/admin/widgets/'.$route['action'];
        return $this->to(Url::to($parameters), [], $referenceType);
    }


    /**
     * Get the URL to a path.
     *
     * @param  string $path
     * @param  array  $parameters
     * @param  mixed  $referenceType
     * @return string
     */
    public function to($path, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $basePath = strtr($this->request->getBasePath(), '\\', '/');
        $realPath = realpath(dirname(__FILE__) . '/../../../../../../../../../../');

        if ($query = substr(strstr($path, '?'), 1)) {
            parse_str($query, $params);
            $path = strstr($path, '?', true);
            $parameters = array_replace($parameters, $params);
        }

        if ($query = http_build_query($parameters)) {
            $query = '?'.$query;
        }

        if ($path and !$this->isAbsolutePath($path)) {
            $path = $this->locator->find($path) ?: $path;
        }

        $path = strtr($path, '\\', '/');

        if ($basePath && strpos($path, $basePath) === 0) {
            $path = ltrim(substr($path, strlen($basePath)), '/');
        }

        if ($realPath && strpos($path, $realPath) === 0) {
            $path = ltrim(substr($path, strlen($realPath)), '/');
        }

        if ($path and preg_match('/^(?!\/|[a-z]+:\/\/)/i', $path)) {
            $path = $this->base($referenceType).'/'.$path;
        }

        return $path.$query;
    }
}
