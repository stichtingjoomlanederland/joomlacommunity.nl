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

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

class EasyDiscussRouter extends EasyDiscuss
{
	public function build(&$query)
	{
		$segments = array();

		// Get the current query menu
		$menu = JFactory::getApplication()->getMenu();
		$active = isset($query['Itemid']) ? $menu->getItem($query['Itemid']) : $menu->getActive();


		// Forums view
		if (isset($query['view']) && $query['view'] == 'forums') {

            // if ($active->query['view'] != 'forums') {
            //     $segments[] = $query['view'];
            // }

            $segments[] = $query['view'];
			unset($query['view']);

			// It may contain a category id
			if (isset($query['category_id']) && $query['category_id']) {
				$aliases = EDR::getCategoryAliases($query['category_id']);

				foreach ($aliases as $alias) {
					$segments[]	= $alias;
				}

				unset($query['category_id']);
			}

			if (isset($query['layout'])) {
				$segments[]	= $query['layout'];
				unset($query['layout']);
			}
		}

		// Post view
		if (isset($query['view']) && $query['view'] == 'post') {

			// var_dump($query);exit;

			// We don't want to include the view for the entry links.
			unset($query['view']);

			if (isset($query['id'])) {
				$segments[]	= EDR::getPostAlias($query['id']);
				unset($query['id']);
			}

			if (isset($query['layout'])) {
				$segments[] = $query['layout'];
				unset($query['layout']);
			}
		}

		// Profile view
		if (isset($query['view']) && $query['view'] == 'profile') {

			$segments[] = $query['view'];
			unset($query['view']);

			if (isset($query['layout'])) {
				$segments[]	= $query['layout'];
				unset($query['layout']);
			}

			if (isset($query['id'])) {
				$segments[]	= EDR::getUserAlias($query['id']);
				unset($query['id']);
			}

			if (isset($query['category_id'])) {
				$aliases = EDR::getCategoryAliases($query['category_id']);

				foreach ($aliases as $alias) {
					$segments[]	= $alias;
				}

				unset($query['category_id']);
			}

			if (isset($query['viewtype'])) {

				$segments[] = $query['viewtype'];
				unset($query['viewtype']);
			}
		}

		// Recent topics view
		if (isset($query['view']) && $query['view'] == 'index') {

			$segments[] = $query['view'];
			unset($query['view']);

			if (isset($query['category_id'])) {
				$aliases		= DiscussRouter::getCategoryAliases( $query['category_id'] );

				foreach ($aliases as $alias) {
					$segments[]	= $alias;
				}

				unset( $query['category_id'] );
			}
		}

		// Ask question view
		if (isset($query['view']) && $query['view'] == 'ask') {
			$segments[] = $query['view'];
			unset( $query['view'] );

			// in ED 4.0, we no longer translate category in ask page.

		}

		// Points view
		if (isset($query['view']) && $query['view'] == 'points') {
			$segments[]	= $query['view'];
			unset($query['view']);

			if (isset($query['layout'])) {
				$segments[]	= $query['layout'];
				unset($query['layout']);
			}

			if (isset($query['id'])) {
				$segments[]	= EDR::getUserAlias($query['id']);
				unset($query['id']);
			}

		}

		// Tags view
		if (isset($query['view']) && $query['view'] == 'tags') {
			$segments[] = $query['view'];
			unset($query['view']);

			if (isset($query['id'])) {
				$segments[]	= DiscussRouter::getTagAlias( $query['id'] );
				unset($query['id']);
			}

            if (isset( $query['layout'])) {
                unset( $query['layout'] );
            }
		}

		// Users view
		if (isset($query['view']) && $query['view'] == 'users') {
			$segments[] = $query['view'];
			unset($query['view']);

			if (isset($query['sorting'])) {
				$segments[] = 'latest';
				unset($query['sorting']);
			}
		}

		// Badges view
		if (isset($query['view']) && $query['view'] == 'badges') {
			$segments[]		= $query['view'];
			unset( $query['view'] );

			if(isset($query['id']))
			{
				$segments[]	= DiscussRouter::getAlias( 'badges', $query['id'] );
				unset($query['id']);
				unset( $query['layout'] );
			}

			if( isset( $query['layout'] ) )
			{
				$segments[] = $query['layout'];
				unset( $query['layout'] );
			}
		}

		// search view
		if (isset($query['view']) && $query['view'] == 'search') {

			$segments[]		= $query['view'];
			unset( $query['view'] );

			if (isset($query['query']) && $query['query']) {
				$segments[] = $query['query'];
				unset( $query['query'] );
			}
		}

		// Favourites view
		if (isset($query['view']) && $query['view'] == 'favourites') {
			$segments[] = $query['view'];
			unset($query['view']);
		}

		// Assigned view
		if (isset($query['view']) && $query['view'] == 'assigned') {
			$segments[] = $query['view'];
			unset($query['view']);
		}

		// mypost view
		if (isset($query['view']) && $query['view'] == 'mypost') {
			$segments[] = $query['view'];
			unset($query['view']);
		}

		// Categories view
		if (isset($query['view']) && $query['view'] == 'categories') {
			$segments[]	= $query['view'];
			unset($query['view']);
		}

		// Conversations view
		if (isset($query['view']) && $query['view'] == 'conversation') {
			$segments[] = $query['view'];
			unset( $query['view'] );

			if (isset($query['layout'])) {
				$segments[]	= $query['layout'];
				unset($query['layout']);
			}
		}

		// notification view
		if (isset($query['view']) && $query['view'] == 'notifications') {
			$segments[] = $query['view'];
			unset( $query['view'] );

			if (isset($query['layout'])) {
				$segments[]	= $query['layout'];
				unset($query['layout']);
			}
		}

		// subscriptions view
		if (isset($query['view']) && $query['view'] == 'subscription') {
			$segments[] = $query['view'];
			unset( $query['view'] );

		}


		// dashboard view
		if (isset($query['view']) && $query['view'] == 'dashboard') {
			$segments[] = $query['view'];
			unset( $query['view'] );

			if (isset($query['layout'])) {
				$segments[]	= $query['layout'];
				unset($query['layout']);
			}
		}

		if (isset($query['filter'])) {
			$segments[] = $query['filter'];
			unset( $query['filter'] );
		}

		if (isset($query['sort'])) {
			$segments[]	= $query['sort'];
			unset( $query['sort'] );
		}

		if (!isset($query['Itemid'])) {
			$query['Itemid'] = DiscussRouter::getItemId();
		}

		return $segments;
	}

