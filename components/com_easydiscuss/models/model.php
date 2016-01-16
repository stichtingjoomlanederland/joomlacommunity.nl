<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

if( DiscussHelper::getJoomlaVersion() >= '3.0' )
{
	class EasyDiscussModel extends JModelLegacy
	{
		protected function populateState()
		{
			// Load the parameters.
			$value = JComponentHelper::getParams($this->option);
			$this->setState('params', $value);
		}
	}
}
else
{
	jimport('joomla.application.component.model');
	class EasyDiscussModel extends JModel
	{

	}
}