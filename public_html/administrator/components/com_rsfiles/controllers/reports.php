<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

class rsfilesControllerReports extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * remove record(s)
	 * @return void
	 */
	public function delete() {
		$model	= $this->getModel('reports');
		$cid	= JFactory::getApplication()->input->get('cid',array(),'array');
		$id		= JFactory::getApplication()->input->getString('id','');
		
		$model->delete($cid);
		$this->setRedirect('index.php?option=com_rsfiles&view=reports&id='.$id); 
	}
}