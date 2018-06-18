<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
	/**
	 * Filters tags on a given filter
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function filter()
	{
		$sort = $this->input->get('sort', 'title', 'default');
		$order = $sort == 'postcount' ? 'desc' : 'asc';

		$view = $this->input->get('view', 'tags', 'cmd');
		$limit = DISCUSS_TAGS_LIMIT;

		$model = ED::model("Tags");
		$tags = $model->getTagCloud($limit, $sort, $order, '', true);
		$pagination = $model->getPagination();

		$filtering = array('sort' => $sort);

		$pagination = $pagination->getPagesLinks($view, $filtering, true);

		if (!$tags) {
			return $this->ajax->resolve('', $pagination);
		}

		$theme = ED::themes();
		$contents = '';

		foreach ($tags as $tag) {
			$theme->set('tag', $tag);
			$contents .= $theme->output('site/tags/default.item');
		}

		$this->ajax->resolve($contents, $pagination);
		return $this->ajax->send();
	}
}
