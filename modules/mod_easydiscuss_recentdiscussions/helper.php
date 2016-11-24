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

class modRecentDiscussionsHelper
{
	public static function getData( $params )
	{
		$count			= (int) $params->get( 'count', 10 );
		$filter			= (int) $params->get( 'filter_option', 0 );
		$includeSubcat	= (bool) $params->get( 'include_subcategories', 0 );

		$catId			= intval($params->get( 'category', 0));
		$tagId			= intval($params->get( 'tags', 0));



		$options = array();


		// default sorting will be by latest
		$options['sort'] = 'latest';
        $options['includeChilds'] = false;
        $options['limit'] = $count;
        $options['respectSearch'] = false;

        // Let the model knows this comes from module
        $options['module'] = true;

		switch($filter)
		{
			case '4':
				// unanswered post
				$options['filter'] = 'unanswered';
				break;

            case '3':
                // featured posts
                $options['featured'] = true;

                break;

            case '2':
                // by tag id
                if ($tagId) {
                    $options['tag'] = $tagId;
                }
                break;

			case '1':
                // by category id
				if ($catId) {
					$options['category'] = $catId;
                    if ($includeSubcat) {
                        $options['includeChilds'] = true;
                    }
				}
                break;
			case '0':
			default:
				break;
		}


        $model = ED::model('Posts');
        $results = $model->getDiscussions($options);

		if (!$results) {
			return false;
		}

        // preload posts
        ED::post($results);

        // preload users
        $ids = array();
		foreach ($results as $row) {
			$ids[] = $row->user_id;
		}

        ED::user($ids);

        $posts = ED::modules()->format($results);

		return $posts;
	}
}
