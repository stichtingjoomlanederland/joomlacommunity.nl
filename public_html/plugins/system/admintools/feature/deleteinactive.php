<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureDeleteinactive extends AtsystemFeatureAbstract
{
	protected $loadOrder = 100;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('deleteinactive', 0) == 1);
	}

	/**
	 * Deletes inactive users (not activated or not visited the site for too long).
	 */
	public function onAfterInitialise()
	{
		// If the days are not at least 1, bail out
		$filtertype = (int)$this->params->get('deleteinactive', 1);
		$days       = (int)$this->params->get('deleteinactive_days', 0);

		if ($days <= 0)
		{
			return;
		}

		// Get up to 5 ids of users to remove
		$db = $this->db;

		$sql = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__users'))
			->where($db->qn('lastvisitDate') . ' = ' . $db->q($db->getNullDate()))
			->where($db->qn('registerDate') . ' <= ' . "DATE_SUB(NOW(), INTERVAL $days DAY)");

		switch ($filtertype)
		{
			case 1:
				// Only users not yet activated
				$sql->where($db->qn('activation') . ' != ' . $db->quote(''));
				break;

			case 2:
				// Only users already activated
				$sql->where($db->qn('activation') . ' = ' . $db->quote(''));
				break;

			case 3:
				// All users who haven't logged in
				break;
		}


		$db->setQuery($sql, 0, 5);

		$ids = $db->loadColumn();

		// Remove those inactive users
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				$userToKill = JFactory::getUser($id);
				$userToKill->delete();
			}
		}
	}
} 