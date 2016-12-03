<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * JSON View class for the ACL Manager component
 */
class AclmanagerViewHome extends JViewLegacy
{
	function display($tpl = null) {
		$type	= JFactory::getApplication()->input->get('type');
		$output	= $this->get('TableData');
		$output['aaData'] = array();

		// Load User data
		if($type == 'user'){
			$row = array();
			foreach($output['data'] as $user){
				$row[0] = '<a href="index.php?option=com_aclmanager&view=user&id='.$user[2].'">'.$user[0].'</a>';
				$row[1] = $user[1];
				$row[2] = $user[2];
				$output['aaData'][] = $row;
			}
		// Load User Groups data
		} elseif ($type == 'group'){
			$row = array();
			foreach($output['data'] as $group){
				$row[0] = str_repeat('<span class="gi">|&mdash;</span>', $group[2]).'<a href="index.php?option=com_aclmanager&view=group&id='.$group[1].'">'.$group[0].'</a>';
				$row[1] = $group[1];
				$output['aaData'][] = $row;
			}
		}

		// Send the JSON response.
		echo json_encode($output);

		// Close the application.
		JFactory::getApplication()->close();
	}
}

