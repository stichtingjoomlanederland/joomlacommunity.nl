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

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';
require_once DISCUSS_HELPERS . '/router.php';

function EasyDiscussBuildRoute(&$query)
{
	$segments	= array();
	$config		= DiscussHelper::getConfig();


	if(isset($query['view']))
	{
		switch($query['view'])
		{
			case 'post':
				// We don't want to include the view for the entry links.
				unset($query['view']);

				if(isset($query['id']))
				{
					$segments[]	= DiscussRouter::getPostAlias( $query['id'] );
					unset($query['id']);
				}

				if( isset( $query['layout' ] ) )
				{
					$segments[] = $query[ 'layout' ];
					unset( $query['layout'] );
				}

				break;
			case 'profile':
				$segments[] = $query['view'];
				unset($query['view']);

				if(isset($query['layout']))
				{
					$segments[]	= $query['layout'];
					unset($query['layout']);
				}

				if(isset($query['id']))
				{
					$segments[]	= DiscussRouter::getUserAlias( $query['id'] );
					unset($query['id']);
				}

				if( isset( $query[ 'category_id' ] ) )
				{
					$aliases		= DiscussRouter::getCategoryAliases( $query['category_id'] );

					foreach( $aliases as $alias )
					{
						$segments[]	= $alias;
					}

					unset( $query[ 'category_id' ] );
				}

				if( isset( $query[ 'viewtype'] ) )
				{
					$segments[]		= $query[ 'viewtype' ];

					unset( $query[ 'viewtype' ] );
				}

				break;
			case 'index':
				$segments[]     = $query[ 'view' ];
				unset( $query[ 'view' ] );

				if( isset( $query[ 'category_id' ] ) )
				{
					$aliases		= DiscussRouter::getCategoryAliases( $query['category_id'] );

					foreach( $aliases as $alias )
					{
						$segments[]	= $alias;
					}

					unset( $query[ 'category_id' ] );
				}
				break;
			case 'ask':
				$segments[]     = $query[ 'view' ];
				unset( $query[ 'view' ] );

				if( isset( $query[ 'category' ] ) )
				{
					$aliases		= DiscussRouter::getCategoryAliases( $query['category'] );

					foreach( $aliases as $alias )
					{
						$segments[]	= $alias;
					}

					unset( $query[ 'category' ] );
				}
				break;
			case 'points':
				$segments[]	= $query[ 'view' ];
				unset( $query[ 'view' ] );

				$segments[]	= $query[ 'layout' ];
				unset( $query[ 'layout' ] );

				$segments[]	= DiscussRouter::getUserAlias( $query[ 'id' ] );
				unset( $query[ 'id' ] );
				break;

			case 'tags':
				$segments[] = $query['view'];
				unset($query['view']);

				if(isset($query['id']))
				{
					$segments[]	= DiscussRouter::getTagAlias( $query['id'] );
					unset($query['id']);
				}
				break;
			case 'users':
				$segments[]		= $query[ 'view' ];
				unset( $query[ 'view' ] );

				if( isset( $query[ 'sorting' ] ) )
				{
					$segments[]     = 'latest';
					unset( $query[ 'sorting' ] );
				}
				break;
			case 'badges':
				$segments[]		= $query[ 'view' ];
				unset( $query[ 'view' ] );

				if(isset($query['id']))
				{
					$segments[]	= DiscussRouter::getAlias( 'badges', $query['id'] );
					unset($query['id']);
					unset( $query['layout'] );
				}

				if( isset( $query['layout' ] ) )
				{
					$segments[] = $query[ 'layout' ];
					unset( $query['layout'] );
				}

				break;
			case 'favourites':
				// We don't want to include the view for the entry links.
				$segments[]		= $query[ 'view' ];
				unset($query['view']);

				break;

			case 'categories':
				$segments[]		= $query[ 'view' ];

				unset( $query['view'] );

				if( isset( $query['layout'] ) )
				{
					$segments[]	= $query[ 'layout' ];
					unset( $query[ 'layout' ] );
				}

					if( isset( $query['category_id' ] ) )
					{
						$segments[]	= DiscussRouter::getAlias( 'category' , $query['category_id'] );
						unset( $query['category_id'] );
					}

				break;
			case 'conversation':
				$segments[]		= $query['view'];
				unset( $query['view' ] );


				if( isset( $query['layout'] ) )
				{
					$segments[]	= $query['layout'];
					unset( $query['layout'] );
				}

				break;
			default:
				$segments[] = $query['view'];
				unset( $query['view'] );
		}
	}

	if( isset( $query['filter'] ) )
	{
		$segments[]		= $query[ 'filter' ];
		unset( $query[ 'filter' ] );
	}

	if( isset( $query['sort'] ) )
	{
		$segments[]		= $query['sort'];
		unset( $query[ 'sort' ] );
	}

	if( !isset($query['Itemid'] ) )
	{
		$query['Itemid']	= DiscussRouter::getItemId();
	}
	return $segments;
}

