<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorSluggable extends KControllerBehaviorAbstract
{
    /**
     * Populated in before.edit to see what changed in after.edit
     *
     * @var array
     */
    protected $_slug_cache = array();

    /**
     * Caches current values for categories
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeEdit(KControllerContextInterface $context)
    {
        $entities = $this->getModel()->fetch();

        foreach ($entities as $entity) {
            $this->_slug_cache[$entity->id] = $entity->slug;
        }
    }

    /**
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterEdit(KControllerContextInterface $context)
    {
        if (count($context->result))
        {
            $pages = $this->getObject('com://admin/docman.model.pages')->view(['tree', 'list'])->fetch();

            foreach ($context->result as $entity)
            {
                if (isset($this->_slug_cache[$entity->id]) && $this->_slug_cache[$entity->id] !== $entity->slug)
                {
                    $old_slug = $this->_slug_cache[$entity->id];

                    foreach($pages as $page)
                    {
                        $slug = isset($page->query['slug']) ? $page->query['slug'] : null;

                        if ($slug && $slug === $old_slug)
                        {
                            $table = $this->getObject('com://admin/docman.database.table.menus', array('name' => 'menu'));
                            $item  = $table->select(['id' => $page->id], KDatabase::FETCH_ROW);
                            $item->link = str_replace('slug='.$old_slug, 'slug='.$entity->slug, $page->link);
                            $item->save();
                        }
                    }
                }
            }
        }
    }
}