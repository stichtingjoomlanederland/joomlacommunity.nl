<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Factory;

defined('_JEXEC') || die;

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
		$input = Factory::getApplication()->input;
		$input->set('option', 'com_admintools');
		$input->set('view', 'Blocks');
		$input->set('task', 'browse');
		$input->set('layout', 'default');
		$input->set('format', 'html');

		if (class_exists('JRequest'))
		{
			JRequest::set([
				'option' => 'com_admintools',
				'view'   => 'Blocks',
				'task'   => 'browse',
				'layout' => 'default',
				'format' => 'html'
			], 'get', true);
		}
	}
}
