<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Provides ordering support for closure tables by the help of another table
 */
class ComDocmanDatabaseBehaviorCategoryOrderable extends KDatabaseBehaviorAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'  => self::PRIORITY_LOWEST,
        ));

        parent::_initialize($config);
    }

    protected function _beforeSelect(KDatabaseContextInterface $context)
    {
        $query = $context->query;

        if ($query)
        {
            $params    = $context->query->params;
            $id_column = $context->getSubject()->getIdentityColumn();

            // To display the custom ordering in backend
            if (!$query->isCountQuery())
            {
                $query->columns(array('ordering' => 'ordering2.custom'))
                    ->join(array('ordering2' => 'docman_category_orderings'), 'tbl.' . $id_column . ' = ordering2.' . $id_column, 'left');
            }

            // Force the sort if we are not fetching immediate children of a category
            if ($params && !(in_array($params->level, [1, [1]]) && $params->sort !== 'custom'))
            {
                if (in_array($params->sort, array('title', 'created_on', 'custom')))
                {
                    $query->order = array();

                    $column = sprintf('GROUP_CONCAT(LPAD(`ordering`.`%s`, 5, \'0\') ORDER BY crumbs.level DESC  SEPARATOR \'/\')', $params->sort);

                    $query->join(array('ordering' => 'docman_category_orderings'), 'crumbs.ancestor_id = ordering.' . $id_column, 'inner')
                        ->columns(array('order_path' => $column))
                        ->order('order_path', 'ASC');
                }
            }
        }
    }

    protected function _afterInsert(KDatabaseContextInterface $context)
    {
        $entity    = $context->data;
        $siblings  = $entity->getSiblings();
        $orderings = array(
            'title' => array(),
            'created_on' => array(),
            'custom' => array()
        );
        $custom_values = array();
        $sibling_ids   = array();

        foreach ($siblings as $sibling) {
            $sibling_ids[] = $sibling->id;
        }

        if ($sibling_ids) {
            $orders = $this->getObject('com://admin/docman.model.category_orderings')
                ->id($sibling_ids)->sort('custom')->direction('asc')->fetch();
        } else {
            $orders = array();
        }

        foreach ($orders as $order) {
            $custom_values[$order->id] = $order->custom;
        }

        $next_order = ($custom_values ? max($custom_values) : 0)+1;

        foreach ($siblings as $child)
        {
            $orderings['title'][$child->id] = $child->title;
            $orderings['created_on'][$child->id] = $child->created_on;
            $orderings['custom'][$child->id] = isset($custom_values[$child->id]) ? $custom_values[$child->id] : $next_order++;
        }

        if ($entity->order)
        {
            // Pre-sort custom values
            asort($orderings['custom']);

            $ids = array_keys($orderings['custom']);
            $position = array_search($entity->id, $ids);
            $newPosition = $position + $entity->order;

            $temp = array_flip($orderings['custom']);

            foreach($temp as $i => $custom) {

               if($custom == $entity->id) {
                 unset($temp[$i]);
                 break;
               }
            }

            $temp = array_values($temp);

            array_splice($temp, $newPosition, 0, $entity->id);

            //start array from 1 instead of 0
            $temp = array_combine(range(1, count($temp)), array_values($temp));

            $orderings['custom'] = array_flip($temp);
        }

        // Sort before saving orders
        foreach ($orderings as $key => &$array)
        {
            if ($key === 'title') {
                $array = array_map('strtolower', $array);
            }

            asort($array, SORT_REGULAR);
        }

        foreach ($siblings as $item)
        {
            $order = $orders->find($item->id);

            if (!$order)
            {
                $order = $orders->create();
                $order->id = $item->id;
            }

            foreach (array_keys($orderings) as $key) {
                $order->{$key} = array_search($item->id, array_keys($orderings[$key])) + 1;
            }

            $order->save();
        }
    }

    protected function _afterUpdate(KDatabaseContextInterface $context)
    {
        $this->_afterInsert($context);
    }

    protected function _afterDelete(KDatabaseContextInterface $context)
    {
        $this->getObject('com://admin/docman.model.category_orderings')
            ->id($context->data->id)
            ->fetch()
            ->delete();
    }
}
