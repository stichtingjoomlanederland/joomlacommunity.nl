<?php
/**
 * @package     mod_easydiscuss_tag_cloud
 * @copyright   Copyright (C) 2016 Stack Ideas Private Limited. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class modEDTagCloud
{
	public static function getTagCloud($params)
	{
		$order = $params->get('order', 'postcount');
		$sort = $params->get('sort', 'desc');
		$count = (int) trim($params->get('count', 0));
		$shuffeTags	= $params->get('shuffleTags', TRUE);
		$pMinSize = $params->get('minsize', '10');
		$pMaxSize = $params->get('maxsize', '30');

		$model = ED::model('Tags');
		$tagCloud = $model->getTagCloud($count, $order, $sort);
		$extraInfo = array();

		if ($shuffeTags) {
			shuffle($tagCloud);
		}

		foreach ($tagCloud as $item) {
			$extraInfo[] = $item->post_count;
		}

		$min_size = $pMinSize;
		$max_size = $pMaxSize;

		$minimum_count = 0;
		$maximum_count = 0;

		if (!empty($extraInfo)) {
			$minimum_count = min($extraInfo);
			$maximum_count = max($extraInfo);
		}

		$spread = $maximum_count - $minimum_count;

		if ($spread == 0) {
			$spread = 1;
		}

		$cloud_tags = array();

		foreach ($tagCloud as $tag) {
			$tag->fontsize = $min_size + ($tag->post_count - $minimum_count) * ($max_size - $min_size) / $spread;
			$cloud_tags[] = $tag;
		}

		return $cloud_tags;
	}
}