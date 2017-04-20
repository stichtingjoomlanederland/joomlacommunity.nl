<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsControllerMessage extends JControllerForm
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

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'tag') {
		$tag = JFactory::getApplication()->input->getString('tag');
		$append = parent::getRedirectToItemAppend($tag,$urlVar);
		return $append;
	}
}