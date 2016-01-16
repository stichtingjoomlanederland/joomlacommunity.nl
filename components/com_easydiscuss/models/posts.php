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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPosts extends EasyDiscussModel
{
	public $isModule = false;

	/**
	 * Post total
	 *
	 * @var integer
	 */
	protected $_total		= null;

	/**
	* Pagination object
	*
	* @var object
	*/
	protected $_pagination	= null;

	/**
	* Post data array
	*
	* @var array
	*/
	protected $_data		= null;

	/**
	 * Parent ID
	 *
	 * @var integer
	 */
	protected $_parent		= null;
	protected $_isaccept	= null;
	protected $_favs		= true;

	static $_lastReply      = array();

	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();
		$limit		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.limit', 'limit', DiscussHelper::getListLimit() );
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal( $sort = 'latest' , $filter = '', $category='', $featuredOnly = 'all')
	{
		$sid = serialize($sort) . serialize($filter) . serialize($category) . serialize($featuredOnly);

		static $_cache = array();

		if( isset( $_cache[$sid] ) )
		{
			$this->_total = $_cache[ $sid];
		}
		else
		{
			$query = $this->_buildQueryTotal($sort, $filter, $category, $featuredOnly);

			$db 	= JFactory::getDBO();
			$db->setQuery( $query );

			$this->_total 	= $db->loadResult();
			$_cache[ $sid ] = $this->_total;
		}

		return $this->_total;
	}

	/**
	 * Removes all finder indexed items for replies
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteRepliesInFinder($postId)
	{
		$db = JFactory::getDBO();

		$query = array();
		$query[] = 'DELETE FROM ' . $db->quoteName('#__finder_links');
		$query[] = 'WHERE ' . $db->quoteName('url') . ' LIKE (' . $db->Quote('%index.php?option=com_easydiscuss&view=post&id=' . $postId . '#reply-%') . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Method to get a pagination object for the posts
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination( $parent_id = 0, $sort = 'latest', $filter='', $category='', $featuredOnly = 'all', $userId = '' )
	{
		$this->_parent	= $parent_id;

		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			$this->_pagination	= DiscussHelper::getPagination( $this->getTotal($sort, $filter, $category, $featuredOnly, $userId), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Retrieve the total number of posts which are resolved.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTotalResolved()
	{
		$db 	= DiscussHelper::getDBO();

		$query	= array();
		$query[] 	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'isresolve' ) . '=' . $db->Quote( 1 );
		$query[]	= 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( DISCUSS_ID_PUBLISHED );
		$query[]	= 'AND ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 );
		$query[]	= 'AND ' . $db->nameQuote('private') . '=' . $db->Quote( 0 );

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		return $db->loadResult();
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	private function _buildQueryTotal( $sort = 'latest', $filter = '' , $category = '', $featuredOnly = 'all', $reply = false, $userId = '' )
	{
		$my	= JFactory::getUser();
		$config = DiscussHelper::getConfig();

		// Get the WHERE and ORDER BY clauses for the query
		if(empty($this->_parent))
		{
			$parent_id = JRequest::getInt('parent_id', 0);
			$this->_parent = $parent_id;
		}

		$filteractive	= (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;
		$where			= $this->_buildQueryWhere( $filter , $category, $featuredOnly, array(), $userId);
		$db				= DiscussHelper::getDBO();

		$orderby		= '';
		$queryExclude	= '';
		$excludeCats	= array();

		$date			= DiscussHelper::getDate();

		// We do not need to check for private categories for replies since replies are posted in that particular discussion.
		if( !$reply )
		{
			$excludeCats = DiscussHelper::getPrivateCategories();
		}

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		$query  = 'SELECT COUNT(a.`id`)';
		$query	.= ' FROM `#__discuss_posts` AS a';

		if( $filteractive == 'myreplies' )
		{
			$query 	.= ' AND a.`parent_id` != 0 AND a.`published`=' . $db->Quote( 1 );
		}

		if( $filter == 'favourites' )
		{
			$query	.= '	LEFT JOIN `#__discuss_favourites` AS f ON f.`post_id` = a.`id`';
		}

		$query	.= $where;

		if(! empty($this->_isaccept))
		{
			$query	.= ' AND a.`answered` = ' . $db->Quote( '1' );
		}

		if( $filteractive == 'unanswered' )
		{
			// Should not fetch posts which are resolved
			$query	.= ' AND a.`isresolve`=' . $db->Quote( 0 );
		}

		if ($filteractive == 'mine') {
			$query .= ' AND a.`user_id`=' . $db->Quote($my->id);
		}

		$query	.= $queryExclude;

		return $query;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	private function _buildQuery($sort = 'latest', $filter = '', $category = '', $featuredOnly = 'all', $reply = false, $exclude = array(), $reference = null, $referenceId = null, $userId = null, $private = null)
	{
		$my = JFactory::getUser();
		$config = DiscussHelper::getConfig();

		// Get the WHERE and ORDER BY clauses for the query
		if (empty($this->_parent)) {
			$parent_id = JRequest::getInt('parent_id', 0);
			$this->_parent = $parent_id;
		}

		if (isset($this->isModule) && $this->isModule == true) {
			$this->_parent = 0;
		}

		$filteractive = (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;
		$where = $this->_buildQueryWhere( $filter , $category, $featuredOnly , $exclude, $userId );

		$db = DiscussHelper::getDBO();

		$orderby		= '';
		$queryExclude	= '';
		$excludeCats	= array();

		$date			= DiscussHelper::getDate();

		// We do not need to check for private categories for replies since replies are posted in that particular discussion.
		if( !$reply )
		{
			$excludeCats = DiscussHelper::getPrivateCategories();
		}

		if (! empty($excludeCats)) {
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query = 'SELECT ';

		// Include polls
		$query 	.= ' (SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . ' = a.' . $db->nameQuote( 'id' ) . ') AS `polls_cnt`,';

		// Include favourites
		$query 	.= ' (SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_favourites' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . ' = a.' . $db->nameQuote( 'id' ) . ') AS `totalFavourites`,';

		// Calculate number replies
		$query 	.= '(SELECT COUNT(1) FROM `#__discuss_posts` WHERE `parent_id` = a.`id` AND `published`="1") AS `num_replies`,';

		// Include attachments
		if( !$reply )
		{
			$query 	.= ' (SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_attachments' ) . ' WHERE ' . $db->nameQuote( 'uid' ) . ' = a.' . $db->nameQuote( 'id' )
					. ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( DISCUSS_QUESTION_TYPE )
					. ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ') AS `attachments_cnt`,';
		}

		//sorting criteria
		if($sort == 'likes')
		{
			$query	.= ' a.`num_likes` as `likeCnt`,';
		}

		if($sort == 'voted')
		{
			$query	.= ' a.`sum_totalvote` as `VotedCnt`,';
		}

		if($my->id != 0)
		{
			$query 	.= ' (SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_votes' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . ' = a.' . $db->nameQuote( 'id' ) . ' AND `user_id` = ' . $db->Quote( $my->id ) . ') AS `isVoted`,';
		}
		else
		{
			$query	.= ' ' . $db->Quote('0') . ' as `isVoted`,';
		}

		$query	.= ' a.`post_status`, a.`post_type`, pt.`suffix` AS post_type_suffix, pt.`title` AS post_type_title , a.*, ';


		$query	.= ' e.`title` AS `category`, a.`legacy`, ';
		$query	.= ' IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) as `lastupdate`';

		$query	.= ', (select count(1) from `#__discuss_votes` where post_id = a.id) as `total_vote_cnt`';

		$query	.= ' FROM `#__discuss_posts` AS a';

		// Join with post types table
		$query 	.= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_post_types' ) . ' AS pt ON a.`post_type`= pt.`alias`';

		// Join with category table.
		$query	.= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS e ON a.`category_id`=e.`id`';

		if( $filter == 'favourites' )
		{
			$query	.= '	LEFT JOIN `#__discuss_favourites` AS f ON f.`post_id` = a.`id`';
		}

		if ($filter == 'assigned') {
			$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_assignment_map' ) . ' AS am ON am.`post_id` = a.id';
			$query 	.= ' AND am.`assignee_id`=' . $db->Quote(JFactory::getUser()->id);
		}

		// 3rd party integrations
		if( !is_null( $reference ) && !is_null( $referenceId ) )
		{
			$query 	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_posts_references' ) . ' AS ref';
			$query	.= ' ON a.' . $db->nameQuote( 'id' ) . '= ref.' . $db->nameQuote( 'post_id' );
			$query	.= ' AND ref.' . $db->nameQuote( 'extension' ) . '=' . $db->Quote( $reference );
			$query	.= ' AND ref.' . $db->nameQuote( 'reference_id' ) . '=' . $db->Quote( $referenceId );
		}

		if( $filter == 'answer' )
		{
			$where 	.= ' AND a.' . $db->nameQuote( 'answered' ) . '=' . $db->Quote( 1 );
		}

		if ($filter == 'mine') {
			$where	.= ' AND a.`user_id`=' . $db->Quote((int)JFactory::getUser()->id);
		}

		if($filteractive == 'unanswered')
		{
			$where 	.= ' AND a.`answered`=' . $db->Quote( 0 );
		}

		if (!EDC::isSiteAdmin() && !EDC::isModerator() && !$private && $filter != 'mine') {
			$where 	.= ' AND a.`private`=' . $db->Quote(0);
		}

		$query	.= $where;
		$query	.= $queryExclude;

		if( $featuredOnly && $config->get('layout_featuredpost_style') != '0' && empty($this->_parent) )
		{
			switch( $config->get('layout_featuredpost_sort', 'date_latest') )
			{
				case 'date_oldest':
					$orderby	= ' ORDER BY a.`replied` ASC'; //used in getdata only
					break;
				case 'order_asc':
					$orderby	= ' ORDER BY a.`ordering` ASC'; //used in getreplies only
					break;
				case 'order_desc':
					$orderby	= ' ORDER BY a.`ordering` DESC'; //used in getdate and getreplies
					break;
				case 'date_latest':
				default:
					$orderby	= ' ORDER BY a.`replied` DESC'; //used in getsticky and get created date
					break;
			}
		}
		else
		{
			switch($sort)
			{
				case 'popular':
					$orderby	= ' ORDER BY `num_replies` DESC, a.`created` DESC'; //used in getdata only
					break;
				case 'hits':
					$orderby	= ' ORDER BY a.`hits` DESC'; //used in getdata only
					break;
				case 'voted':
					$orderby	= ' ORDER BY a.`sum_totalvote` DESC'; //used in getreplies only
					break;
				case 'likes':
					$orderby	= ' ORDER BY a.`num_likes` DESC'; //used in getdate and getreplies
					break;
				case 'activepost':
					$orderby	= ' ORDER BY a.`replied` DESC'; //used in getsticky and getlastreply
					break;
				case 'featured':
					$orderby	= ' ORDER BY a.`featured` DESC, a.`created` DESC'; //used in getsticky and getlastreply
					break;
				case 'oldest':
				case 'replylatest':
					$orderby	= ' ORDER BY a.`created` ASC'; //used in discussion replies
					break;
				case 'latest':
				default:
					$orderby	= ' ORDER BY a.`replied` DESC'; //used in getsticky and get created date
					break;
			}
		}

		$query	.= $orderby;

		// echo $query;exit;

		return $query;
	}

	private function _getDateDiffs(&$results)
	{
		$now = DiscussHelper::getDate();
		$today = explode(' ', $now->toMySQL());
		$today = $today[0];

		if (! empty($results)) {
			for ($i = 0 ; $i < count($results); $i++) {
				$item =& $results[$i];

				//creation date
				$creation = $item->created;
				$creation = explode(' ', $creation);
				$creation = $creation[0];

				//daydiff
				$datetotest = ($item->replied == '0000-00-00 00:00:00' ) ? $item->created : $item->replied;
				$datesegment = explode(' ', $datetotest);
				$datesegment = $datesegment[0];

				$noofdays = floor((abs(strtotime($today) - strtotime($creation)) / (60*60*24)));
				$daydiff = floor((abs(strtotime($today) - strtotime($datesegment)) / (60*60*24)));
				$timediff = $this->calcTimeDiff(strtotime($now->toMySQL()), strtotime($datetotest));

				// var_dump( $item->noofdays, $noofdays );
				// var_dump( $item->daydiff, $daydiff );
				// var_dump( $item->timediff, $timediff );

				$item->noofdays = $noofdays;
				$item->daydiff = $daydiff;
				$item->timediff = $timediff;
			}
		}
	}

	private function calcTimeDiff( $date1, $date2 )
	{
	   $diff = abs($date1-$date2);
	   $seconds = 0;
	   $hours  = 0;
	   $minutes = 0;

	   if ($diff % 86400 > 0) {

	       $rest = ($diff % 86400);
	       $days = ($diff - $rest) / 86400;

	       if ($rest % 3600 > 0) {
	           $rest1 = ($rest % 3600);
	           $hours = ($rest - $rest1) / 3600;

	           if( $rest1 % 60 > 0 ) {
	               $rest2 = ($rest1 % 60);
	               $minutes = ($rest1 - $rest2) / 60;
	               $seconds = $rest2;
	           } else {
	               $minutes = $rest1 / 60;
	           }
	       } else {
	           $hours = $rest / 3600;
	       }
	   } else {
	       $days = $diff / 86400;
	   }

	   $hours = ($days * 24) + $hours;
	   $time = $hours . ':' . $minutes . ':' . $seconds;
	   return $time;
	}

	private function _buildQueryWhere($filter='' , $category = '', $featuredOnly = 'all' , $exclude = array(), $userId = '' )
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$user_id		= JRequest::getInt('user_id');

		$search			= $db->getEscaped( JRequest::getString( 'query' , '' ) );
		$filteractive	= (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;
		$where			= array();


		$where[]		= ' a.`published` = ' . $db->Quote('1');

		// get all posts where parent_id = 0
		if(empty($this->_parent))
		{
			$this->_parent	= '0';
		}

		if( $user_id )
		{
			$where[]	= ' a.`user_id` = ' . $db->Quote( (int) $user_id );
		}

		if($filteractive == 'featured' || $featuredOnly === true)
		{
			$where[]	= ' a.`featured` = ' . $db->Quote('1');
		}
		else if( $featuredOnly === false && $filter != 'resolved' )
		{
			$where[]	= ' a.`featured` = ' . $db->Quote('0');
		}

		if( $filteractive == 'myposts' )
		{
			$my = JFactory::getUser();
			$where[]	= ' a.`user_id`= ' .$db->Quote( $my->id );
		}

		if( $filteractive == 'userposts' && !empty($userId) )
		{
			$where[]	= ' a.`user_id`= ' .$db->Quote( $userId );
		}

		if( $filteractive == 'new' )
		{
			$config		= DiscussHelper::getConfig();
			$where[]	= ' DATEDIFF( ' . $db->Quote( DiscussHelper::getDate()->toMySQL() ) . ', a.`created` ) <= ' . $db->Quote( $config->get( 'layout_daystostaynew' ) );
		}

		if( $filteractive == 'myreplies' )
		{
			$my = JFactory::getUser();
			$where[]	= ' a.`parent_id` != ' . $db->Quote( 0 ) . ' AND a.`user_id`=' . $db->Quote( $my->id );
		}

		if( !empty( $exclude ) )
		{
			$excludePost	= 'a.`id` NOT IN(';

			for( $i = 0; $i < count( $exclude ); $i++ )
			{
				$excludePost	.= $db->Quote( $exclude[ $i ] );

				if( next( $exclude) !== false )
				{
					$excludePost	.= ',';
				}
			}

			$excludePost 	.= ')';
			$where[]		= $excludePost;
		}

		// @since 3.0
		if( $filteractive == 'unread' )
		{
			$my			= JFactory::getUser();
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $my->id );

			$readPosts	= $profile->posts_read;
			if( $readPosts )
			{
				$readPosts  = unserialize( $readPosts );
				if( count( $readPosts ) > 1 )
				{
					$extraSQL	= implode( ',', $readPosts);
					$where[]	= ' a.`id` NOT IN (' . $extraSQL . ')';
				}
				else
				{
					$where[]	= ' a.`id` != ' . $db->Quote( $readPosts[0] );
				}
			}
			$where[]	= ' a.`legacy` = 0';
		}

		if( $filteractive == 'unanswered' )
		{
			// Should not fetch posts which are resolved
			$where[] = ' a.`isresolve`=' . $db->Quote( 0 );
			$where[] = ' a.`created` = a.`replied`';
		}

		if( $filteractive == 'favourites' )
		{
			$my	= JFactory::getUser();

			if( empty($userId) )
			{
				$id = $my->id;
			}
			else
			{
				$id = $userId;
			}

			$where[] = ' f.`created_by` = ' . $db->quote( $id );
		}

		if( $filteractive == 'unresolved' )
		{
			$where[]	= ' a.`isresolve`= ' .$db->Quote( '0' );
		}

		// @since 3.1 resolved filter
		if( $filteractive == 'resolved' )
		{
			$where[]	= ' a.`isresolve`=' . $db->Quote( 1 );
		}

		if($this->_parent=='allreplies')
		{
			$where[]	= ' a.`parent_id` != ' . $db->Quote( '0' );

			$excludedCategories		= DiscussHelper::getPrivateCategories();

			if(! empty($excludedCategories))
			{
				$where[]	= ' a.`category_id` NOT IN (' . implode(',', $excludedCategories) . ')';
			}
		}
		else
		{
			$where[] = ' a.`parent_id` = ' . $db->Quote( $this->_parent );

			if( $this->_isaccept )
			{
				$where[]	= ' a.`answered` = ' . $db->Quote( '1' );
			}
			else
			{
				$where[]	= ' a.`answered` = ' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[]	= ' LOWER( a.`title` ) LIKE \'%' . $search . '%\' ';
		}

		// Filter by category
		if( !empty( $category ) )
		{
			require_once dirname(__FILE__) . '/categories.php';

			if( !is_array( $category ) )
			{
				$category 	= array( $category );
			}

			$tmpCategoryArr = array();

			for( $i = 0 ; $i < count( $category ); $i++ )
			{
				$categoryId 	= $category[ $i ];

				// Fetch all subcategories from within this category
				$model	= $this->getInstance( 'Categories' , 'EasyDiscussModel' );
				$childs	= $model->getChildIds( $categoryId );

				if( $childs )
				{
					$childs[]	= $categoryId;

					foreach( $childs as &$child )
					{
						$child	= $db->Quote( $child );
						$tmpCategoryArr[]   = $child;
					}
				}
				else
				{
					$tmpCategoryArr[]   = $db->Quote( $category[ $i ] );
				}
			}

			if( count( $tmpCategoryArr ) > 0 )
			{
				$where[]	= ' a.`category_id` IN (' . implode( ',' , $tmpCategoryArr ) . ')';
			}
		}
		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	private function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order',		'filter_order',		'created DESC'	, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order_Dir',	'filter_order_Dir',	''				, 'word' );

		$orderby	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Retrieve a list of discussions
	 *
	 * @since	1.0
	 * @param	array 	An array of options
	 * the ignorePostIds must be a string when pass into this method.
	 *
	 */
	public function getDiscussions( $options = array() )
	{
		$sort 			= isset( $options[ 'sort' ] ) ? $options[ 'sort' ] : 'latest';
		$pagination		= isset( $options[ 'pagination' ] ) ? $options[ 'pagination' ] : true;
		$limitstart 	= isset( $options[ 'limitstart' ] ) ? $options[ 'limitstart' ] : null;
		$filter 		= isset( $options[ 'filter' ] ) ? $options[ 'filter' ] : '';
		$category 		= isset( $options[ 'category' ] ) ? $options[ 'category'  ] : '';
		$limit 			= isset( $options[ 'limit' ] ) ? $options[ 'limit'  ] : null;
		$featured 		= isset( $options[ 'featured' ] ) ? $options[ 'featured' ] : 'all';
		$exclude 		= isset( $options[ 'exclude' ] ) ? $options[ 'exclude' ] : array();
		$reference		= isset( $options[ 'reference' ] ) ? $options[ 'reference' ] : null;
		$referenceId	= isset( $options[ 'reference_id' ] ) ? $options[ 'reference_id' ] : null;
		$userId			= isset( $options[ 'userId' ] ) ? $options[ 'userId' ] : null;
		$private 		= isset($options['private']) ? $options['private'] : null;

		$query			= $this->_buildQuery( $sort , $filter , $category , $featured , false, $exclude , $reference , $referenceId, $userId );


		$limitstart		= is_null( $limitstart ) ? $this->getState( 'limitstart') : $limitstart;
		$limit			= is_null( $limit ) ? $this->getState( 'limit') : $limit;

		if( $limit == DISCUSS_NO_LIMIT )
		{
			$result = $this->_getList( $query , 0 );
			$this->_getDateDiffs($result);
		}
		else
		{
			if( $pagination )
			{
				$result = $this->_getList( $query , $limitstart , $limit );
				$this->_getDateDiffs($result);
			}
			else
			{
				$result = $this->_getList( $query , 0 , $limit );
				$this->_getDateDiffs($result);
			}
		}

		return $result;
	}

	/**
	 * Method to get posts item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData($usePagination = true, $sort = 'latest', $limitstart = null, $filter = '', $category = '', $limit = null, $featuredOnly = 'all', $userId = null, $isModule = false)
	{
		$query = $this->_buildQuery($sort, $filter , $category, $featuredOnly, false, array(), null, null, $userId );

		if ($usePagination) {
			$limitstart = is_null( $limitstart ) ? $this->getState( 'limitstart') : $limitstart;
			$limit = is_null( $limit ) ? $this->getState( 'limit') : $limit;

			$this->_data = $this->_getList($query, $limitstart , $limit);
			$this->_getDateDiffs($this->_data);
		}
		else
		{
			$limit = is_null( $limit ) ? $this->getState( 'limit') : $limit;
			$this->_data = $this->_getList($query, 0 , $limit);
			$this->_getDateDiffs($this->_data);

		}

		if ($this->_favs == true) {
			return $this->_data;
		}
	}

	public function clearData()
	{
		$this->_data = null;
	}


	/**
	 * Method to get replies
	 *
	 * @access public
	 * @return array
	 */
	public function getReplies( $id, $sort = 'replylatest' , $limitstart = null, $limit = null )
	{
		$db					= DiscussHelper::getDBO();
		$this->_parent		= $id;
		$this->_isaccept	= false;

		$isReplies		= ( $id == 'allreplies' ) ? false : true;
		$query			= $this->_buildQuery( $sort , '' , '' , 'all' , $isReplies );

		$result = '';
		if( !empty( $limitstart ) )
		{
			if(empty($limit))
			{
				$limit = $this->getState('limit');
			}
			$result = $this->_getList($query, $limitstart , $limit);
			$this->_getDateDiffs($result);
		}
		else
		{
			if( !empty($limit) )
			{
				$result	= $this->_getList($query, 0 , $limit);
				$this->_getDateDiffs($result);
			}
			else
			{
				$result	= $this->_getList( $query );
				$this->_getDateDiffs($result);
			}
		}

		return $result;
	}

	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	public function publish( $categories = array(), $publish = 1 )
	{
		$config = DiscussHelper::getConfig();

		if( count( $categories ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$tags	= implode( ',' , $categories );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $tags . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// We need to update the parent post last replied date
			foreach( $categories as $postId )
			{
				// Load the reply
				$reply = DiscussHelper::getTable( 'Posts' );
				$reply->load( $postId );

				// We only need replies
				if( !empty( $reply->parent_id ) )
				{
					$parent = DiscussHelper::getTable( 'Post' );
					$parent->load( $reply->parent_id );

					// Check if current reply date is more than the last replied date of the parent to determine if this reply is new or is an old pending moderate reply.
					if( $reply->created > $parent->replied )
					{
						$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' ) . ' '
								. 'SET ' . $db->nameQuote( 'replied' ) . '=' . $db->Quote( $reply->created ) . ' '
								. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $parent->id );

						$db->setQuery( $query );

						if( !$db->query() )
						{
							$this->setError($this->_db->getErrorMsg());
							return false;
						}
					}
				}
			}

			return true;
		}
		return false;
	}

	public function getPostsBy( $type, $typeId = 0, $sort = 'latest', $limitstart = null , $published = DISCUSS_FILTER_PUBLISHED , $search = '' , $limit = null )
	{
		$db	= DiscussHelper::getDBO();

		$queryPagination	= false;
		$queryWhere		= '';
		$queryOrder		= '';
		$queryLimit		= '';
		$queryWhere		= '';

		switch( $published )
		{
			case DISCUSS_FILTER_PUBLISHED:
			default:
				$queryWhere	= ' WHERE a.`published` = ' . $db->Quote('1');
				break;
		}

		$contentId	= '';
		$isIdArray	= false;
		if(is_array($typeId))
		{
			if(count($typeId) > 1)
			{
				$contentId	= implode(',', $typeId);
				$isIdArray	= true;
			}
			else
			{
				$contentId	= $typeId[0];
			}
		}
		else
		{
			$contentId	= $typeId;
		}

		switch( $type )
		{
			case 'category':
				$queryWhere	.= ($isIdArray) ? ' AND a.`category_id` IN ('. $contentId .')' : ' AND a.`category_id` = ' . $db->Quote($contentId);
				break;
			case 'user':
				$queryWhere	.= ' AND a.`user_id`=' . $db->Quote( $contentId );
				break;
			default:
				break;
		}

		if( ! empty($search) )
		{
			$queryWhere	.= ' AND a.`title` LIKE ' . $db->Quote( '%' . $search . '%' );
		}


		//getting only main posts.
		$queryWhere	.= ' AND a.`parent_id` = 0';

		switch( $sort )
		{
			case 'latest':
				$queryOrder	= ' ORDER BY a.`created` DESC';
				break;
			case 'popular':
				$queryOrder	= ' ORDER BY a.`hits` DESC';
				break;
			case 'alphabet':
				$queryOrder	= ' ORDER BY a.`title` ASC';
			case 'likes':
				$queryOrder	= ' ORDER BY a.`num_likes` DESC';
				break;
			default :
				break;
		}

		$limitstart = is_null($limitstart) ? (int) $this->getState('limitstart') : $limitstart;
		$limit = is_null($limit) ? (int) $this->getState('limit') : $limit;

		$limitstart = abs($limitstart);

		// Ensure that nobody can temper with the limit
		if ($limit < 0) {
			$limit = 10;
		}

		$queryLimit = ' LIMIT ' . $limitstart . ',' . $limit;

		$query	= 'SELECT COUNT(1) FROM `#__discuss_posts` AS a';
		$query	.= $queryWhere;

		$db->setQuery( $query );
		$this->_total	= $db->loadResult();

		jimport('joomla.html.pagination');
		// $this->_pagination	= new JPagination( $this->_total , $limitstart , $limit);
		$this->_pagination	= DiscussHelper::getPagination( $this->_total, $limitstart, $limit );


		$date	= DiscussHelper::getDate();

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
		$query	.= ' a.`id`, a.`title`, a.`alias`, a.`created`, a.`modified`, a.`replied`, a.`legacy`,';
		$query	.= ' a.`content`, a.`category_id`, a.`published`, a.`ordering`, a.`vote`, a.`hits`, a.`islock`,';
		$query	.= ' a.`featured`, a.`isresolve`, a.`isreport`, a.`user_id`, a.`parent_id`,';
		$query	.= ' a.`user_type`, a.`poster_name`, a.`poster_email`, a.`num_likes`,';
		$query	.= ' a.`num_negvote`, a.`sum_totalvote`,a.`answered`,';
		$query	.= ' a.`post_status`, a.`post_type`, pt.`title` AS `post_type_title`,pt.`suffix` AS `post_type_suffix`,';
		$query	.= ' count(b.id) as `num_replies`,';
		$query	.= ' c.`title` AS `category`, a.`password`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a';
		$query	.= '	 LEFT JOIN `#__discuss_posts` AS b ON a.`id` = b.`parent_id`';
		$query	.= '	 AND b.`published` = 1';
		$query	.= '	 LEFT JOIN `#__discuss_category` AS c ON a.`category_id` = c.`id`';
		$query 	.= '	LEFT JOIN `#__discuss_post_types` AS pt ON a.`post_type` = pt.`alias`';

		$query	.= $queryWhere;

		$query	.= ' GROUP BY (a.id)';

		$query .= $queryOrder;
		$query .= $queryLimit;

		$db->setQuery($query);
		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();

		return $result;
	}

	public function setLastReplyBatch( $ids )
	{
		$authorIds  = array();

		if( count($ids) > 0 )
		{
			$db	= DiscussHelper::getDBO();

			$query = 'SELECT * FROM `#__discuss_posts` as a';
			if( count( $ids ) == 1 )
			{
				$query .= ' WHERE a.`parent_id` = ' . $db->Quote( $ids[0] );
			}
			else
			{
				$query .= ' WHERE a.`parent_id` IN (' . implode(',', $ids) . ')';
			}
			$query .= ' and a.`id` = ( select max( b.`id` ) from `#__discuss_posts` as b where a.`parent_id` = b.`parent_id` )';

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					self::$_lastReply[ $item->parent_id ] = $item;
					$authorIds[]    = $item->user_id;
				}
			}

			foreach( $ids as $id )
			{
				if( ! isset( self::$_lastReply[ $id ] ) )
				{
					self::$_lastReply[ $id ] = '';
				}
			}
		}

		return $authorIds;

	}


	public function getLastReply($id)
	{
		if( isset( self::$_lastReply[ $id ] ) )
		{
			return self::$_lastReply[ $id ];
		}

		$db	= DiscussHelper::getDBO();
		$query = 'SELECT * FROM `#__discuss_posts` WHERE ' . $db->nameQuote('parent_id') . ' = ' . $db->Quote($id) . ' ORDER BY '	. $db->nameQuote('created') . ' DESC LIMIT 1';
		$db->setQuery( $query );
		$result = $db->loadObject();

		self::$_lastReply[ $id ] = $result;
		return $result;
	}


	public function getTotalReplies( $id )
	{
		$db	= DiscussHelper::getDBO();
		$query = 'SELECT COUNT(id) AS `replies` FROM `#__discuss_posts` WHERE `parent_id` = ' . $db->Quote($id);
		$query	.= ' AND `answered` = ' . $db->Quote( '0' );
		$query	.= ' AND `published` = ' . $db->Quote('1');

		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves the total number of comments for this particular discussion.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		$id		The post id
	 * @param	string	$type	Type of comments to calculate (post to calculate individual post comment count, thread to calculate full thread comment count)
	 * @return	int
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public static function getTotalComments( $postid, $type = 'post' )
	{

		static $loaded = array();

		$sig    = $postid . $type;

		if( isset( $loaded[$sig] ) )
			return $loaded[$sig];

		$db	= DiscussHelper::getDBO();

		$ids = array();

		$count = 0;

		if( $type == 'thread' )
		{
			$query = 'SELECT `id` FROM `#__discuss_posts` WHERE `parent_id` = ' . $db->quote( $postid );
			$db->setQuery( $query );
			$ids = $db->loadResultArray();
			array_unshift( $ids, $postid );
		}
		else
		{
			$ids = array( $postid );
		}

		foreach( $ids as $id )
		{
			$query	= 'SELECT COUNT(1) FROM `#__discuss_comments` WHERE `post_id` = ' . $db->quote( $id );
			$db->setQuery( $query );

			$result = $db->loadResult();

			$tmpSig = $result . 'post';
			$loaded[ $tmpSig ] = $result;

			$count += (int) $result;
		}

		$loaded[$sig] = $count;

		return $loaded[$sig];
	}

	/**
	 * Method to retrieve blog posts based on the given tag id.
	 *
	 * @access public
	 * @param	int		$tagId	The tag id.
	 * @return	array	$rows	An array of blog objects.
	 */
	public function getTaggedPost( $tagId = 0, $sort	= 'latest', $filter = '', $limitStart = '' )
	{
		if( $tagId ==	0 )
			return false;

		if( is_array($tagId) && empty($tagId) )
			return false;

		$db = DiscussHelper::getDBO();
		$limit = (int) $this->getState('limit');
		$limitstart = (empty($limitStart) ) ? $this->getState('limitstart') : $limitStart;

		$limitstart = abs($limitstart);

		if ($limit < 0) {
			$limit = 0;
		}

		$filteractive	= (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;

		$date		= DiscussHelper::getDate();

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', b.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', b.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', b.`created`) as `timediff`,';


		// Include polls
		$query 	.= ' (SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ') AS `polls_cnt`,';

		// Include favourites
		$query 	.= ' (SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_favourites' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ') AS `totalFavourites`,';

		// Include attachments
		$query 	.= ' (SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_attachments' ) . ' WHERE ' . $db->nameQuote( 'uid' ) . ' = b.' . $db->nameQuote( 'id' )
				. ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( DISCUSS_QUESTION_TYPE )
				. ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ') AS `attachments_cnt`,';


		//sorting criteria
		if($sort == 'likes')
		{
			$query	.= ' b.`num_likes` as `likeCnt`,';
		}

		if($sort == 'popular')
		{
			$query	.= ' count(c.id) as `PopularCnt`,';
		}

		if($sort == 'voted')
		{
			$query	.= ' b.`sum_totalvote` as `VotedCnt`,';
		}

		$queryWhere = '';

		if( is_array($tagId) )
		{
			$queryWhere = ' WHERE a.tag_id IN (' . implode(',', $tagId) . ')';

		}else{
			$queryWhere = ' WHERE a.tag_id = ' . $db->Quote( $tagId );
		}

		$query	.= ' b.`id`, b.`title`, b.`alias`, b.`created`, b.`modified`, b.`replied`,';
		$query	.= ' b.`content`, b.`published`, b.`ordering`, b.`vote`, b.`hits`, b.`islock`,';
		$query	.= ' b.`featured`, b.`isresolve`, b.`isreport`, b.`user_id`, b.`parent_id`,';
		$query	.= ' b.`user_type`, b.`poster_name`, b.`poster_email`, b.`num_likes`,';
		$query	.= ' b.`post_status`, b.`post_type`,pt.`suffix` AS post_type_suffix, pt.`title` AS post_type_title ,';
		$query	.= ' b.`num_negvote`, b.`sum_totalvote`, b.`category_id`, d.`title` AS category, b.`password`, ';
		$query	.= ' count(b.id) as `num_replies`, b.`legacy`';

		if( is_array($tagId) ) {
			$query	.= ' FROM `#__discuss_posts` AS b';
			$query	.= ' INNER JOIN `#__discuss_posts_tags` AS a ON a.post_id = b.id';
			$query	.= ' INNER JOIN `#__discuss_tags` AS e ON e.id = a.tag_id';
			$query	.= ' INNER JOIN `#__discuss_category` AS d ON d.id = b.category_id';

		}else{

			$query	.= ' FROM ' . $db->nameQuote( '#__discuss_posts_tags' ) . ' AS a ';
			$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS b ';
			$query	.= ' ON a.post_id=b.id ';
			$query	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS c ';
			$query	.= ' ON b.id=c.parent_id ';
			$query	.= ' AND c.`published` = ' . $db->Quote('1');
			$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS d ';
			$query	.= ' ON d.`id`=b.`category_id` ';
		}

		// Join with post types table
		$query 	.= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_post_types' ) . ' AS pt ON b.`post_type`= pt.`alias`';

		$query	.= $queryWhere;
		$query	.= ' AND b.`published` = ' . $db->Quote('1');
		$query	.= ' AND b.`private` = ' . $db->Quote('0');

		$excludeCats = DiscussHelper::getPrivateCategories();

		if(!empty($excludeCats)) {
			$query .= ' AND b.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		if($filteractive == 'featured')
		{
			$query .= ' AND b.`featured` = ' . $db->Quote('1');
		}

		$orderby = '';
		switch($sort)
		{
			case 'popular':
				$orderby	= ' ORDER BY `PopularCnt` DESC, b.created DESC'; //used in getdata only
				break;
			case 'hits':
				$orderby	= ' ORDER BY b.hits DESC'; //used in getdata only
				break;
			case 'voted':
				$orderby	= ' ORDER BY b.`sum_totalvote` DESC, b.created DESC'; //used in getreplies only
				break;
			case 'likes':
				$orderby	= ' ORDER BY b.`num_likes` DESC, b.created DESC'; //used in getdate and getreplies
				break;
			case 'activepost':
				$orderby	= ' ORDER BY b.featured DESC, b.replied DESC'; //used in getsticky and getlastreply
				break;
			case 'featured':
			case 'latest':
			default:
				$orderby	= ' ORDER BY b.featured DESC, b.created DESC'; //used in getsticky and get created date
				break;
		}

		if( is_array($tagId) )
		{
			$orderby =  $orderby . ', count(b.id) DESC';
		}

		if($filteractive == 'unanswered')
		{
			$groupby	= ' GROUP BY b.`id` HAVING(COUNT(c.id) = 0)';
		}
		else
		{
			$groupby	= ' GROUP BY b.`id`';
		}

		if( is_array($tagId) )
		{
			$groupby	= ' GROUP BY b.id HAVING (count(b.id) >= ' . count($tagId) . ')';
		}

		$query	.= $groupby . $orderby;

		//total tag's post sql
		$totalQuery = 'SELECT COUNT(1) FROM (';
		$totalQuery .= $query;
		$totalQuery .= ') as x';

		$query	.= ' LIMIT ' . $limitstart . ',' . $limit;

		$db->setQuery( $query );
		$rows	= $db->loadObjectList();

		$db->setQuery( $totalQuery );
		$db->loadResult();
		$this->_total	= $db->loadResult();

		$this->_pagination	= DiscussHelper::getPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		return $rows;
	}

	/**
	 * Get all child posts based on parent_id given
	 */
	public function getAllReplies($parent_id)
	{
		$db = DiscussHelper::getDBO();

		$query = 'SELECT * FROM #__discuss_posts';
		$query	.= ' WHERE `published` = 1';
		$query	.= ' AND ' . $db->nameQuote('parent_id') . ' = ' . $db->Quote($parent_id);

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function deleteAllReplies($parent_id)
	{
		if( !$parent_id )
		{
			return false;
		}

		$db = DiscussHelper::getDBO();

		$query = 'DELETE FROM #__discuss_posts WHERE ' .	$db->nameQuote('parent_id') . ' = ' . $db->Quote($parent_id);
		$db->setQuery($query);

		return $db->query();
	}

	public function getNegativeVote( $postId )
	{
		$db = DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_votes`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($postId);
		$query	.= ' AND `value` = ' . $db->Quote('-1');

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	public function getComments( $postId, $limit = null, $limitstart = null )
	{
		$db		= DiscussHelper::getDBO();
		$date	= DiscussDateHelper::getDate();
		$offset	= DiscussDateHelper::getOffSet( true );

		$query	= 'SELECT DATEDIFF('. $db->Quote( $date->toMySQL( true ) ) . ', DATE_ADD(a.`created`, INTERVAL '.$offset.' HOUR ) ) as `noofdays`, '
				. ' DATEDIFF(' . $db->Quote( $date->toMySQL( true ) ) . ', DATE_ADD(a.`created`, INTERVAL '.$offset.' HOUR ) ) as `daydiff`, '
				. ' TIMEDIFF(' . $db->Quote( $date->toMySQL( true ) ). ', DATE_ADD(a.`created`, INTERVAL '.$offset.' HOUR ) ) as `timediff`, '
				. ' a.* ';
		$query	.= ' FROM `#__discuss_comments` AS a';
		if( is_array($postId) )
		{
			if( count( $postId ) == 1 )
			{
				$query	.= ' WHERE a.`post_id` = ' . $db->Quote( $postId );
				$query	.= ' ORDER BY a.`created` ASC';
			}
			else
			{
				$query	.= ' WHERE a.`post_id` IN (' . implode( ',', $postId ) . ')';
				$query	.= ' ORDER BY a.post_id, a.`created` ASC';
			}
		}
		else
		{
			$query	.= ' WHERE a.`post_id` = ' . $db->Quote( $postId );
			$query	.= ' ORDER BY a.`created` ASC';
		}



		if( $limit !== null )
		{
			if( $limitstart !== null )
			{
				$query .= ' LIMIT ' . $limitstart . ',' . $limit;
			}
			else
			{
				$query .= ' LIMIT ' . $limit;
			}
		}

		$db->setQuery( $query ) ;
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get replies
	 *
	 * @access public
	 * @return array
	 */
	public function getAcceptedReply( $id )
	{
		$db				= DiscussHelper::getDBO();
		$this->_parent		= $id;
		$this->_isaccept	= true;

		$query			= $this->_buildQuery( 'latest' , 'answer' , '', 'all', true);

		$db->setQuery($query);
		$result = $db->loadObjectList();
		$this->_getDateDiffs($result);

		return $result;
	}

	public function getUnresolvedCount( $filter = '' , $category = '' , $tagId = '', $featuredOnly = 'all', $queryOnly = false )
	{
		$db	= DiscussHelper::getDBO();
		$my	= JFactory::getUser();

		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= DiscussHelper::getPrivateCategories();
		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT COUNT(a.`id`) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a';

		if(! empty($tagId))
		{
			$query	.= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query	.= '	ON a.`id` = c.`post_id`';
			$query	.= '	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query	.= ' WHERE a.`parent_id` = ' . $db->Quote(0);
		$query	.= ' AND a.`published`=' . $db->Quote(1);

		// @rule: Should not calculate resolved posts
		$query	.= ' AND a.`isresolve`=' . $db->Quote(0);

		if( $featuredOnly === true )
		{
			$query	.= ' AND a.`featured`=' . $db->Quote(1);
		}
		else if( $featuredOnly === false)
		{
			$query	.= ' AND a.`featured`=' . $db->Quote(0);
		}

		if (!EDC::isSiteAdmin() && !EDC::isModerator()) {
			$query	.= ' AND a.`private`=' . $db->Quote(0);
		}


		if( $category )
		{
			if( !is_array( $category ) )
			{
				$category 	= array( $category );
			}

			$model 		= DiscussHelper::getModel( 'Categories' );

			foreach( $category as $categoryId )
			{
				$data		= $model->getChildIds( $categoryId );

				if( $data )
				{
					foreach( $data as $childCategory )
					{
						$childs[]	= $childCategory;
					}
				}
				$childs[]	= $categoryId;
			}

			$query	.= ' AND a.`category_id` IN (' . implode( ',' , $childs ) . ')';
		}

		$query	.= $queryExclude;


		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function getResolvedCount( $filter = '' , $category = '' , $tagId = '', $featuredOnly = 'all', $queryOnly = false )
	{
		$db	= DiscussHelper::getDBO();
		$my	= JFactory::getUser();

		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= DiscussHelper::getPrivateCategories();
		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT COUNT(a.`id`) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a';

		if(! empty($tagId))
		{
			$query	.= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query	.= '	ON a.`id` = c.`post_id`';
			$query	.= '	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query	.= ' WHERE a.`parent_id` = ' . $db->Quote(0);
		$query	.= ' AND a.`published`=' . $db->Quote(1);

		// @rule: Should not calculate resolved posts
		$query	.= ' AND a.`isresolve`=' . $db->Quote(1);

		if( $featuredOnly === true )
		{
			$query	.= ' AND a.`featured`=' . $db->Quote(1);
		}
		else if( $featuredOnly === false)
		{
			$query	.= ' AND a.`featured`=' . $db->Quote(0);
		}

		if( $category )
		{
			if( !is_array( $category ) )
			{
				$category 	= array( $category );
			}

			$model 		= DiscussHelper::getModel( 'Categories' );

			foreach( $category as $categoryId )
			{
				$data		= $model->getChildIds( $categoryId );

				if( $data )
				{
					foreach( $data as $childCategory )
					{
						$childs[]	= $childCategory;
					}
				}
				$childs[]	= $categoryId;
			}

			$query	.= ' AND a.`category_id` IN (' . implode( ',' , $childs ) . ')';
		}

		$query	.= $queryExclude;


		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function getUnansweredCount( $filter = '' , $category = '' , $tagId = '', $featuredOnly = 'all' )
	{
		$db	= DiscussHelper::getDBO();
		$my	= JFactory::getUser();

		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= DiscussHelper::getPrivateCategories();
		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		$query	= 'SELECT COUNT(a.`id`) FROM `#__discuss_posts` AS a';
		$query	.= '	LEFT JOIN `#__discuss_posts` AS b';
		$query	.= '	ON a.`id`=b.`parent_id`';
		$query	.= '	AND b.`published`=' . $db->Quote('1');

		if(! empty($tagId))
		{
			$query	.= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query	.= '	ON a.`id` = c.`post_id`';
			$query	.= '	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND a.`published`=' . $db->Quote('1');

		// @rule: Should not calculate resolved posts
		$query	.= ' AND a.`isresolve`=' . $db->Quote( 0 );

		if( $featuredOnly === true )
		{
			$query	.= ' AND a.`featured`=' . $db->Quote('1');
		}
		else if( $featuredOnly === false)
		{
			$query	.= ' AND a.`featured`=' . $db->Quote('0');
		}

		if (!EDC::isSiteAdmin() && !EDC::isModerator()) {
			$query	.= ' AND `private`=' . $db->Quote(0);
		}

		if( $category )
		{
			$model	= DiscussHelper::getModel( 'Categories' );
			$childs	= $model->getChildIds( $category );
			$childs[]	 = $category;
			$query	.= ' AND a.`category_id` IN (' . implode( ',' , $childs ) . ')';
		}
		$query	.= ' AND b.`id` IS NULL';
		$query	.= $queryExclude;

		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function getUnreadCount( $category = 0 , $excludeFeatured = false )
	{
		$db		= DiscussHelper::getDBO();
		$my		= JFactory::getUser();

		$excludeCats	= DiscussHelper::getPrivateCategories();

		if( !is_array( $category ) )
		{
			$category 	= array( $category );
		}

		$catModel	= DiscussHelper::getModel('Categories');

		$childs 	= array();

		foreach( $category as $categoryId )
		{
			$data		= $catModel->getChildIds( $categoryId );

			if( $data )
			{
				foreach( $data as $childCategory )
				{
					$childs[]	= $childCategory;
				}
			}

			$childs[]	= $categoryId;
		}

		if( empty( $category ) )
		{
			$categoryIds 	= false;
		}
		else
		{
			$categoryIds	= array_diff($childs, $excludeCats);

			if( empty( $categoryIds ) )
			{
				return '0';
			}
		}

		$profile    = DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		$readPosts  = $profile->posts_read;
		$extraSQL   = '';


		if( $readPosts )
		{
			$readPosts  = unserialize( $readPosts );
			if( count( $readPosts ) > 1 )
			{
				$extraSQL   = implode( ',', $readPosts);
				$extraSQL   = ' AND `id` NOT IN (' . $extraSQL . ')';
			}
			else
			{
				$extraSQL   = ' AND `id` != ' . $db->Quote( $readPosts[0] );
			}
		}

		$query = 'SELECT COUNT(1) FROM `#__discuss_posts`';
		$query .= ' WHERE `published` = ' . $db->Quote( '1' );
		$query .= ' AND `parent_id` = ' . $db->Quote( '0' );

		if( $categoryIds && !( count( $categoryIds ) == 1 && empty( $categoryIds[0] ) ) )
		{

			if( count( $categoryIds ) == 1 )
			{
				$query .= ' AND `category_id` = ' . $db->Quote( $categoryIds[0] );
			}
			else
			{
				$query .= ' AND `category_id` IN (' . implode( ',', $categoryIds ) .')';
			}
		}

		$query	.= ' AND `answered` = ' . $db->Quote( '0' );

		if( $excludeFeatured )
		{
			$query .= ' AND `featured` = ' . $db->Quote( '0' );
		}

		if (!EDC::isSiteAdmin() && !EDC::isModerator()) {
			$query	.= ' AND `private`=' . $db->Quote(0);
		}

		$query .= ' AND `legacy` = ' . $db->Quote( '0' );

		$query .= $extraSQL;

		$db->setQuery( $query );
		$result = $db->loadResult();

		return empty( $result ) ? '0' : $result;

	}

	public function getNewCount( $filter = '' , $category = '' , $tagId = '', $featuredOnly = 'all' )
	{
		$db		= DiscussHelper::getDBO();
		$my	= JFactory::getUser();

		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= DiscussHelper::getPrivateCategories();
		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT COUNT(a.`id`) FROM `#__discuss_posts` AS a';

		if(! empty($tagId))
		{
			$query	.= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query	.= '	ON a.`id` = c.`post_id`';
			$query	.= '	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND a.`published`=' . $db->Quote('1');

		if( $featuredOnly === true )
		{
			$query	.= ' AND a.`featured`=' . $db->Quote('1');
		}
		else if( $featuredOnly === false)
		{
			$query	.= ' AND a.`featured`=' . $db->Quote('0');
		}

		$config	= DiscussHelper::getConfig();
		$query	.= ' AND DATEDIFF( ' . $db->Quote( DiscussHelper::getDate()->toMySQL() ) . ', a.`created`) <= ' . $db->Quote( $config->get( 'layout_daystostaynew' ) );

		if( $category )
		{
			$model	= DiscussHelper::getModel( 'Categories' );
			$childs	= $model->getChildIds( $category );
			$childs[]	 = $category;
			$query	.= ' AND a.`category_id` IN (' . implode( ',' , $childs ) . ')';
		}

		// $query	.= ' AND b.`id` IS NULL';
		$query	.= $queryExclude;


		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function getFeaturedCount( $filter = '' , $category = '' , $tagId = '' )
	{
		$db = DiscussHelper::getDBO();
		$my = JFactory::getUser();

		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= DiscussHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		$query	= 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';

		if(! empty($tagId))
		{
			$query	.= ' INNER JOIN `#__discuss_posts_tags` AS b ON a.`id` = b.`post_id`';
			$query	.= ' AND b.`tag_id` = ' . $db->Quote($tagId);
		}

		$query	.= ' WHERE a.`featured` = ' . $db->Quote('1');
		$query	.= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND a.`published` = ' . $db->Quote('1');
		if( $category )
		{
			$query	.= ' AND a.`category_id`=' . $db->Quote( $category );
		}
		$query	.=	$queryExclude;

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	public function getFeaturedPosts( $category = '' )
	{
		$db = DiscussHelper::getDBO();
		$my = JFactory::getUser();

		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= DiscussHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		$query	= 'SELECT a.* FROM `#__discuss_posts` AS a';

		if(! empty($tagId))
		{
			$query	.= ' INNER JOIN `#__discuss_posts_tags` AS b ON a.`id` = b.`post_id`';
			$query	.= ' AND b.`tag_id` = ' . $db->Quote($tagId);
		}

		$query	.= ' WHERE a.`featured` = ' . $db->Quote('1');
		$query	.= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND a.`published` = ' . $db->Quote('1');
		if( $category )
		{
			$query	.= ' AND a.`category_id`=' . $db->Quote( $category );
		}
		$query	.=	$queryExclude;

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Get unresolved posts from a specific user.
	 *
	 * @access	public
	 * @param	int	$userId		The specific user.
	 */
	public function getUnresolvedFromUser( $userId )
	{
		$db		= DiscussHelper::getDBO();
		$date	= DiscussHelper::getDate();

		$limitstart = $this->getState('limitstart');
		$limit 		= $this->getState('limit');

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', b.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', b.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', b.`created`) as `timediff`,';
		$query	.= ' b.`id`, b.`title`, b.`alias`, b.`created`, b.`modified`, b.`replied`, b.`legacy`,';
		$query	.= ' b.`content`, b.`category_id`, b.`published`, b.`ordering`, b.`vote`, b.`hits`, b.`islock`,';
		$query	.= ' b.`featured`, b.`isresolve`, b.`isreport`, b.`user_id`, b.`parent_id`,';
		$query	.= ' b.`user_type`, b.`poster_name`, b.`poster_email`, b.`num_likes`,';
		$query	.= ' b.`num_negvote`, b.`sum_totalvote`,b.`answered`,';
		$query	.= ' b.`post_status`, b.`post_type`, pt.`title` AS `post_type_title`,pt.`suffix` AS `post_type_suffix`,';
		$query	.= ' count(d.id) as `num_replies`,';
		$query	.= ' c.`title` as `category`, b.`password`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS b ';
		$query	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS d ';
		$query	.= ' ON d.' . $db->nameQuote( 'parent_id' ) . '=b.' . $db->nameQuote( 'id' );
		$query	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS c';
		$query	.= ' ON c.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'category_id' );
		$query 	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_post_types' ) . ' AS pt';
		$query 	.= ' ON b.`post_type` = pt.' . $db->nameQuote( 'alias' );
		$query	.= ' WHERE b.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
		$query	.= ' AND b.' . $db->nameQuote( 'isresolve' ) . '=' . $db->Quote( 0 );
		$query	.= ' AND b.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND b.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 );
		$query	.= ' GROUP BY b.' . $db->nameQuote( 'id' );

		$this->_total = $this->_getListCount($query);

		$this->_pagination	= DiscussHelper::getPagination( $this->_total, $limitstart, $limit );

		$this->_data		= $this->_getList($query, $limitstart , $limit);

		return $this->_data;
	}

	/**
	 * Retrieve replies from a specific user
	 **/
	public function getRepliesFromUser($userId, $ordering = '')
	{
		$db		= DiscussHelper::getDBO();
		$date	= DiscussHelper::getDate();

		$limitstart = $this->getState('limitstart');
		$limit 		= $this->getState('limit');

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', b.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', b.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', b.`created`) as `timediff`,';
		$query	.= ' b.`id`, b.`title`, b.`alias`, b.`created`, b.`modified`, b.`replied`,b.`legacy`,';
		$query	.= ' b.`content`, b.`category_id`, b.`published`, b.`ordering`, b.`vote`, a.`hits`, b.`islock`,';
		$query	.= ' b.`featured`, b.`isresolve`, b.`isreport`, b.`user_id`, b.`parent_id`,';
		$query	.= ' b.`user_type`, b.`poster_name`, b.`poster_email`, b.`num_likes`,';
		$query	.= ' b.`num_negvote`, b.`sum_totalvote`, b.`answered`,';
		$query	.= ' b.`post_status`, b.`post_type`, pt.`title` AS `post_type_title`,pt.`suffix` AS `post_type_suffix`,';
		$query	.= ' count(a.id) as `num_replies`,';
		$query	.= ' c.`title` as `category`, b.`password`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a ';
		$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS b ';
		$query	.= ' ON a.' . $db->nameQuote( 'parent_id' ) . ' = b.' . $db->nameQuote( 'id' );
		$query	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS c';
		$query	.= ' ON c.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'category_id' );
		$query 	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_post_types' ) . ' AS pt';
		$query 	.= ' ON b.`post_type` = pt.' . $db->nameQuote( 'alias' );
		$query	.= ' WHERE a.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
		$query	.= ' AND a.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 );

		$query	.= ' AND a.`parent_id` != ' . $db->Quote('0');
		$query	.= ' AND b.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 );
		$query	.= ' AND b.`parent_id` = ' . $db->Quote('0');

		$query	.= ' GROUP BY b.`id`';

		if (!empty($ordering)) {

			if ($ordering == 'latest') {
				$query	.= ' ORDER BY a.`created` DESC';
			}
		}

		$this->_total = $this->_getListCount($query);

		$this->_pagination	= DiscussHelper::getPagination( $this->_total, $limitstart, $limit );

		$this->_data		= $this->_getList($query, $limitstart , $limit);

		return $this->_data;
	}

	public function getUserReplies( $postId, $excludeLastReplyUser	= false )
	{
		$db = DiscussHelper::getDBO();

		$repliesUser	= '';
		$lastReply		= '';

		if( $excludeLastReplyUser )
		{
			$query	= 'SELECT `id`, `user_id`, `poster_name`, `poster_email` FROM `#__discuss_posts` where `published` = 1 and `parent_id` = ' . $db->Quote( $postId ) ;
			$query	.= ' ORDER BY `id` DESC LIMIT 1';

			$db->setQuery( $query );
			$lastReply	= $db->loadAssoc();
		}

		if( isset($lastReply['id']) )
		{
			$query	= 'SELECT DISTINCT `user_id`, `poster_email`, `poster_name` FROM `#__discuss_posts`';
			$query	.= ' WHERE `published` = ' . $db->Quote( '1' );
			$query	.= ' and `parent_id` = ' . $db->Quote( $postId );
			$query	.= ' and `id` != ' . $db->Quote( $lastReply['id'] );

			if( !empty( $lastReply['user_id']	) )
				$query	.= ' and `user_id` != ' . $db->Quote( $lastReply['user_id'] );

			if( !empty( $lastReply['poster_email']	) )
				$query	.= ' and `poster_email` != ' . $db->Quote( $lastReply['poster_email'] );

			$query	.= ' ORDER BY `id` DESC';
			$query	.= ' LIMIT 5';

			$db->setQuery( $query );

			$repliesUser	= $db->loadObjectList();
		}

		return $repliesUser;
	}

	public function getCategoryId( $postId )
	{
		$db 	= DiscussHelper::getDBO();

		$query		= array();
		$query[]	= 'SELECT ' . $db->nameQuote( 'category_id' );
		$query[]	= 'FROM ' . $db->nameQuote( '#__discuss_posts' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $postId );
		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	/**
	 * Retrieves a list of user id's that has participated in a discussion
	 *
	 * @access	public
	 * @param	int $postId		The main discussion id.
	 * @return	Array	An array of user id's.
	 *
	 **/
	public function getParticipants( $postId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT DISTINCT `user_id` FROM `#__discuss_posts`';
		$query	.= ' WHERE `parent_id` = ' . $db->Quote( $postId );

		$db->setQuery( $query );
		$participants		= $db->loadResultArray();

		return $participants;
	}

	public function hasAttachments( $postId , $type )
	{
		static $loaded 	= array();

		$index 	= $postId . $type;

		if( !isset( $loaded[ $index ] ) )
		{
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_attachments' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $postId ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
			$db->setQuery( $query );
			$result	= $db->loadResult();

			$loaded[ $index ]	= $result;
		}

		return $loaded[ $index ];
	}

	public function hasPolls( $postId )
	{
		static $cache	= array();

		if( !isset( $cache[ $postId ] ) )
		{
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT COUNT( DISTINCT(`post_id`) ) FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );
			$db->setQuery( $query );
			$result	= $db->loadResult();

			$cache[ $postId ]	= $result;
		}

		return $cache[ $postId ];
	}

	/**
	 * When merging posts, we need to update attachments type
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateAttachments( $postId , $type )
	{
		$db		= DiscussHelper::getDBO();

		$where 	= $type == 'questions' ? 'replies' : 'questions';

		$query 	= 'UPDATE ' . $db->nameQuote( '#__discuss_attachments' ) . ' SET ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
		$query 	.= ' WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $postId ) . ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $where );

		$db->setQuery( $query );

		$db->query();
	}

	/**
	 * Updates existing posts to a new parent.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateNewParent( $currentParent , $newParent )
	{
		$db 		= DiscussHelper::getDBO();

		$query		= array();

		$query[]	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' );
		$query[]	= 'SET ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $newParent );
		$query[]	= 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $currentParent );

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );
		$db->Query();
	}
}
