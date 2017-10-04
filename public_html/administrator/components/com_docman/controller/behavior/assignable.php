<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Assigns and removes users from groups
 */
class ComDocmanControllerBehaviorAssignable extends KControllerBehaviorAbstract
{
    /**
     * Sets the default access level to inherit for documents and Joomla default for categories
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeEdit(KControllerContextInterface $context)
    {
        $data = $context->request->data;

        $assign = (array) $data->assign_group;
        $remove = (array) $data->remove_group;

        $users = $this->getModel()->fetch();

        $current_user = $this->getObject('user');

        foreach ($users as $user)
        {
            if ($user->id === $current_user->getId())
            {
                $is_admin = $current_user->authorise('core.admin');

                if ($is_admin && count($remove))
                {
                    $groups = $current_user->getGroups();

                    if ($assign) {
                        $groups = array_merge($groups, $assign);
                    }

                    if ($remove) {
                        $groups = array_diff($groups, $remove);
                    }

                    $is_still_admin = false;

                    foreach ($groups as $group) {
                        if (JAccess::checkGroup($group, 'core.admin')) {
                            $is_still_admin = true;
                            break;
                        }
                    }

                    if (!$is_still_admin) {
                        continue;
                    }

                }
            }

            try
            {
                foreach ($assign as $group) {
                    JUserHelper::addUserToGroup($user->id, $group);
                }

                foreach ($remove as $group) {
                    JUserHelper::removeUserFromGroup($user->id, $group);
                }
            } catch (Exception $e) {}
        }
    }
}