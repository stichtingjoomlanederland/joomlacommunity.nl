<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsTableComment extends JTable
{
	public function __construct(&$db) {
		parent::__construct('#__rscomments_comments', 'IdComment', $db);
	}
	
	public function publish($pks = null, $value = 1, $userid = 0) {
		if (JFactory::getApplication()->input->getCmd('task') == 'publish') {
			if ($pks) {
				require_once JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php';
				
				foreach ($pks as $id) {
					$table = JTable::getInstance('Comment', 'RscommentsTable', array('dbo' => $this->getDbo()));
					$table->load($id);
					RSCommentsHelper::sendEmailSubscriptions($table);
				}
			}
		}
		
		return parent::publish($pks, $value, $userid);
	}
	
	public function store($updateNulls = false) {
		if (parent::store($updateNulls)) {			
			if ($this->published) {
				require_once JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php';
			
				RSCommentsHelper::sendEmailSubscriptions($this);
			}
			
			return true;
		}
		
		return false;
	}
}