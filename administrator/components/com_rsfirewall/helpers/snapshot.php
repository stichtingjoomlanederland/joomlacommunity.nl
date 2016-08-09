<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

abstract class RSFirewallSnapshot
{
	public static function create($user) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from('#__user_usergroup_map')
			  ->where($db->qn('user_id').'='.(int) $user->id);
		$db->setQuery($query);
		
		$snapshot = (object) array(
			'adjacent' 	=> array(
				// #__user_usergroup_map
				'user_usergroup_map' => $db->loadObjectList()
			),
			// #__users
			'user_id' 	=> $user->id,
			'name' 		=> $user->name,
			'username' 	=> $user->username,
			'email' 	=> $user->email,
			'password' 	=> $user->password,
			'block' 	=> $user->block,
			'sendEmail' => $user->sendEmail,
			'params' 	=> $user->params
		);
		
		return base64_encode(serialize($snapshot));
	}
	
	public static function get($type) {
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*')
			  ->from('#__rsfirewall_snapshots')
			  ->where($db->qn('type').'='.$db->q($type));
		$db->setQuery($query);
		$results = $db->loadObjectList('user_id');
		
		$return = array();
		if (!empty($results)) {
			foreach ($results as $result)
				$return[$result->user_id] = unserialize(base64_decode($result->snapshot));
		}
		
		return $return;
	}
	
	public static function modified($current, $snapshot) {
		foreach ($snapshot as $key => $value) {
			if ($key == 'user_id') continue;
			if ($key == 'adjacent') continue;
			if ($current->$key != $value)
				return array('key' => $key, 'value' => $current->$key, 'snapshot' => $value);
		}
		
		return false;
	}
	
	public static function replace($snapshot, $update = false) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);

		// Don't add empty users
		if (!$snapshot->user_id || !strlen($snapshot->username) || !strlen($snapshot->password) || !strlen($snapshot->email)) {
			return false;
		}

		if ($update) {
			// update
			$query->update('#__users')
				  ->where($db->qn('id').'='.$db->q($snapshot->user_id));
		} else {
			// insert
			$query->insert('#__users')
				  ->set($db->qn('id').'='.$db->q($snapshot->user_id))
				  ->set($db->qn('registerDate').'='.$db->q(JFactory::getDate()->toSql()));
		}
		
		$query->set($db->qn('name').'='.$db->q($snapshot->name))
			  ->set($db->qn('username').'='.$db->q($snapshot->username))
			  ->set($db->qn('email').'='.$db->q($snapshot->email))
			  ->set($db->qn('password').'='.$db->q($snapshot->password))
			  ->set($db->qn('block').'='.$db->q($snapshot->block))
			  ->set($db->qn('sendEmail').'='.$db->q($snapshot->sendEmail))
			  ->set($db->qn('params').'='.$db->q($snapshot->params));
		$db->setQuery($query);
		
		$db->execute();
		
		// adjacent
		if (!empty($snapshot->adjacent)) {
			foreach ($snapshot->adjacent as $adjacent_table => $values) {
				foreach ($values as $value) {
					if (!is_object($value)) {
						continue;
					}
					$query = $db->getQuery(true);					
					$query->select('*')
						  ->from($db->qn('#__'.$adjacent_table));
					// Let's check if the data already matches
					foreach (get_object_vars($value) as $k => $v) {
						$query->where($db->qn($k).'='.$db->q($v));
					}
					$db->setQuery($query);
					if (!$db->loadObject()) {
						$query->clear();
						$query->insert('#__'.$adjacent_table)
							  ->columns(array_keys(get_object_vars($value)))
							  ->values(implode(',',array_values(get_object_vars($value))));
						
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
	}
}