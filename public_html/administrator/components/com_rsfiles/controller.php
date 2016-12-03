<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesController extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();
		
		$lang = JFactory::getLanguage();
		
		$lang->load('com_rsfiles', JPATH_ADMINISTRATOR, 'en-GB', true);
		$lang->load('com_rsfiles', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
		$lang->load('com_rsfiles', JPATH_ADMINISTRATOR, null, true);
		
		// Set the table directory
		JTable::addIncludePath(JPATH_COMPONENT.'/tables');
	}
	
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false) {
		// Add the submenu
		rsfilesHelper::subMenu();
		
		parent::display();
		return $this;
	}
	
	/**
	 *	Method to display the RSFiles! Dashboard
	 */
	public function rsfiles() {
		return $this->setRedirect('index.php?option=com_rsfiles');
	}
	
	/**
	 *	Method to display the preview
	 */
	public function preview() {
		$id = JFactory::getApplication()->input->getInt('id',0);
		return rsfilesHelper::preview($id);
	}
	
	public function stats() {
		$model = $this->getModel('rsfiles');
		echo json_encode($model->getStats());
		
		JFactory::getApplication()->close();
	}
	
	public function hits() {
		$model = $this->getModel('rsfiles');
		echo json_encode($model->getHits());
		
		JFactory::getApplication()->close();
	}
}