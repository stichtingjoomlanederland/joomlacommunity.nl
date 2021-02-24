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

class EasyDiscussCronHookImageOptimizer extends EasyDiscuss
{
	/**
	 * Optimize images in attachments
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function execute()
	{
		$optimizer = ED::imageoptimizer();

		if (!$optimizer->enabled() || !$this->config->get('optimize_cron')) {
			return false;
		}

		// Get a list of images that are not processed by yet
		$model = ED::model('Attachments');
		$attachments = $model->getImagesNotOptimized();
		
		if (!$attachments) {
			return;
		}

		foreach ($attachments as $attachment) {
			$attachment->optimize();
		}
	}

}
