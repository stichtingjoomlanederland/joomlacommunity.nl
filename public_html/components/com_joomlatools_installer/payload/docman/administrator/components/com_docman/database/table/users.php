<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDatabaseTableUsers extends KDatabaseTableAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'name' => 'users',
        ));

        parent::_initialize($config);
    }

    public function getPermissions()
    {
      $permissions = array();

      $permissions['core.admin']  = $this->canPerform('admin');
      $permissions['core.manage'] = $this->canPerform('manage');

      return $permissions;
    }

    public function canPerform($action)
    {
        $user = $this->getObject('user');

        if (!$user->isAuthentic()) {
            return false;
        }

        $joomla_action = 'core.' . $action;
        $asset_name    = 'com_users';

        return (bool) $user->authorise($joomla_action, $asset_name);
    }
}
