<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

class JFormFieldModal_Posts extends JFormField
{
	protected $type = 'Modal_Posts';

	protected function getInput()
	{
		$mainframe	= JFactory::getApplication();
		$doc		= JFactory::getDocument();
		$db			= DiscussHelper::getDBO();

		$options	= array();
		$attr		= '';
		$postsList	= array();

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		$query	= 'SELECT `id`, `title` FROM `#__discuss_posts`';
		$query	.= ' WHERE `published` = ' . $db->Quote('1');
		$query	.= ' AND `parent_id` = ' . $db->Quote('0');
		$query	.= ' ORDER BY `id` DESC';

		$db->setQuery($query);
		$data = $db->loadObjectList();

		if(count($data) > 0)
		{
			$optgroup = JHTML::_('select.optgroup','Select post','id','title');
			array_push($postsList,$optgroup);

			foreach ($data as $row) {
				$opt		= new stdClass();
				$opt->id	= $row->id;
				$opt->title	= '(' . $row->id . ') ' . $row->title;;

				array_push($postsList,$opt);
			}
		}

		$html = JHTML::_('select.genericlist',  $postsList, $this->name, trim($attr), 'id', 'title', $this->value );
		return $html;
	}
}
