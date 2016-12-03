<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Extend Joomla core class
 */
class ModulesViewModules extends ModulesViewModulesCore
{
	public function display($tpl = null)
	{
		$this->addTemplatePath(dirname(__FILE__) . '/tmpl/');
		parent::display($tpl);
	}
}
