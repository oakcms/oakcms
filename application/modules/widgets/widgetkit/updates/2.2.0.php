<?php

namespace YOOtheme\Widgetkit;

class Update220 implements Update
{
    public function run()
    {
        try {

            $app       = require(JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit.php');
            $db        = $app['db'];
            $provider  = $app['content'];
            $shortcode = $app['shortcode'];
            $contents  = $provider->findAll();

            $update = function ($data) use ($provider, $contents) {

                try {

                    if (!@$data['id'] || !@$data['widget'] || !@$contents[$data['id']]) {
                        return false;
                    }

                    $name    = $data['widget'];
                    $content = $contents[$data['id']];

                    unset($data['id'], $data['widget']);

                    if (isset($content['_widget'])) {
                        $content = clone $content;
                        $content->setId(null);
                    }

                    $content['_widget'] = array('name' => $name, 'data' => $data);

                    return $provider->save(array_filter($content->toArray()));

                } catch (\Exception $e) {
                    \JError::raiseWarning(null, 'Error executing update to 2.2.0. ('.$e->getMessage().')');
                }

                return false;
            };

            foreach ($db->fetchAll('
                    SELECT :content as source, "introtext" as field, `id`, `introtext` as `value` FROM @content WHERE `introtext` LIKE :search
                    UNION ALL
                    SELECT :content as source, "fulltext" as field, `id`, `fulltext` as `value` FROM @content WHERE `fulltext` LIKE :search
                    UNION ALL
                    SELECT :modules as source, "content" as field, `id`, `content` as `value` FROM @modules WHERE `content` LIKE :search
                ', array(
                    'content' => $db->replacePrefix('@content'),
                    'modules' => $db->replacePrefix('@modules'),
                    'search'  => '%[widgetkit%'
                )
            ) as $result) {
                $db->update($result['source'], array($result['field'] => $shortcode->parse('widgetkit', $result['value'], function ($attrs, $param, $tag, $shortcode) use ($update) {
                    return (!$data = $update($attrs)) ? $shortcode : "[{$tag} id=\"{$data['id']}\" name=\"{$data['name']}\"]";
                })), array('id' => $result['id']));
            }

            foreach ($db->fetchAll('SELECT `id`, `params` FROM @modules WHERE `module` = :module', array('module' => 'mod_widgetkit')) as $result) {

                if (!$params = json_decode($result['params'], true)
                    or !isset($params['widgetkit'])
                    or !$data = (is_array($params['widgetkit']) ? $params['widgetkit'] : json_decode($params['widgetkit'], true))
                    or !$data = $update($data)
                ) {
                    continue;
                }

                $params['widgetkit'] = json_encode(array('id' => $data['id']));

                $db->update($db->replacePrefix('@modules'), array('params' => json_encode($params)), array('id' => $result['id']));
            }

        } catch (\Exception $e) {
            \JError::raiseWarning(null, 'Error executing update to 2.2.0. ('.$e->getMessage().')');
        }
    }
}

return new Update220();