	public function parse($segments)
	{
		$config = ED::config();
		$vars = array();
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$item = $menu->getActive();


        // var_dump($item);


        // var_dump($segments);exit;

		// We need to get a list of valid views
		$views = array('attachments', 'categories', 'index', 'forums','conversation',
						'post', 'profile', 'search', 'tag', 'tags', 'users', 'notifications',
						'badges', 'ask', 'subscriptions', 'featured', 'favourites', 'assigned',
						'points','dashboard');

		// @rule: For view=post&id=xxx we do not include

		if (isset($segments[0]) && !in_array($segments[0], $views)) {
			$vars['view']	= 'post';

			$count	= count($segments);

			if( $count >= 1 )
			{
				// Submission
				$index = 0;
				if( $segments[ $index ] == 'submit' )
				{
					$vars['layout']	= $segments[ $index ];
					$index += 1;
				}

				if (isset($segments[$index])) {

					$postId = 0;
					$postId = EDR::decodeAlias($segments[$index], 'Post');

					$vars['id']	= $postId;
					$index += 1;
				}

				if( isset( $segments[ $index ] ) )
				{
					if( $segments[ $index ] == 'edit' )
					{
						$vars['layout'] = $segments[ $index ];
					}
					else
					{
						$vars['sort'] = $segments[ $index ];
					}
				}
			}
		}

        /*
            /forums/cat1/cat2/cat3
            /forums/cat1/cat2/cat3/listings
        */
		if (isset($segments[0]) && $segments[0] == 'forums') {
			$vars['view'] = 'forums';

            $count = count($segments);
            if ($count > 1) {
                if (isset($segments[$count-1]) && $segments[$count-1] == 'listings') {

                    // get the last cat
                    $category = $segments[$count-2];
                    $vars['category_id'] = EDR::decodeAlias($category, 'Category');

                    $vars['layout'] = 'listings';
                } else {
                    // get the last cat
                    $category = $segments[$count-1];
                    $vars['category_id'] = EDR::decodeAlias($category, 'Category');
                }
            }
		}

		if (isset($segments[0]) && $segments[0] == 'index') {
			$count	= count($segments);

			if ($count > 1) {
				$vars['view']	= $segments[0];

				$segments = DiscussRouter::encodeSegments( $segments );

				if (in_array( $segments[1] , array('allposts','unanswered', 'unresolved', 'unread', 'resolved'))) {
					$vars['filter'] = $segments[1];

					if (isset($segments[2])) {
						$vars['sort'] = $segments[2];
					}
				}
			}
		}

		if (isset($segments[0]) && $segments[0] == 'points') {

			// $segments = DiscussRouter::encodeSegments($segments);

			// Get the current view
			$vars['view']	= $segments[ 0 ];

			// Get the user's id.
			$alias 	= $segments[1];

			$id = (int) $alias;

			if (! $id) {

				if ($config->get('main_sef_user') == 'realname' || $config->get('main_sef_user') == 'username') {
					$tmp 	= explode('-', $alias);
					$id 	= $tmp[0];
				}

				if (!$id) {
					// Username might contains "-" character
					$alias 	= JString::str_ireplace( ':' , '-' , $alias );
					$id = DiscussHelper::getUserId( $alias );
				}

				if (!$id) {
					// Username might contains "-" character
					$alias 	= JString::str_ireplace( '-' , ' ' , $alias );
					$id = DiscussHelper::getUserId( $alias );
				}

			}

			$vars['id']	= $id;
		}

		if (isset($segments[0]) && $segments[0] == 'categories') {
			$count	= count($segments);

			if ($count > 1) {
				$vars['view'] = $segments[ 0 ];

				$segments = DiscussRouter::encodeSegments( $segments );


				if (isset( $segments[ 1 ] ) && $segments[ 1 ] == 'listings') {
					// Get the last item since the category might be recursive.
					$cid		= $segments[2];
					$catId = EDR::decodeAlias($cid, 'Category');

					$vars['layout']		= $segments[ 1 ];
					$vars['category_id']  = $catId;
				} else {

					// Get the last item since the category might be recursive.
					$cid = $segments[1];

					$catId = EDR::decodeAlias($cid, 'Category');
					$vars['category_id']  = $catId;
				}

				if (isset( $segments[3])) {
					$vars['filter']	= $segments[3];

					if (isset($segments[4])) {
						$vars['sort'] = $segments[4];
					}
				}
			}
		}

		if (isset($segments[0]) && $segments[0] == 'tags') {
			$count	= count($segments);

			if ($count > 1) {
				$segments = DiscussRouter::encodeSegments($segments);

				$vars['id'] = EDR::decodeAlias($segments[ 1 ], 'Tags');

				if ($count > 2) {
					if ($segments[2] == 'allposts' || $segments[2] == 'featured' || $segments[2] == 'unanswered') {
						$vars['filter'] = $segments[2];
					}

					if (! empty($segments[3])) {
						$vars['sort'] =  $segments[3];
					}
				}
			}

			$vars['view'] = $segments[0];
		}

		if (isset($segments[0]) && $segments[0] == 'profile') {
			$count	= count($segments);

			if ($count > 1) {
				$segments	= DiscussRouter::encodeSegments($segments);

				if ($segments[1] == 'edit') {
					$vars['layout'] = 'edit';
				} else {
					$user	= 0;
					// $id		= DiscussHelper::getUserId($segments[1]);
					$id = 0;

					if ($config->get('main_sef_unicode')) {
						// var_dump($segments[1], $id);exit;
						$id = EDR::decodeAlias($segments[1], 'Profile');
					}

					if (! $id) {
						if ($config->get('main_sef_user') == 'realname' || $config->get('main_sef_user') == 'username') {

							$id 	= explode('-', $segments[1]);
							$id 	= $id[0];
						}

						// Username might contains "-" character
						if ($id) {
							$user	= JFactory::getUser($id);
						}

						if (!$user) {

							$segments[1] = JString::str_ireplace( '-' , ' ' , $segments[1] );
							$id	= DiscussHelper::getUserId( $segments[1] );
							$user = JFactory::getUser( $id );
						}

						$id = $user->id;
					}

					$vars['id'] = $id;
				}

				if (isset($segments[2])) {
					$vars['viewtype'] = $segments[2];
				}
			}

			$vars['view'] = $segments[0];
		}

		if (isset($segments[0]) && $segments[0] == 'users') {
			$count	= count($segments);

			if ($count > 1) {
                $vars['sort'] = $segments[ 1 ];
			}

			$vars['view'] = $segments[0];
		}

		if (isset($segments[0]) && $segments[0] == 'badges') {
			$count	= count($segments);

			if ($count > 1) {
				if ($segments[1] == 'mybadges') {
					$vars['layout'] = 'mybadges';
				} else {
					$segments		= DiscussRouter::encodeSegments($segments);

					$vars['id'] = EDR::decodeAlias($segments[1], 'Badges');
					$vars['layout'] = 'listings';
				}
			}
			$vars['view']	= $segments[0];
		}

		if (isset($segments[0]) && $segments[0] == 'ask') {
			$vars['view']	= $segments[0];

			// in ED 4.0, we no longer translate category
		}

		if (isset($segments[0]) && $segments[0] == 'search') {
			$vars['view']	= $segments[0];

			if (isset($segments[1])) {
				$vars['query'] = $segments[1];

			}
		}

		if (isset($segments[0]) && $segments[0] == 'mypost') {
			$vars['view']	= $segments[0];

			if (isset($segments[1])) {
				$vars['query'] = $segments[1];

			}
		}

		if (isset($segments[0]) && $segments[0] == 'conversation') {
			$vars['view'] = $segments[0];

			if (isset($segments[1])) {
				$vars['layout']	= $segments[1];
			}
		}

		if (isset($segments[0]) && $segments[0] == 'dashboard') {
			$vars['view'] = $segments[0];

			if (isset($segments[1])) {
				$vars['layout']	= $segments[1];
			}
		}


		if (isset($segments[0]) && $segments[0] == 'subscription') {
			$vars['view'] = $segments[0];

			if (isset($segments[1])) {
				$vars['filter']	= $segments[1];
			}
		}

		$count = count($segments);
		if ($count == 1 && in_array( $segments[0], $views)) {
			$vars['view'] = $segments[0];
		}

		unset( $segments );
		return $vars;
	}
}


/**
 * Proxy methods for building urls
 *
 * @since 4.0
 */
function EasyDiscussBuildRoute(&$query)
{
	$router = new EasyDiscussRouter();

	return $router->build($query);
}

/**
 * Proxy methods for parsing urls
 *
 * @since 4.0
 */
function EasyDiscussParseRoute($segments)
{
	$router = new EasyDiscussRouter();

	return $router->parse($segments);
}
