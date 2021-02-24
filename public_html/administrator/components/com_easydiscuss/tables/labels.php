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

class DiscussLabels extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $published = null;
	public $colour = null;
	public $created = null;
	
	public function __construct(&$db)
	{
		parent::__construct('#__discuss_post_labels', 'id', $db);
	}

	public function delete($pk = null)
	{
		// retrieve the post label  here
		$postLabelTitle = $this->title;

		$state = parent::delete($pk);

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_POSTLABEL_DELETED', 'postLabel', array(
			'postLabelTitle' => JText::_($postLabelTitle)
		));

		return $state;
	}	
}