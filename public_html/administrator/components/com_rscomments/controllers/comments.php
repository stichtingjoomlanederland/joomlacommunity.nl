<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsControllerComments extends JControllerAdmin
{
	protected $text_prefix = 'COM_RSCOMMENTS';
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rseventsproControllerSubscriptions
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Comment', $prefix = 'RscommentsModel', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, $config);
	}
	
	/**
	 *	Clear votes
	 */
	public function votes() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the selected items
		$pks = JFactory::getApplication()->input->get('cid', array(0), 'array');
		
		$model = $this->getModel();
		
		// Force array elements to be integers
		JArrayHelper::toInteger($pks);
		
		if (!$model->votes($pks)) {
			$this->setMessage($model->getError());
		} else {
			$this->setMessage(JText::_('COM_RSCOMMENTS_VOTES_CLEARED'));
		}
		
		$this->setRedirect('index.php?option=com_rscomments&view=comments');
	}
}