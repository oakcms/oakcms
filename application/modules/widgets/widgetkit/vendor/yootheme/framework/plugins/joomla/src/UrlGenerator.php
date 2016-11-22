<?php

namespace YOOtheme\Framework\Joomla;

use YOOtheme\Framework\Routing\UrlGenerator as BaseGenerator;

class UrlGenerator extends BaseGenerator
{
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

        $url = $this->request->getBaseRoute();

        if ($query = http_build_query($parameters)) {
            $url .= strpos($url, '?') ? '&' : '?';
            $url .= $query;
        }

        return $this->to(\JRoute::_($url, false), array(), $referenceType);
    }
}
