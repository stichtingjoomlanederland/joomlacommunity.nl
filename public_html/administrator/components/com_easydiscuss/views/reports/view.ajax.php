<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussViewReports extends EasyDiscussAdminView
{
	/**
	 * Previews an reports
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function preview()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject();
		}

		$url = JURI::root() . 'administrator/index.php?option=com_easydiscuss&view=reports&layout=preview&browse=1&tmpl=component&id=' . $id;

		$theme = ED::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/reports/dialogs/reasons');

		return $this->ajax->resolve($output);
	}

	/**
	 * Delete posts confirmation
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function deleteConfirm()
	{
		$theme = ED::themes();
		$contents = $theme->output('admin/reports/dialogs/delete.post');

		return $this->ajax->resolve($contents);
	}
}
