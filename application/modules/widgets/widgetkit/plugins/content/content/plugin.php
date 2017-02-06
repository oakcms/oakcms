<?php

$config = array(
    'name' => 'content/content',
    'main' => 'YOOtheme\\Widgetkit\\Content\\Type',
    'config' => array(

        'name'  => 'content',
        'label' => 'Content',
        'icon'  => 'plugins/content/content/content.svg',
        'item'  => array('title', 'content', 'link', 'media'),
        'data'  => array(
            'number'        => 5,
            'category'      => '0',
            'subcategories' => '0',
            'content'       => 'intro',
            'image'         => 'intro',
            'link'          => '',
            'order_by'      => 'order',
            'date'          => 'publish_up',
            'author'        => 'author',
            'categories'    => 'categories',

        )

    ),

    'items' => function ($items, $content, $app) {

//        $args = array(
//            'order'         => $content['order_by'] ?: 'order'
//        );

        $query = \app\modules\content\models\ContentArticles::find()
            ->joinWith(['translations'])
            ->published()
            ->andWhere([
                'category_id' => $content['category'] ? (array) $content['category'] : 0,
            ]);
        if($content['number']) {
            $query->limit($content['number']);
        }
        if($content['subcategories']) {
            $query->leaves();
        }
        if($content['order_by']) {
            switch ($content['order_by']) {
                case "date":
                    $query->orderBy(['created_at' => SORT_ASC]);
                    break;
                case "rdate":
                    $query->orderBy(['created_at' => SORT_DESC]);
                    break;
                case "modified":
                    $query->orderBy(['updated_at' => SORT_ASC]);
                    break;
                case "rmodified":
                    $query->orderBy(['updated_at' => SORT_DESC]);
                    break;
                case "alpha":
                    $query->orderBy(['{{%content_articles_lang}}.title' => SORT_ASC]);
                    break;
                case "ralpha":
                    $query->orderBy(['{{%content_articles_lang}}.title' => SORT_DESC]);
                    break;
                case "hits":
                    $query->orderBy(['hits' => SORT_ASC]);
                    break;
                case "rhits":
                    $query->orderBy(['hits' => SORT_DESC]);
                    break;
                case "random":
                    $query->orderBy(new \yii\db\Expression('rand()'));
                    break;
            }
        }

        $model = $query->all();

        foreach ($model as $item) {

            $data = array(
                'model'   => $item,
                'title'   => $item->title,
                'media'   => $content['image'] == 'intro' ? $item->getThumbUploadUrl('image') : $item->getUploadUrl('image'),
                'media2'  => $item->getUploadUrl('image'),
                'content' => $app['filter']->apply($item->{$content['content']}, 'content'),
                'link'    => \yii\helpers\Url::to($item->getFrontendViewLink()),
                'tags'    => array(),
                'author'  => '',

                'categories' => ''
            );

            if (!empty($content['date'])) {
                $data['date'] = $content['date'] == 'created' ? $item->created_at : $item->published_at;
            }

            $items->add($data);
        }

    },

    'events' => array(

        'init.admin' => function ($event, $app) {
            $app['angular']->addTemplate('content.edit', 'plugins/content/content/views/edit.php');
        }

    )

);

return $config;