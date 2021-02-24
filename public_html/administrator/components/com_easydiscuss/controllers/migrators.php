<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.application.component.controller');

class EasyDiscussControllerMigrators extends EasyDiscussController
{
	public function purge()
	{
		ED::checkToken();

		$layout = $this->input->get('layout', '', 'cmd');

		$db = ED::db();
		$sql = $db->sql();

		$mapping = array('kunena' => 'com_kunena',
						'jomsocial' => 'com_community',
						'vbulletin' => 'vbulletin'
					);

		$component = '';

		if ($layout) {
			//let map the layout with component.
			if (isset($mapping[$layout]) && $mapping[$layout]) {
				$component = $mapping[$layout];
			}
		}

		if ($component) {
			// delete only associated records from the component.
			$query = 'delete from ' . $db->nameQuote('#__discuss_migrators') . ' where ' . $db->nameQuote('component') . ' = ' . $db->Quote($component);

		} else {
			// truncate all
			$query = 'TRUNCATE TABLE ' . $db->nameQuote('#__discuss_migrators');
		}

		$db->setQuery($query);
		$db->Query();

		$link = 'index.php?option=com_easydiscuss&view=migrators';

		if ($layout) {
			$link .= '&layout=' . $layout;
		}

		if ($db->getError()) {
        	ED::setMessage('COM_EASYDISCUSS_PURGE_ERROR', ED_MSG_ERROR);
			return ED::redirect($link);
		}

        ED::setMessage('COM_EASYDISCUSS_PURGE_SUCCESS', 'success');
        return ED::redirect($link);
	}
}
