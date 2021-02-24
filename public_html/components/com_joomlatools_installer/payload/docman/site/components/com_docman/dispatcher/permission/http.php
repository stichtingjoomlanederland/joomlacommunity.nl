<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Http Dispatcher Permission
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Docman\Library\Dispatcher
 */
class ComDocmanDispatcherPermissionHttp extends ComKoowaDispatcherPermissionAbstract
{
    /**
     * Checks if there is an active menu item
     *
     * @throws RuntimeException
     * @return bool
     */
    public function canDispatch()
    {
        $menu = JFactory::getApplication()->getMenu()->getActive();

        if (!$menu || $menu->id != $this->getRequest()->query->Itemid) {
            $result = in_array($this->getRequest()->query->view, array('doclink', 'documents'));
        } else {
            $result = $menu->query['option'] === 'com_docman';
        }

        if (!$result) {
            throw new RuntimeException($this->getObject('translator')->translate('Invalid menu item'));
        }

        return true;
    }
}