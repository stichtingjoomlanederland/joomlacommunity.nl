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

class EasyDiscussViewDownload extends EasyDiscussView
{
	/**
	 * Renders the download account data page
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Unauthorized users should not be allowed to access this page.
		ED::requireLogin();

		if (!$this->config->get('main_userdownload')) {
			throw ED::exception('COM_ED_GDPR_DOWNLOAD_DISABLED', ED_MSG_ERROR);
		}

		if (!$this->my->id) {
			throw ED::exception('COM_ED_GDPR_DOWNLOAD_INVALID_ID', ED_MSG_ERROR);
		}

		$download = ED::table('Download');
		$exists = $download->load(array('userid' => $this->my->id));

		if (!$exists || !$download->isReady()) {
			throw ED::exception('COM_ED_GDPR_DOWNLOAD_INVALID_ID', ED_MSG_ERROR);
		}

		return $download->showArchiveDownload();
	}
}
