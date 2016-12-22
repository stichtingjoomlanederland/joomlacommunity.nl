<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
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