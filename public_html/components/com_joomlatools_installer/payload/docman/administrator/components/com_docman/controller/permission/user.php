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
class ComDocmanControllerPermissionUser extends ComDocmanControllerPermissionAbstract
{
    public function canEdit()
    {
        return JFactory::getUser()->authorise('core.edit', 'com_users');
    }

    /**
     * Allow render for managers in json request
     * 
     * @return boolean
     */
    public function canRender()
    {
        $result = false;

        if ($this->getRequest()->getFormat() == 'json')
        {
            $result = $this->canManage();
        }
        else $result = parent::canRender();

        return $result;
    }
}
