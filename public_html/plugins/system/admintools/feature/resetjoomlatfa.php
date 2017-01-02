<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureResetjoomlatfa extends AtsystemFeatureAbstract
{
	protected $loadOrder = 920;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->helper->isFrontend())
		{
			return false;
		}

		if ($this->cparams->getValue('resetjoomlatfa', 0) != 1)
		{
			return false;
		}

		$option = $this->input->getCmd('option', 'com_foobar');
		$task   = $this->input->getCmd('task', 'default');

		if (!(($option == 'com_users') && ($task == 'complete')))
		{
			return false;
		}

		return true;
	}

	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		$db = $this->db;

		$query = $db->getQuery(true)
			->update($db->qn('#__users'))
			->set(array(
				$db->qn('otpKey') . ' = ' . $db->q(''),
				$db->qn('otep') . ' = ' . $db->q(''),
			))
			->where($db->qn('id') . ' = ' . $db->q($user['id']));

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			// Do nothing if the query fails
		}
	}
}