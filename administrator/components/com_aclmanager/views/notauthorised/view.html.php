<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2014 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * HTML View class for the ACL Manager component
 */
class AclmanagerViewNotauthorised extends JViewLegacy
{
	public function display($tpl = null)
	{
		// Display the view
		parent::display($tpl);
	}
}