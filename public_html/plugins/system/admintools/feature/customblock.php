<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureCustomblock extends AtsystemFeatureAbstract
{
	/**
	 * Shows the Admin Tools custom block message
	 */
	public function onAfterRoute()
	{
		$session = JFactory::getSession();

		if ($session->get('block', false, 'com_admintools'))
		{
			// This is an underhanded way to short-circuit Joomla!'s internal router.
			$input = JFactory::getApplication()->input;
			$input->set('option', 'com_admintools');
			$input->set('view', 'Blocks');
			$input->set('task', 'browse');

			if (class_exists('JRequest'))
			{
				JRequest::set(array(
					'option' => 'com_admintools',
					'view' => 'blocks'
				), 'get', true);
			}
		}
	}
}