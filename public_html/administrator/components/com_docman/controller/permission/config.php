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
class ComDocmanControllerPermissionConfig extends ComDocmanControllerPermissionAbstract
{
    public function canRender()
    {
        return $this->canAdmin();
    }

    public function canRead()
    {
        return $this->canAdmin();
    }

    public function canBrowse()
    {
        return $this->canAdmin();
    }

    public function canAdd()
    {
        return $this->canAdmin();
    }

    public function canEdit()
    {
        return $this->canAdmin();
    }

    public function canDelete()
    {
        return $this->canAdmin();
    }
}