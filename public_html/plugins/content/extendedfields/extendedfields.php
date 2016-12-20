<?php
/*
 * @package		Funx
 * @copyright	Copyright (c) 2015 Perfect Web Team / perfectwebteam.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

class PlgContentExtendedfields extends JPlugin
{
	/**
	 * Event method that runs on content preparation
	 *
	 * @param   JForm   $form The form object
	 * @param   integer $data The form data
	 *
	 * @return bool
	 */
	public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		$name = $form->getName();

		if (!in_array($name, array('com_content.article')))
		{
			return true;
		}

		if (empty($data))
		{
			$input = JFactory::getApplication()->input;
			$data  = (object) $input->post->get('jform', array(), 'array');
		}

		if (is_array($data))
		{
			jimport('joomla.utilities.arrayhelper');
			$data = JArrayHelper::toObject($data);
		}

		JForm::addFormPath(__DIR__ . '/forms');

		if ($name == 'com_content.article')
		{
			$form->loadFile('article', false);
		}

		if (!empty($data->id))
		{
			$data = $this->loadData($data);
		}

		return true;
	}

	/**
	 * Event method that is run after an item is saved
	 *
	 * @param   string  $context The context of the content
	 * @param   object  $item    A JTableContent object
	 * @param   boolean $isNew   If the content is just about to be created
	 *
	 * @return    boolean  Return value
	 */
	public function onContentAfterSave($context, $item, $isNew)
	{
		if ($context != 'com_content.article')
		{
			return true;
		}

		$jinput = JFactory::getApplication()->input;
		$form   = $jinput->post->get('jform', null, 'array');

		$content_id = $item->id;
		$this->saveTest($content_id, $context, $form);

		return true;
	}

	/**
	 * Event method run before content is displayed
	 *
	 * @param   string $context The context for the content passed to the plugin.
	 * @param   object &$item   The content to be displayed
	 * @param   mixed  &$params The item params
	 * @param   int    $page    Current page
	 *
	 * @return    null
	 */
	public function onContentBeforeDisplay($context, &$item, &$params, $page = 0)
	{
		if (!empty($item->id))
		{
			$item = $this->loadData($item);
		}
	}

	/**
	 * Task method to save the test value to the database
	 *
	 * @param   int    $content_id Content ID in the #__extendedfields table
	 * @param   string $context    The context for the content passed to the plugin.
	 * @param   mixed  $test       Test value
	 *
	 * @return    bool
	 */
	protected function saveTest($content_id, $context, $form)
	{
	}

	/**
	 * Task method to load the test value from the database
	 *
	 * @param   object $data The content that is being loaded
	 *
	 * @return mixed
	 */
	protected function loadData($data)
	{
		return $data;
	}
}
