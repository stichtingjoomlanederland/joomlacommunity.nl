<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewCategoryHtml extends ComDocmanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append([
            'decorator'  => $config->layout === 'form' ? 'koowa' : 'joomla'
        ]);

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $context->data->parent = $this->getModel()->fetch()->getParent();

        $category = $context->data->category;
        $ignored_parents = array();

        if ($category->id) {
            $ignored_parents[] = $category->id;
            foreach ($category->getDescendants() as $descendant) {
                $ignored_parents[] = $descendant->id;
            }
        }

        $context->data->ignored_parents = $ignored_parents;

        $table = $this->getObject('com://admin/docman.database.table.viewlevels');
        $entities = $table->select(array(), KDatabase::FETCH_ROWSET);

        $context->data->viewlevels = $entities->toArray();

        $access = (int) (JFactory::getConfig()->get('access') || 1);
        $context->data->default_access = $entities->find($access) ?: $entities->create();
    }
}
