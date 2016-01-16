<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 4.1.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

class AkeebaControllerCheckfile extends F0FController
{
	/**
	 * Overridden task dispatcher to whitelist specific tasks
	 *
	 * @param string $task The task to execute
	 *
	 * @return bool|null|void
	 */
	public function execute($task)
	{
		// We only allow specific tasks. If none matches, assume the user meant the "browse" task
		if (!in_array($task, array('step')))
		{
			$task = 'show';
		}

		$this->task = $task;

		parent::execute($task);
	}

	public function show()
	{
		parent::display(false);
	}

	public function step()
	{
		$checker = new F0FUtilsFilescheck('com_akeeba', AKEEBA_VERSION, AKEEBA_DATE);

		$idx = $this->input->getInt('idx', 0);
		$result = $checker->slowCheck($idx);

		echo '###' . json_encode($result) . '###';

		// Cut the execution short
		JFactory::getApplication()->close();
	}
}
