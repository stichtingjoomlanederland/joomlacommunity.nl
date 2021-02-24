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

class EasyDiscussActivityPostEmailParser extends EasyDiscussActivityAbstract
{
	/**
	 * method the translate the activity log to a readable content
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function translate($data)
	{
		$timeLapsed = ED::date()->toLapsed($data->created);

		$text = JText::sprintf('COM_ED_ACTIVITY_POST_FROM_EMAIL_PARSER', $timeLapsed);
		return $text;
	}
}