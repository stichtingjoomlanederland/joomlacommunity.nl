<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerMenus extends RsformController
{
	public function cancelForm()
	{
		$app 	= JFactory::getApplication();
		$formId = $app->input->getInt('formId');

		$app->redirect('index.php?option=com_rsform&view=forms&layout=edit&formId='.$formId);
	}

	public function cancel()
	{
		JFactory::getApplication()->redirect('index.php?option=com_rsform&view=forms');
	}
}