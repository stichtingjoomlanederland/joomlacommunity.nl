<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
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
		if (!$this->container->platform->getSessionVar('block', false, 'com_admintools'))
		{
			return;
		}

		// This is an underhanded way to short-circuit Joomla!'s internal router.
		$input = JFactory::getApplication()->input;
		$input->set('option', 'com_admintools');
		$input->set('view', 'Blocks');
		$input->set('task', 'browse');
		$input->set('layout', 'default');

		if (class_exists('JRequest'))
		{
			JRequest::set([
				'option' => 'com_admintools',
				'view'   => 'Blocks',
				'task'   => 'browse',
				'layout' => 'default',
			], 'get', true);
		}
	}
}
