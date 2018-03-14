<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Submit controller permissions
 */
class ComDocmanControllerPermissionList extends ComDocmanControllerPermissionAbstract
{
    public function canRead()
    {
        return !$this->getModel()->fetch()->isNew();
    }

    public function canAdd()
    {
        // Only do this check on POST since we want to return true on toolbar canAdd calls for documents
        if ($this->getRequest()->getMethod() !== 'GET') {
            $page = JFactory::getApplication()->getMenu()->getItem($this->getRequest()->query->Itemid);

            if ($page && !$page->params->get('allow_category_add', 1) && !$this->canAdmin()) {
                return false;
            }
        }

        // If we are on a certain category make sure user can add something here
        if ($this->getRequest()->query->slug)
        {
            $category = $this->getModel()->fetch();
            $result   = (!$category->isPermissible() || $category->canPerform('add'));
        }
        else
        {
            $result = parent::canAdd();
        }

        return $result;
    }
}