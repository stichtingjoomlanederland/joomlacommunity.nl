<?php
/**
 * @package	RSEventspro!
 * @copyright	(c) 2013 - 2018 RSJoomla!
 * @link		https://www.rsjoomla.com
 * @license	GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

JLoader::register('PrivacyPlugin', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/plugin.php');
JLoader::register('PrivacyRemovalStatus', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/removal/status.php');

/**
 * RSEventspro! Privacy Plugin.
 */
class PlgPrivacyRseventspro extends PrivacyPlugin
{
	const EXTENSION = 'plg_privacy_rseventspro';

	/**
	 * Can we run this plugin?
	 *
	 * @access public
	 *
	 * @return bool
	 */
	public function canRun() {
		return file_exists(JPATH_SITE . '/components/com_rseventspro/helpers/rseventspro.php');
	}

	/**
	 * Performs validation to determine if the data associated with a remove information request can be processed
	 *
	 * This event will not allow a super user account to be removed
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  PrivacyRemovalStatus
	 *
	 * @since   3.9.0
	 */
	public function onPrivacyCanRemoveData(PrivacyTableRequest $request, JUser $user = null) {
		$status = new PrivacyRemovalStatus;

		if (!$user) {
			return $status;
		}

		if ($user->authorise('core.admin')) {
			$status->canRemove = false;
			$status->reason    = JText::_('PLG_PRIVACY_RSEVENTSPRO_ERROR_CANNOT_REMOVE_SUPER_USER');
		}

		return $status;
	}

	/**
	 * Function that retrieves the information for the RSEvents!pro Component Capabilities
	 * @return array
	 *
	 */
	public function onPrivacyCollectAdminCapabilities() {
		if (!$this->canRun()) {
			return array();
		}

		$capabilities = array(
			JText::_('PLG_PRIVACY_RSEVENTSPRO_CAPABILITIES_GENERAL') => array(
				JText::_('PLG_PRIVACY_RSEVENTSPRO_CAPABILITIES_EVENTS'),
				JText::_('PLG_PRIVACY_RSEVENTSPRO_CAPABILITIES_REPORTED_EVENTS'),
				JText::_('PLG_PRIVACY_RSEVENTSPRO_CAPABILITIES_SUBSCRIPTION')
			)
		);

		return $capabilities;
	}
	/**
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  PrivacyExportDomain[]
	 *
	 * @since   3.9.0
	 */
	public function onPrivacyExportRequest(PrivacyTableRequest $request, JUser $user = null) {
		if (!$this->canRun()) {
			return array();
		}

		if (!$user) {
			return array();
		}

		/** @var JTableUser $userTable */
		$userTable = JUser::getTable();
		$userTable->load($user->id);

		$domains = array();
		$domains[] = $this->createUserEventsDomain($userTable);
		$domains[] = $this->createUserSubscriptionsDomain($userTable);
		$domains[] = $this->createUserRsvpDomain($userTable);

		return $domains;
	}

	/**
	 * Removes the data associated with a remove information request
	 *
	 * This event will pseudoanonymise the user account
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  void
	 *
	 * @since   3.9.0
	 */
	public function onPrivacyRemoveData(PrivacyTableRequest $request, JUser $user = null) {
		if (!$this->canRun()) {
			return;
		}

		// This plugin only processes data for registered user accounts
		if (!$user) {
			return;
		}
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		
		// Delete events
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('owner').' = '.$db->q($user->id));
		$db->setQuery($query);
		if ($ids = $db->loadColumn()) {
			foreach($ids as $id) {
				rseventsproHelper::remove($id);
			}
		}
		
