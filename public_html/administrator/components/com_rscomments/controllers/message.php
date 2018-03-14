<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsControllerMessage extends JControllerForm
{
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'tag') {
		$tag = JFactory::getApplication()->input->getString('tag');
		$append = parent::getRedirectToItemAppend($tag,$urlVar);
		return $append;
	}
}