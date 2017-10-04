<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewCategoriesHtml extends ComDocmanViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        $context->data->category_count = $this->getObject('com://admin/docman.model.categories')->count();

        parent::_fetchData($context);

        $context->data->categories->setDocumentCount();

        if ($this->getModel()->getState()->parent_id)
        {
            $parent = $this->getObject('com://admin/docman.model.categories')
                ->id($this->getModel()->getState()->parent_id)->fetch();

            $context->data->parent = $parent;
        }
    }
}
