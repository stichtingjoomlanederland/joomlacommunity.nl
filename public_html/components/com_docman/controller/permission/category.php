<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
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
