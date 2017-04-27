<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\components;

use Yii;
use app\modules\admin\components\behaviors\CacheFlush;
use creocoder\nestedsets\NestedSetsBehavior;

/**
 * Base CategoryModel. Shared by categories
 * @package yii\content\components
 * @inheritdoc
 */
class CategoryModel extends ActiveRecord
{
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    public $children;

    public function behaviors()
    {
        return [
            'cacheflush' => [
                'class' => CacheFlush::className(),
                'key' => [static::tableName().'_tree', static::tableName().'_flat']
            ],
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree'
            ]
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            return true;
        } else {
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

    }

    /**
     * @return ActiveQueryNS
     */
    public static function find()
    {
        return new ActiveQueryNS(get_called_class());
    }

    /**
     * Get cached tree structure of category objects
     * @return array
     */
    public static function tree()
    {
        $cache = Yii::$app->cache;
        $key = static::tableName().'_tree';

        $tree = $cache->get($key);
        if(!$tree){
            $tree = static::generateTree();
            $cache->set($key, $tree, 3600);
        }
        return $tree;
    }

    /**
     * Get cached flat array of category objects
     * @return array
     */
    public static function cats()
    {
        $cache = Yii::$app->cache;
        $key = static::tableName().'_flat';

        $flat = $cache->get($key);
        if(!$flat){
            $flat = static::generateFlat();
            $cache->set($key, $flat, 3600);
        }
        return $flat;
    }

    /**
     * Generates tree from categories
     * @return array
     */
    public static function generateTree()
    {
        $collection = static::find()->sort()->all();
        $trees = array();

        if (count($collection) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = array();

            foreach ($collection as $node) {
                $item = $node;
                unset($item['lft'], $item['rgt'], $item['order']);
                $item['children'] = array();

                // Number of stack items
                $l = count($stack);

                // Check if we're dealing with different levels
                while($l > 0 && $stack[$l - 1]->depth >= $item['depth']) {
                    array_pop($stack);
                    $l--;
                }

                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root node
                    $i = count($trees);
                    $trees[$i] = (object)$item;
                    $stack[] = & $trees[$i];

                } else {
                    // Add node to parent
                    $item['parent'] = $stack[$l - 1]->id;
                    $i = count($stack[$l - 1]->children);
                    $stack[$l - 1]->children[$i] = (object)$item;
                    $stack[] = & $stack[$l - 1]->children[$i];
                }
            }
        }

        return $trees;
    }

    public static function generateFlat()
    {
        $collection = static::find()->sort()->all();
        $flat = [];

        if (count($collection) > 0) {
            $depth = 0;
            $lastId = 0;
            foreach ($collection as $node) {
                $node = (object)$node;
                $id = $node->id;
                $node->parent = '';

                if($node->depth > $depth){
                    $node->parent = $flat[$lastId]->id;
                    $depth = $node->depth;
                } elseif($node->depth == 0){
                    $depth = 0;
                } else {
                    if ($node->depth == $depth) {
                        $node->parent = $flat[$lastId]->parent;
                    } else {
                        foreach($flat as $temp){
                            if($temp->depth == $node->depth){
                                $node->parent = $temp->parent;
                                $depth = $temp->depth;
                                break;
                            }
                        }
                    }
                }
                $lastId = $id;
                unset($node->lft, $node->rgt);
                $flat[$id] = $node;
            }
        }

        foreach($flat as &$node){
            $node->children = [];
            foreach($flat as $temp){
                if($temp->parent == $node->id){
                    $node->children[] = $temp->id;
                }
            }
        }
        return $flat;
    }
}
