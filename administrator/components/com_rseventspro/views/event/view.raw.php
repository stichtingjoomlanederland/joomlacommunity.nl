<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewEvent extends JViewLegacy
{
	public function display($tpl = null) {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
		
		$input	= JFactory::getApplication()->input;
		$tpl	= $input->getCmd('tpl');
		$id		= $input->getInt('id',0);
		
		$this->eventClass = RSEvent::getInstance($id);
		
		if ($tpl == 'tickets') {
			$tid = rseventsproController::savedata();
			$this->tickets = $this->eventClass->getTickets($tid);
			
			$response = new stdClass();
			$response->id = $tid;
			$response->html = $this->loadTemplate('tickets');
			
			echo json_encode($response);
			die();
		} elseif ($tpl == 'coupons') {
			$cid = rseventsproController::savedata();
			$this->coupons = $this->eventClass->getCoupons($cid);
			
			$response = new stdClass();
			$response->id = $cid;
			$response->html = $this->loadTemplate('coupons');
			
			echo json_encode($response);
			die();
		}
		
		parent::display($tpl);
	}
}