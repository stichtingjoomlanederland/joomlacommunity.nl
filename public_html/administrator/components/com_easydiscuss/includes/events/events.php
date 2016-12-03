<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussEvents extends EasyDiscuss
{
	public static function importPlugin($group = 'easydiscuss')
	{
		JPluginHelper::importPlugin($group);
	}

	/***
	// list of Joomla 1.5 content triggers
	'content_before_save'		=> 'onBeforeContentSave',
	'content_after_save'		=> 'onAfterContentSave',
	'content_before_delete'		=> '',
	'content_after_delete'		=> '',
	'content_before_display'	=> 'onBeforeDisplayContent',
	'content_after_display'		=> 'onAfterDisplayContent',
	'content_after_title'		=> 'onAfterDisplayTitle',
	'content_prepare'			=> '',
	'content_prepare_data'		=> '',
	'content_prepare_form'		=> ''

	// list of Joomla 1.6 content triggers
	'content_before_save'		=> 'onContentBeforeSave',
	'content_after_save'		=> 'onContentAfterSave',
	'content_before_delete'		=> 'onContentBeforeDelete',
	'content_after_delete'		=> 'onContentAfterDelete',
	'content_before_display'	=> 'onContentBeforeDisplay',
	'content_after_display'		=> 'onContentAfterDisplay',
	'content_after_title'		=> 'onContentAfterTitle',
	'content_prepare'			=> 'onContentPrepare',
	'content_prepare_data'		=> 'onContentPrepareData',
	'content_prepare_form'		=> 'onContentPrepareForm'
	**/

	public static function onContentBeforeSave($context = 'post', &$data = '', $isNew)
	{
		$dispatcher	= JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		self::beforeTrigger( $data );

		$result = $dispatcher->trigger('onContentBeforeSave', array($context, &$data, $isNew));

		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentAfterSave($context = 'post', &$data = '', $isNew)
	{
		$dispatcher = JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		self::beforeTrigger( $data );
		$result = $dispatcher->trigger('onContentAfterSave', array($context, &$data, $isNew));
		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentBeforeDelete($context = 'post', &$data = '')
	{
		$dispatcher	= JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		self::beforeTrigger( $data );
		$result = $dispatcher->trigger('onContentBeforeDelete', array($context, &$data));
		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentAfterDelete($context = 'post', &$data = '')
	{
		$dispatcher			= JDispatcher::getInstance();
		$context			= 'com_easydiscuss.'.$context;

		self::beforeTrigger( $data );
		$result = $dispatcher->trigger('onContentAfterDelete', array($context, &$data));
		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentBeforeDisplay($context = 'post', &$data = '', &$params = array(), $limitstart = 0)
	{
		$dispatcher	= JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		if (empty($params)) {
			$params	= DiscussHelper::getRegistry( '' );
		}

		self::beforeTrigger( $data );
		$result = $dispatcher->trigger('onContentBeforeDisplay', array($context, &$data, &$params, $limitstart));
		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentAfterDisplay($context = 'post', &$data = '', &$params = array(), $limitstart = 0)
	{
		$dispatcher	= JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		if (empty($params)) {
			$params	= DiscussHelper::getRegistry( '' );
		}

		self::beforeTrigger( $data );
		$result = $dispatcher->trigger('onContentAfterDisplay', array($context, &$data, &$params, $limitstart));
		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentAfterTitle($context, &$data, &$params = array(), $limitstart = 0)
	{
		$dispatcher	= JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		if (empty($params)) {
			$params	= DiscussHelper::getRegistry( '' );
		}

		self::beforeTrigger( $data );
		$result = $dispatcher->trigger('onContentAfterTitle', array($context, &$data, &$params, $limitstart));
		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentPrepare($context = 'post', &$data = '', &$params = array(), $limitstart = 0)
	{
		$dispatcher	= JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		if (empty($params)) {
			$params	= DiscussHelper::getRegistry( '' );
		}

		self::beforeTrigger( $data );
		$result = $dispatcher->trigger('onContentPrepare', array($context, &$data, &$params, $limitstart));
		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentPrepareData($context = 'post', &$data = '')
	{
		$dispatcher	= JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		self::beforeTrigger( $data );
		$result = $dispatcher->trigger('onContentPrepareData', array($context, &$data));
		self::afterTrigger( $data );

		return $result;
	}

	public static function onContentPrepareForm($form, &$data)
	{
		$dispatcher	= JDispatcher::getInstance();
		$context = 'com_easydiscuss.'.$context;

		self::beforeTrigger($data);
		$result = $dispatcher->trigger('onContentPrepareForm', array($form, &$data));
		self::afterTrigger($data);

		return $result;
	}

	private static function beforeTrigger(&$arg)
	{
		$data = $arg;

		// If give array, convert to object
		if (is_array($data)) {
			$tmp = new stdClass;
			foreach ($data as $k => $v) {
				$tmp->$k = $v;
			}
			$data = $tmp;
			$data->from	= 'array';
		}

		if (empty($data->content)) {
			return;
		}

		$content = '';
		$data->_contentName = 'content';

		if (isset($data->dc_reply_content)) {
			$data->content = $data->dc_reply_content;
			$data->_contentName = 'dc_reply_content';
		}

		$data->introtext	= '';
		$data->text			= $data->content;
		$data->created_by	= $data->user_id;
		$data->easydiscuss	= true;

		$arg = $data;

		return;
	}

	private static function afterTrigger(&$data)
	{
		if (empty($data->text)) {
			return;
		}

		// Replace the modified contents
		$data->{$data->_contentName} = $data->text;

		unset($data->introtext);
		unset($data->text);
		unset($data->created_by);
		unset($data->easydiscuss);

		if (isset($data->from) && $data->from == 'array') {
			unset($data->from);
			$data = (array) $data;
		}

		return;
	}
}
