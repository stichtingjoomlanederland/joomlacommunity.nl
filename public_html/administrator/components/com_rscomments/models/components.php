<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelComponents extends JModelList
{
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);

		$component 	= JFactory::getApplication()->input->get('component', '', 'string');

		$results = array();
		$query->select($db->qn('id').', '.$db->qn('title'));

		switch($component) {
			case 'com_content':
				$query->from($db->qn('#__content'));
				$query->where($db->qn('state').' IN (0,1,2)');
			break;
			case 'com_rsblog':
				$query->from($db->qn('#__rsblog_posts'));
				$query->where($db->qn('published').' IN (0,1)');
			break;
			case 'com_k2':
				$query->from($db->qn('#__k2_items'));
				$query->where($db->qn('published').' IN (0,1)');
			break;
			case 'com_flexicontent':
				$query->from($db->qn('#__flexicontent_items'));
				$query->where($db->qn('state').' IN (0,1)');
			break;
		}

		if ($search = $this->getState('filter.search')) {
			$query->where($db->qn('title').' LIKE '.$db->q('%'.$search.'%'));
		}

		$query->order( $db->qn('ordering').' ASC');
		return $query;
	}
}