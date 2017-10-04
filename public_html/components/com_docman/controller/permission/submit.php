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
class ComDocmanControllerPermissionSubmit extends ComDocmanControllerPermissionAbstract
{
    public function canBrowse()
    {
        return false;
    }

    /**
     * Submit view is meant to be used with new items only
     *
     * @return bool
     */
    public function canRead()
    {
        return !($this->getModel()->getState()->isUnique());
    }

    public function canEdit()
    {
        return false;
    }

    /**
     * User could pass through Joomla menu access checks, so has access
     *
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }

    public function canDelete()
    {
        return false;
    }
}