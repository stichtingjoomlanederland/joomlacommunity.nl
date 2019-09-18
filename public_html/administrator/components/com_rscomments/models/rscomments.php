<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelRSComments extends JModelLegacy
{
	public function __construct($config = array())  {
		parent::__construct($config);
	}
	
	public function getCode() {
		$code = RSCommentsHelperAdmin::getConfig('global_register_code');
		return $code;
	}
	
	public function getLatestComments() {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);

		$query->select('*')
			->from($db->qn('#__rscomments_comments'))
			->order($db->qn('IdComment').' DESC');
			
		$db->setQuery($query, 0, 5);
		return $db->loadObjectList();
	}
	
	public function getTypes() {
		return array(
			JHtml::_('select.option', 0, JText::_('COM_RSCOMMENTS_CHART_DAILY')),
			JHtml::_('select.option', 1, JText::_('COM_RSCOMMENTS_CHART_MONTHLY')),
			JHtml::_('select.option', 2, JText::_('COM_RSCOMMENTS_CHART_YEARLY'))
		);
	}
	
	public function getStats() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= JFactory::getConfig();
		$timezone	= new DateTimeZone($config->get('offset'));
		$offset		= $timezone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
		$type		= JFactory::getApplication()->input->getInt('type',0);
		$return		= array();
		$stats		= array();
		
		if ($type == 0) {
			$date = JFactory::getDate();
			$date->modify('- 30 days');
			$date = $date->toSql();
			
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rscomments_comments'))
				->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) > '.$db->q($date));
			$db->setQuery($query);
			if ($count = (int) $db->loadResult()) {
				$query->clear()
					->select('COUNT('.$db->qn('id').') AS count')->select('DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)) as thedate')
					->from($db->qn('#__rscomments_comments'))
					->where('DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)')
					->group('DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND))');
				$db->setQuery($query);
				$stats = $db->loadObjectList();
			} else {
				$query->clear()
					->select('COUNT('.$db->qn('id').') AS count')->select('DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)) as thedate')
					->from($db->qn('#__rscomments_comments'))
					->where('DATE_SUB('.$db->qn('date').',INTERVAL 30 DAY) <= DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)')
					->group('DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND))');
				$db->setQuery($query);
				$stats = $db->loadObjectList();
			}
		} else if ($type == 1) {
			$query->select('COUNT('.$db->qn('id').') AS count')->select('DATE_FORMAT(DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)),"%M %Y") as thedate')
				->from($db->qn('#__rscomments_comments'))
				->where('DATE_SUB(CURDATE(),INTERVAL 12 MONTH) <= DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)')
				->group('DATE_FORMAT(DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)),"%M %Y")');
			$db->setQuery($query);
			$stats = $db->loadObjectList();
		} else if ($type == 2) {
			$query->select('COUNT('.$db->qn('id').') AS count')->select('DATE_FORMAT(DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)),"%Y") as thedate')
				->from($db->qn('#__rscomments_comments'))
				->where('DATE_SUB(CURDATE(),INTERVAL 12 YEAR) <= DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)')
				->group('DATE_FORMAT(DATE(DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND)),"%Y")');
			$db->setQuery($query);
			$stats = $db->loadObjectList();
		}
		
		if ($stats) {
			$return[] = array(JText::_('COM_RSCOMMENTS_CHART_DATE'), JText::_('COM_RSCOMMENTS_CHART_COMMENTS'));
			
			foreach ($stats as $stat) {
				$return[] = array($stat->thedate, (int) $stat->count);
			}
		}
		
		return $return;
	}
}