function EasyDiscussParseRoute( $segments )
{
	$vars	= array();
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$config	= DiscussHelper::getConfig();
	$views	= array( 'attachments' , 'categories' , 'index' , 'post' , 'profile' , 'search' , 'tag' , 'tags', 'users' , 'notifications' , 'badges' , 'ask', 'subscriptions' , 'featured', 'favourites', 'assigned' );


	// @rule: For view=post&id=xxx we do not include

	if( isset($segments[0]) && !in_array( $segments[0] , $views ) )
	{
		$vars[ 'view' ]	= 'post';

		$count	= count($segments);

		if( $count >= 1 )
		{
			// Submission
			$index = 0;
			if( $segments[ $index ] == 'submit' )
			{
				$vars[ 'layout' ]	= $segments[ $index ];
				$index				+= 1;
			}

			if( isset( $segments[ $index ] ) )
			{
				$table			= DiscussHelper::getTable( 'Post' );
				$table->load( $segments[ $index ] , true );

				$vars[ 'id' ]	= $table->id;
				$index			+= 1;
			}

			if( isset( $segments[ $index ] ) )
			{
				if( $segments[ $index ] == 'edit' )
				{
					$vars[ 'layout' ] = $segments[ $index ];
				}
				else
				{
					$vars[ 'sort' ] = $segments[ $index ];
				}
			}
		}
	}

	if( isset($segments[0]) && $segments[0] == 'index' )
	{
		$count	= count($segments);

		if($count > 1)
		{
			$vars[ 'view' ]	= $segments[ 0 ];

			$segments	= DiscussRouter::encodeSegments( $segments );

			//if( in_array( $segments[ $count - 1 ] , array( 'unanswered', 'featured', 'new' ) ) )
			if( in_array( $segments[1] , array( 'allposts','unanswered', 'unresolved', 'unread', 'resolved' ) ) )
			{
				$vars[ 'filter' ]	= $segments[1];


				if( isset( $segments[2] ) )
				{
					$vars[ 'sort' ] =  $segments[2];
				}

			}
		}
	}

	if( isset($segments[0]) && $segments[0] == 'points' )
	{
		$segments = DiscussRouter::encodeSegments($segments);

		// Get the current view
		$vars[ 'view' ]	= $segments[ 0 ];

		// Get the current layout
		$vars[ 'layout' ]	= $segments[ 1 ];

		// Get the user's id.
		$alias 	= $segments[ 2 ];
		
		$id = 0;
		if ($config->get('main_sef_user') == 'realname' || $config->get('main_sef_user') == 'username') {

			$tmp 	= explode('-', $alias);
			$id 	= $tmp[0];
		}

		if( !$id )
		{
			// Username might contains "-" character
			$alias 	= JString::str_ireplace( ':' , '-' , $alias );
			$id 	= DiscussHelper::getUserId( $alias );
		}

		if( !$id )
		{
			// Username might contains "-" character
			$alias 	= JString::str_ireplace( '-' , ' ' , $alias );
			$id 	= DiscussHelper::getUserId( $alias );
		}

		$vars[ 'id' ]	= $id;
	}

	if( isset($segments[0]) && $segments[0] == 'categories' )
	{
		$count	= count($segments);

		if($count > 1)
		{
			$vars[ 'view' ]	= $segments[ 0 ];

			$segments	= DiscussRouter::encodeSegments( $segments );


			if( isset( $segments[ 1 ] ) && $segments[ 1 ] == 'listings')
			{
				// Get the last item since the category might be recursive.
				$cid		= $segments[2];

				$category	= DiscussHelper::getTable( 'Category' );
				$category->load( $cid , true );

				$vars[ 'layout' ]		= $segments[ 1 ];
				$vars[ 'category_id' ]  = $category->id;
			}
			else
			{
				// Get the last item since the category might be recursive.
				$cid		= $segments[1];

				$category	= DiscussHelper::getTable( 'Category' );
				$category->load( $cid , true );

				$vars[ 'category_id' ]  = $category->id;
			}

			if( isset( $segments[ 3 ] ) )
			{
				$vars[ 'filter' ]	= $segments[3];

				if( isset( $segments[4] ) )
				{
					$vars[ 'sort' ] =  $segments[4];
				}
			}
		}
	}

	if( isset($segments[0]) && $segments[0] == 'tags' )
	{
		$count	= count($segments);

		if( $count > 1 )
		{
			$segments		= DiscussRouter::encodeSegments($segments);

			$table			= DiscussHelper::getTable( 'Tags' );
			$table->load( $segments[ 1 ] , true);
			$vars[ 'id' ]	= $table->id;

			if($count > 2)
			{
				if($segments[2] == 'allposts' || $segments[2] == 'featured' || $segments[2] == 'unanswered')
				{
					$vars[ 'filter' ] = $segments[2];
				}

				if(! empty($segments[3]))
				{
					$vars[ 'sort' ] =  $segments[3];
				}
			}
		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset($segments[0]) && $segments[0] == 'profile' )
	{
		$count	= count($segments);

		if( $count > 1 )
		{
			$segments	= DiscussRouter::encodeSegments($segments);

			if($segments[1] == 'edit')
			{
				$vars[ 'layout' ] = 'edit';
			}
			else
			{
				$user	= 0;
				$id		= DiscussHelper::getUserId($segments[1]);

				if ($config->get('main_sef_user') == 'realname' || $config->get('main_sef_user') == 'username') {

					$id 	= explode('-', $segments[1]);
					$id 	= $id[0];
				}

				// Username might contains "-" character
				if ($id) {
					$user	= JFactory::getUser($id);
				}

				$segments[1]	= JString::str_ireplace( '-' , ' ' , $segments[1] );

				if( !$user )
				{
					$id		= DiscussHelper::getUserId( $segments[1] );
					$user	= JFactory::getUser( $id );
				}

				if( !$user )
				{
					// For usernames with spaces, we might need to replace with dashes since SEF will rewrite it.
					$id		= DiscussHelper::getUserId( JString::str_ireplace( '-' , ' ' , $segments[1] ) );
					$user	= JFactory::getUser( $id );
				}

				$vars['id']		= $user->id;
			}

			if( isset( $segments[2] ) )
			{
				$vars[ 'viewtype' ]	= $segments[2];
			}
		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset($segments[0]) && $segments[0] == 'users' )
	{
		$count	= count($segments);

		if($count > 1)
		{
			$vars[ 'sort' ]  = $segments[ 1 ];
		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset($segments[0]) && $segments[0] == 'badges' )
	{
		$count	= count($segments);

		if($count > 1)
		{
			if($segments[1] == 'mybadges')
			{
				$vars[ 'layout' ] = 'mybadges';
			}
			else
			{
				$segments		= DiscussRouter::encodeSegments( $segments );
				$table			= DiscussHelper::getTable( 'Badges' );
				$table->load( $segments[ 1 ] , true );

				$vars[ 'id' ]	= $table->id;
				$vars[ 'layout' ] = 'listings';
			}
		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset($segments[0]) && $segments[0] == 'ask' )
	{
		$count	= count($segments);

		if($count > 1)
		{
			// Get the last item since the category might be recursive.
			$cid		= $segments[ $count - 1 ];

			$category	= DiscussHelper::getTable( 'Category' );
			$category->load( $cid , true );

			$vars[ 'category' ]  = $category->id;

		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset( $segments[ 0 ] ) && $segments[ 0 ] == 'conversation' )
	{

		$vars['view'] 	= $segments[0];

		if( isset( $segments[1] ) )
		{
			$vars['layout']	= $segments[1];
		}
	}

	$count	= count($segments);
	if( $count == 1 && in_array( $segments[0 ] , $views ) )
	{
		$vars['view']	= $segments[0];
	}

	unset( $segments );
	return $vars;
}
