<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelIgnored extends JModelLegacy
{
	public function getFiles()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->qn('#__rsfirewall_hashes'))
			->where($db->qn('type') . '=' . $db->q('ignore'));
		$db->setQuery($query);

		return $db->loadObjectList('id');
	}

	public function remove($id)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$message = array(
			'status'  => false,
			'message' => ''
		);

		try
		{
			$query->delete($db->qn('#__rsfirewall_hashes'))
				->where($db->qn('id') . '=' . $db->q($id))
				->where($db->qn('type') . '=' . $db->q('ignore'));
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception(JText::_('COM_RSFIREWALL_FILES_READD_ERROR'));
			}

			$message['status']  = true;
			$message['message'] = JText::_('COM_RSFIREWALL_FILES_READDED_TO_CHECK');

		} catch (Exception $e)
		{
			$message['message'] = $e->getMessage();
		}

		echo json_encode($message);
		jexit();
	}
}