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
}
