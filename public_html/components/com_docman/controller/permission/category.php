<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Category controller permissions
 */
class ComDocmanControllerPermissionCategory extends ComDocmanControllerPermissionAbstract
{
    public function canAdd()
    {
        $result = parent::canAdd();

        // Only do this check on POST since we want to return true on toolbar canAdd calls for documents
        if ($this->getRequest()->getMethod() !== 'GET') {
            $page = JFactory::getApplication()->getMenu()->getItem($this->getRequest()->query->Itemid);

            if ($page && !$page->params->get('allow_category_add', 1) && !$this->canAdmin()) {
                return false;
            }
        }

        if ($this->getModel()->getState()->isUnique())
        {
            $category = $this->getModel()->fetch();

            if ($category && $category->isPermissible()) {
                $result = $category->canPerform('add');
            }
        }

        return (bool) $result;
    }
}
