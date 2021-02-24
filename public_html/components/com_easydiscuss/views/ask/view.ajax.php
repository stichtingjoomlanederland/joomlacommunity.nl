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

use Joomla\CMS\HTML\HTMLHelper;
require_once ED_ROOT . '/views/views.php';

class EasyDiscussViewAsk extends EasyDiscussView
{
	/**
	 * Retrieves the list of child categories given the parent category
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getCategory()
	{
		$id = $this->input->get('id', 0, 'int');
		$model = ED::model('categories');
		$items = $model->getChildCategories($id, true, true);

		if (!$items) {
			return $this->ajax->resolve(array());
		}

		$categories = array();

		for ($i = 0; $i < count($items); $i++) {
			
			$item = $items[$i];

			$category = ED::table('Category');
			$category->load($item->id);

			$item->hasChild = $category->getChildCount();
		}

		$this->ajax->resolve($items);
	}

	/**
	 * Renders a preview of the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function preview()
	{
		$content = $this->input->get('content', '', 'raw');

		if (empty($content)) {
			$this->ajax->resolve(JText::_('COM_ED_NOTHING_TO_PREVIEW'));
		}

		$data['content'] = $content;

		// @task: We'll need to find a better way to do this instead of binding it to post object.
		$post = ED::post();
		$post->bind($data);

		$content = ED::formatContent($post);

		// Check if the formatted contents contains any scripts from gist.
		// Console throw warning - It isn't possible to preview asynchronously-loaded external script.
		preg_match_all('/(\<script.*src=\"(https?:\/\/gist.github.com.*)\".*\<\/script>)/Ui', $content, $scripts);

		$notice = '';

		if (count($scripts[0])) {

			for ($i = 0; $i < count($scripts[0]); $i++) {
				$script = $scripts[1][$i];
				$link = HTMLHelper::link($scripts[2][$i], $scripts[2][$i], ['target' => '_blank']);

				$content = EDJString::str_ireplace($script, $link, $content);
			}

			$notice = JText::_('COM_ED_PREVIEW_NOTICE');
		}

		if (empty($content)) {
			$this->ajax->resolve(JText::_('COM_ED_NOTHING_TO_PREVIEW'));
		}

		$this->ajax->resolve($content, $notice);
	}
}

