<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperUser extends KTemplateHelperAbstract
{
   function groups($config = array())
   {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select('title');
      $query->from($db->quoteName('#__usergroups') . 'AS ug');
      $query->join('LEFT', $db->quoteName('#__user_usergroup_map') . 'AS map ON ug.id = map.group_id');
      $query->where('map.user_id = ' . (int) $config['user_id']);
      $db->setQuery($query);

      return $db->loadColumn();
   }
}
