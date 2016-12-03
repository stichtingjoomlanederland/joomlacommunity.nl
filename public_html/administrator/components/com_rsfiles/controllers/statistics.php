<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

class rsfilesControllerStatistics extends JControllerLegacy
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
		$model	= $this->getModel('Statistics');
		$input	= JFactory::getApplication()->input;
		$pks	= $input->get('cid', array(), 'array');
		$return	= base64_decode($input->getString('return'));
		
		$model->delete($pks);
		$this->setRedirect($return, JText::_('COM_RSFILES_STATISTICS_REMOVED')); 
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	public function cancel() {
		$this->setRedirect('index.php?option=com_rsfiles&view=statistics');
	}
}