		// Delete subscriptions
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('idu').' = '.$db->q($user->id));
		$db->setQuery($query);
		if ($ids = $db->loadColumn()) {
			foreach($ids as $id) {
				// Remove the tickets
				$query->clear()->delete($db->qn('#__rseventspro_user_tickets'))->where($db->qn('ids').' = '.(int) $id);
				$db->setQuery($query);
				$db->execute();
				
				// Remove ticket seats
				$query->clear()->delete($db->qn('#__rseventspro_user_seats'))->where($db->qn('ids').' = '.(int) $id);
				$db->setQuery($query);
				$db->execute();
				
				// Remove confirmed tickets
				$query->clear()->delete($db->qn('#__rseventspro_confirmed'))->where($db->qn('ids').' = '.(int) $id);
				$db->setQuery($query);
				$db->execute();
				
				JFactory::getApplication()->triggerEvent('rsepro_beforeDeleteSubscription', array(array('id' => $id)));
				
				// Remove subscription
				$query->clear()->delete($db->qn('#__rseventspro_users'))->where($db->qn('id').' = '.(int) $id);
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		// Delete rsvps
		$query->clear()
			->delete($db->qn('#__rseventspro_rsvp_users'))
			->where($db->qn('uid').' = '.(int) $user->id);
		
		$db->setQuery($query);
		$db->execute();
	}


	/**
	 * Create the domain for the events list
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   3.9.0
	 */
	private function createUserEventsDomain(JTableUser $user) {
		$domain = $this->createDomain('user_rseventspro_events', 'rseventspro_events_list');

		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->qn('#__rseventspro_events'))
			->where($this->db->qn('owner') . ' = '. $this->db->quote($user->id));

		$items = $this->db->setQuery($query)->loadAssocList();

		if (!empty($items)) {
			$items = ArrayHelper::dropColumn($items, 'owner');
			foreach ($items as $item) {
				$domain->addItem($this->createItemFromArray($item, $item['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for the subscriptions list
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   3.9.0
	 */
	private function createUserSubscriptionsDomain(JTableUser $user) {
		$domain = $this->createDomain('user_rseventspro_subscriptions', 'rseventspro_subscriptions_list');
		$app	= JFactory::getApplication();

		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->qn('#__rseventspro_users'))
			->where($this->db->qn('idu') . ' = '. $this->db->quote($user->id));

		$items = $this->db->setQuery($query)->loadAssocList();
		
		if ($items) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
			JFactory::getLanguage()->load('com_rseventspro', JPATH_SITE);
			
			foreach ($items as $i => $item) {
				$tickets = $this->getTickets($item);
				$items[$i]['tickets'] = $tickets['tickets'];
				$items[$i]['total'] = $tickets['total'];
				$items[$i]['discount'] = rseventsproHelper::currency($items[$i]['discount']);
				$items[$i]['early_fee'] = rseventsproHelper::currency($items[$i]['early_fee']);
				$items[$i]['late_fee'] = rseventsproHelper::currency($items[$i]['late_fee']);
				$items[$i]['tax'] = rseventsproHelper::currency($items[$i]['tax']);
			}
		}

		if (!empty($items)) {
			$items = ArrayHelper::dropColumn($items, 'idu');

			foreach ($items as $item) {
				$domain->addItem($this->createItemFromArray($item, $item['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for the RSVP subscriptions list
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   3.9.0
	 */
	private function createUserRsvpDomain(JTableUser $user) {
		$domain = $this->createDomain('user_rseventspro_rsvp', 'rseventspro_rsvp_list');

		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->qn('#__rseventspro_rsvp_users'))
			->where($this->db->qn('uid') . ' = '. $this->db->quote($user->id));

		$items = $this->db->setQuery($query)->loadAssocList();

		if (!empty($items)) {
			$items = ArrayHelper::dropColumn($items, 'uid');

			foreach ($items as $item) {
				$domain->addItem($this->createItemFromArray($item, $item['id']));
			}
		}

		return $domain;
	}
	
	protected function getTickets($item) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$total	= 0;
		$info	= array();
		
		$query->clear()
			->select($db->qn('ut.quantity'))->select($db->qn('t.id'))
			->select($db->qn('t.name'))->select($db->qn('t.price'))
			->from($db->qn('#__rseventspro_user_tickets','ut'))
			->join('left', $db->qn('#__rseventspro_tickets','t').' ON '.$db->qn('t.id').' = '.$db->qn('ut.idt'))
			->where($db->qn('ut.ids').' = '.(int) $item['id']);
		
		$db->setQuery($query);
		$tickets = $db->loadObjectList();
		
		if ($item['ide']) {
			foreach ($tickets as $ticket) {
				if ($ticket->price > 0) {
					$info[] = $ticket->quantity. ' x '.$ticket->name.' ('.rseventsproHelper::currency($ticket->price). ')';
					$total += $ticket->price * $ticket->quantity;
				} else {
					$info[] = $ticket->quantity. ' x '.$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'). ')';
				}
			}
			
			if (!empty($item['discount']) && !empty($total)) {
				$total = $total - $item['discount'];
			}
			
			if (!empty($item['early_fee']) && !empty($total)) {
				$total = $total - $item['early_fee'];
			}
			
			if (!empty($item['late_fee']) && !empty($total)) {
				$total = $total + $item['late_fee'];
			}
			
			if (!empty($item['tax'])) {
				$total = $total + $item['tax'];
			}
		} else {
			JFactory::getApplication()->triggerEvent('rsepro_paymentForm', array(array('id' => $item['id'], 'total' => &$total, 'info' => &$info)));
			$info = str_replace(array('<em>','</em>'), '', $info);
		}
		
		return array('total' => rseventsproHelper::currency($total), 'tickets' => implode(';',$info));
	}
}