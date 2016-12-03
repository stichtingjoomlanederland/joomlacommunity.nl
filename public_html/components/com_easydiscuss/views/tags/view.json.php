<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewTags extends EasyDiscussView
{
	public function display($tmpl = null)
	{
		$model = ED::model("Tags");
		$data = $model->getTagCloud('', '', '');
		$tags = array();

		if ($data) {

			foreach ($data as $row) {
				$tag = ED::table('Tags');
				$tag->bind($row);

				$tmp = new stdClass();
				$tmp->id = $tag->id;
				$tmp->permalink = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=tags&layout=listings&id=' . $tag->id . '&tmpl=component&format=json', false, true);
				$tmp->title = $tag->title;
				$tmp->alias = $tag->alias;
				$tmp->created = $tag->created;
				$tmp->posts = $row->post_count;

				$tags[] = $tmp;
			}
		}

		$this->set('tags', $tags);

		parent::display();
	}
}
