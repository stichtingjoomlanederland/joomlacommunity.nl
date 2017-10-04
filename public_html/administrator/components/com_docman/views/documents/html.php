<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDocumentsHtml extends ComDocmanViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        if ($this->getModel()->getState()->category)
        {
            $category = $this->getObject('com://admin/docman.model.categories')
                ->id($this->getModel()->getState()->category)->fetch();

            $context->data->category = $category;
        }

        $context->data->category_count = $this->getObject('com://admin/docman.model.categories')->count();
        $context->data->document_count = $this->getObject('com://admin/docman.model.documents')->count();
        $context->data->can_create_tag = $this->getObject('com://admin/docman.model.configs')->fetch()->can_create_tag;

        parent::_fetchData($context);
    }
}
