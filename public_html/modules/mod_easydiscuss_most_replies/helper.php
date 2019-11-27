<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class modEasydiscussMostRepliesHelper
{
	public static function getData($params)
	{
		$db = DiscussHelper::getDBO();
		$count = (int) trim($params->get('count', 0));

		if( !class_exists('EasyDiscussModelPosts')) {
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'posts' , DISCUSS_MODELS );
		}

		$model = ED::model('Posts');
		$model->isModule = true;
		$posts = $model->getData(false, 'popular', null, '', '', $count);

		if ($posts) {
			$posts = ED::modules()->format($posts);
		}

		return $posts;
	}
}
