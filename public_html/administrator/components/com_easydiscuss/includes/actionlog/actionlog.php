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

use Joomla\CMS\Component\ComponentHelper;

class EasyDiscussActionLog
{
	private $defaultData = array(
		'action' => '',
		'title' => 'com_easydiscuss',
		'extension_name' => 'com_easydiscuss'
	);

	/**
	 * Determines if actionlog feature is enabled or not from the 'Events To Log' option
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isEnabled()
	{
		$params = ComponentHelper::getComponent('com_actionlogs')->getParams();

		$extensions = $params->get('loggable_extensions', array());

		if (in_array('com_easydiscuss', $extensions)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if actionlog feature already exist in current Joomla version.
	 * Because this actionlog feature only available in Joomla 3.9
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function exists()
	{
		static $loaded = null;

		$file = JPATH_ADMINISTRATOR . '/components/com_actionlogs/models/actionlog.php';

		if (ED::isJoomla4()) {
			$file = JPATH_ADMINISTRATOR . '/components/com_actionlogs/src/Model/ActionlogModel.php';
		}

		if (is_null($loaded)) {
			jimport('joomla.filesystem.file');

			$exists = JFile::exists($file);
			$loaded = $exists;
		}

		return $loaded;
	}

	/**
	 * Store the user action into the log table
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function log($actionString, $context, $data = array())
	{
		// Skip this if the actionlog feature not exist in current Joomla version
		if (!$this->exists()) {
			return;
		}

		if (!$this->isEnabled()) {
			return;
		}

		// load backend language if some of the action log from frontend
		if (!ED::isFromAdmin()) {
			JFactory::getLanguage()->load('com_easydiscuss', JPATH_ADMINISTRATOR);
		}

		$my = JFactory::getUser();

		$user = isset($data['user']) && is_object($user) ? $user : $my;
		
		$data = array_merge($data, $this->defaultData);
		
		$data['userid'] = $user->id;
		$data['username'] = $user->username;
		$data['accountlink'] = "index.php?option=com_users&task=user.edit&id=" . $user->id;
		
		$context = $data['extension_name'] . '.' . $context;

		$model = $this->getModel();

		// Could be disabled
		if ($model === false) {
			return false;
		}

		$model->addLog(array($data), JText::_($actionString), $context, $user->id);
	}

	/**
	 * Retrieve joomla's ActionLog model
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getModel()
	{
		$config = array('ignore_request' => true);

		if (ED::isJoomla4()) {
			$model = new Joomla\Component\Actionlogs\Administrator\Model\ActionlogModel($config);

			return $model;
		}

		\Joomla\CMS\MVC\Model\ItemModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_actionlogs/models', 'ActionlogsModelActionlog');
		$model = \Joomla\CMS\MVC\Model\ItemModel::getInstance('Actionlog', 'ActionLogsModel', $config);

		return $model;
	}

	/**
	 * Normalize the action log language constants for question and reply 
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function normalizeActionLogConstants($isReply, $langConstant)
	{
		if ($isReply) {
			$langConstant = $langConstant . '_REPLY';
			return $langConstant;
		}

		$langConstant = $langConstant . '_QUESTION';

		return $langConstant;
	}

	/**
	 * Normalize the action log post permalink
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function normalizeActionLogPostPermalink(EasyDiscussPost $post)
	{
		$url = 'index.php?option=com_easydiscuss&view=posts&layout=redirectPost&id=' . $post->id;

		return $url;
	}

	/**
	 * Normalize the action log user permalink from backend
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function normalizeActionLogUserPermalink($userId)
	{
		$url = 'index.php?option=com_easydiscuss&view=users&layout=form&id=' . $userId;

		return $url;
	}

	/**
	 * Normalize the action log post permalink
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function normalizeActionLogPostTitle(EasyDiscussPost $post)
	{
		$actionLogPostTitle = $post->getTitle();

		if ($post->isReply()) {

			$parent = $post->getParent();
			$actionLogPostTitle = $parent->title;
		}

		return $actionLogPostTitle;
	}	
}
