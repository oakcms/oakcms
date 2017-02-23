<?php
/**
 * Class NestedSetsBehavior
 *
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\behaviors;

use yii\db\ActiveRecord;
use yii\db\Exception;

class NestedSetsBehavior extends \creocoder\nestedsets\NestedSetsBehavior
{
    static private $_orderCache;
    public $orderingAttribute = 'ordering';
    public $depthAttribute = 'level';

    /**
     * Сортировка дерева начиная с $this элемента. Сортировка поуровневая.
     *
     * @param string $orderByAttribute Колонка по которой будет сортироватся элементы(ex: ordering asc/lft asc).
     * @param int    $orderByDir
     *
     * @throws Exception
     * @throws \Exception
     */
    public function reorderNode($orderByAttribute, $orderByDir = SORT_ASC)
    {
        $db = $this->owner->getDb();

        $transaction = $db->getTransaction() === null ? $db->beginTransaction() : null;

        try {
            self::$_orderCache = [];

            $this->applyNodeOrder([$orderByAttribute => $orderByDir], $this->owner->{$this->leftAttribute}, $this->owner->{$orderByAttribute});

            foreach (self::$_orderCache as $node) {

                /** @var $node ActiveRecord */
                $node->updateAttributes([$this->leftAttribute, $this->rightAttribute, $this->orderingAttribute]);
            }

            if ($transaction !== null) {
                $transaction->commit();
            }

        } catch (Exception $e) {

            if ($transaction !== null) {
                $transaction->rollback();
            }

            throw $e;
        }
    }

    /**
     * Рекурсивная функция проходящяя по уровням дерева, применяя порядок $orderBy к выборке,
     * с последущей нумерацией поля $this->owner->{$this->orderingAttribute}, и перестройки атрибутов lft, rgt
     * @param $orderBy array ['ordering' => 'ASC']
     * @param $leftId  integer
     * @param $order   integer
     *
     * @return mixed
     */
    public function applyNodeOrder($orderBy, $leftId, $order)
    {
        $children = $this->children(1)->orderBy($orderBy)->all();

        $rightId = $leftId + 1;

        foreach ($children as $i => $node) {
            $rightId = $node->applyNodeOrder($orderBy, $rightId, $i + 1);
        }

        $this->owner->{$this->leftAttribute} = $leftId;
        $this->owner->{$this->rightAttribute} = $rightId;
        $this->owner->{$this->orderingAttribute} = $order;

        self::$_orderCache[] = $this->owner;

        return $rightId + 1;
    }
}
