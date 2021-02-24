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

ED::import('admin:/tables/table');

class DiscussPoints extends EasyDiscussTable
{
	public $id = null;
	public $rule_id	= null;
	public $title = null;
	public $created	= null;
	public $published = null;
	public $rule_limit = null;

	public function __construct(& $db)
	{
		parent::__construct('#__discuss_points', 'id', $db);
	}

	public function delete($pk = null)
	{
		// retrieve the points title here
		$pointRuleTitle = $this->title;

		$state = parent::delete($pk);

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_POINTRULES_DELETED', 'points', array(
			'pointRuleTitle' => JText::_($pointRuleTitle)
		));

		return $state;
	}

	/**
	 * Method to publish for the points
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function publish($items = array(), $state = 1, $userId = 0)
	{
		$this->published = 1;

		$state = parent::store();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_POINTRULES_PUBLISH', 'points', array(
			'link' => 'index.php?option=com_easydiscuss&view=points&layout=form&id=' . $this->id,
			'pointRuleTitle' => $this->title
		));

		return $state;	
	}

	/**
	 * Method to unpublish for the points
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function unpublish($items = array())
	{
		$this->published = 0;

		$state = parent::store();

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_POINTRULES_UNPUBLISH', 'points', array(
			'link' => 'index.php?option=com_easydiscuss&view=points&layout=form&id=' . $this->id,
			'pointRuleTitle' => $this->title
		));

		return $state;
	}
}
