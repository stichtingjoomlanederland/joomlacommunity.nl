<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproTableSubscription extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rseventspro_users', 'id', $db);
	}
	
	/**
	 * Overloaded bind function
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @since	1.6
	 */
	public function bind($array, $ignore = '') {
		if (!isset($array['id']) || empty($array['id'])) {
			if ($fields = $this->getFields()) {
				foreach ($fields as $key => $field) {
					if (!isset($array[$key])) {
						if (strpos(strtolower($field->Type), 'int') !== false || strpos(strtolower($field->Type), 'float') !== false) {
							$array[$key] = 0;
						} elseif (strpos(strtolower($field->Type), 'datetime') !== false) {
							$array[$key] = JFactory::getDbo()->getNullDate();
						} else {
							$array[$key] = '';
						}
					}
				}
			}
		}
		
		return parent::bind($array, $ignore);
	}
	
	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     JTable::check
	 * @since   11.1
	 */
	public function check() {
		if (!$this->id) {
			$this->date = JFactory::getDate()->toSql();
			$this->verification = md5($this->ide.$this->name);
			$this->ip = rseventsproHelper::getIP();
			$this->lang = JFactory::getLanguage()->getTag();
			
			if (rseventsproHelper::getConfig('create_user') == 2) {
				$this->create_user = 1;
			}
		}
		
		return true;
	}
	
	/**
	 * Method to delete a node and, optionally, its child nodes from the table.
	 *
	 * @param   integer  $pk        The primary key of the node to delete.
	 * @param   boolean  $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     http://docs.joomla.org/JTable/delete
	 * @since   2.5
	 */
	public function delete($pk = null, $children = false) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		// Remove the tickets
		$query->clear()
			->delete($db->qn('#__rseventspro_user_tickets'))
			->where($db->qn('ids').' = '.(int) $pk);
		
		$db->setQuery($query);
		$db->execute();
		
		// Remove ticket seats
		$query->clear()
			->delete($db->qn('#__rseventspro_user_seats'))
			->where($db->qn('ids').' = '.(int) $pk);
		
		$db->setQuery($query);
		$db->execute();
		
		// Remove confirmed tickets
		$query->clear()
			->delete($db->qn('#__rseventspro_confirmed'))
			->where($db->qn('ids').' = '.(int) $pk);
		
		$db->setQuery($query);
		$db->execute();
		
		$query->clear()
			->select($db->qn('e.id'))->select($db->qn('e.sync'))->select($db->qn('u.SubmissionId'))
			->from($db->qn('#__rseventspro_users','u'))
			->join('left', $db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.id').' = '.$db->qn('u.ide'))
			->where($db->qn('u.id').' = '.(int) $pk);
		
		$db->setQuery($query);
		$subscription = $db->loadObject();
		
		// Delete RSForm!Pro submission
		if (file_exists(JPATH_SITE.'/components/com_rsform/rsform.php') && $subscription->sync) {
			$query->clear()
				->delete()
				->from($db->qn('#__rsform_submission_values'))
				->where($db->qn('SubmissionId').' = '.(int) $subscription->SubmissionId);
			
			$db->setQuery($query);
			$db->execute();
			
			$query->clear()
				->delete()
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('SubmissionId').' = '.(int) $subscription->SubmissionId);
			
			$db->setQuery($query);
			$db->execute();
		}
		
		JFactory::getApplication()->triggerEvent('rsepro_beforeDeleteSubscription', array(array('id' => $pk)));
		
		return parent::delete($pk, $children);
	}
}