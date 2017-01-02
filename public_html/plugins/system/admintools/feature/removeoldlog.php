<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureRemoveoldlog extends AtsystemFeatureAbstract
{
	protected $loadOrder = 110;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('maxlogentries', 0) > 0);
	}

	/**
	 * Deletes old log entries, keeping up to maxlogentries entries.
	 */
	public function onAfterInitialise()
	{
		// Delete up to 100 old entries
		$maxEntries = $this->params->get('maxlogentries', 0);
		$db = $this->db;
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__admintools_log'))
			->order($db->qn('id') . ' DESC');
		$db->setQuery($query, $maxEntries, 100);
		$ids = $db->loadColumn(0);

		if (!count($ids))
		{
			return;
		}

		$temp = array();

		foreach ($ids as $id)
		{
			$temp[] = $db->q($id);
		}

		$ids = implode(',', $temp);

		$query = $db->getQuery(true)
			->delete($db->qn('#__admintools_log'))
			->where($db->qn('id') . ' IN(' . $ids . ')');
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (Exception $exc)
		{
			// Do nothing on DB exception
		}
	}
} 