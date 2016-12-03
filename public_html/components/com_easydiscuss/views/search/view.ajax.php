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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewSearch extends EasyDiscussView
{
	public function filter()
	{
		$type = $this->input->get('type', '', 'default');

		if (!$type || ($type != 'tag' && $type != 'category')) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_SEARCH_INVALID_FILTER_TYPE'));
		}

		$query = $this->input->get('query', '', 'default');
		$exclude = $this->input->get('exclude', '', 'int');

		$items = ($type == 'tag') ? $this->listTags($query, $exclude) : $this->listCategories($query, $exclude);
		$this->ajax->resolve($items);
	}


	private function listTags($search, $exclude)
	{
		$model = ED::model('Tags');
		$result = $model->suggestTags($search);
		return $result;
	}

	private function listCategories($search, $exclude)
	{
		$model = ED::model('Categories');
		$result = $model->suggestCategories($search);
		return $result;
	}
}
