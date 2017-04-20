<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsControllerGroup extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since	1.6
	 */
	public function __construct() {
		parent::__construct();
	}
	
	protected function postSaveHook(&$model, $validData) {
		$task = $this->getTask();
		
		if ($task == 'apply') {
			$this->setRedirect(JRoute::_('index.php?option=com_rscomments&view=group&layout=edit&IdGroup='.$model->getState('group.id'), false));
		}
	}
	
	public function allowAdd($data = array()) {
		return RSCommentsHelper::canAdd();
	}
}