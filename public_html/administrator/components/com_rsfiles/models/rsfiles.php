<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesModelRsfiles extends JModelLegacy
{
	public function __construct($config = array())  {
		parent::__construct($config);
	}
	
	public function getStats() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= JFactory::getConfig();
		$timezone	= new DateTimeZone($config->get('offset'));
		$offset		= $timezone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
		$input		= JFactory::getApplication()->input;
		$return		= array();
		$stats		= array();
		
		$defaultFrom = JFactory::getDate()->modify('-7 days')->toSql();
		$defaultTo	 = JFactory::getDate()->setTime(23,59,59)->toSql();
		
		$from		= $input->getString('from', $defaultFrom);
		$to			= $input->getString('to', $defaultTo);
		$from		= JFactory::getDate($from)->setTime(0,0,0)->toSql();
		$to			= JFactory::getDate($to)->setTime(23,59,59)->toSql();
		
		$query->clear()
			->select('COUNT('.$db->qn('IdStatistic').') AS count')->select('DATE(DATE_ADD('.$db->qn('Date').', INTERVAL '.$offset.' SECOND)) as thedate')
			->from($db->qn('#__rsfiles_statistics'))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) > '.$db->q($from))
			->where('DATE_ADD('.$db->qn('date').', INTERVAL '.$offset.' SECOND) < '.$db->q($to))
			->group('DATE(thedate)');
		$db->setQuery($query);
		$stats = $db->loadObjectList();
		
		if ($stats) {
			$return[] = array(JText::_('COM_RSFILES_CHART_DATE'), JText::_('COM_RSFILES_CHART_DOWNLOADS'));
			
			foreach ($stats as $stat) {
				$date = JFactory::getDate($stat->thedate)->format('d M Y');
				$return[] = array($date, (int) $stat->count);
			}
		}
		
		return $return;
	}
	
	public function getHits() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$return	= array();
		$limit	= JFactory::getApplication()->input->getInt('limit',10);
		
		$query->select($db->qn('FilePath'))
			->select($db->qn('hits'))
			->from($db->qn('#__rsfiles_files'))
			->where($db->qn('briefcase').' = 0')
			->where($db->qn('published').' = 1')
			->where($db->qn('hash').' <> '.$db->q(''))
			->where($db->qn('hits').' <> 0')
			->order($db->qn('hits').' DESC');
		$db->setQuery($query,0,$limit);
		if ($files = $db->loadObjectList()) {
			$return[] = array(JText::_('File'), JText::_('Hits'));
			
			foreach ($files as $file) {
				$return[] = array($file->FilePath, (int) $file->hits);
			}
		}
		
		return $return;
	}
}