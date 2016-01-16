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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';

class DiscussPost extends JTable
{
	public $id				= null;
	public $title			= null;
	public $modified		= null;
	public $created			= null;
	public $replied			= null;
	public $alias			= null;
	public $content			= null;
	public $published		= null;
	public $ordering		= null;
	public $vote			= null;
	public $islock			= null;
	public $featured		= null;
	public $isresolve		= null;
	public $hits			= null;
	public $user_id			= null;
	public $category_id		= null;
	public $parent_id		= null;
	public $user_type		= null;
	public $poster_name		= null;
	public $poster_email	= null;
	public $num_likes		= null;
	public $num_negvote		= null;
	public $sum_totalvote	= null;
	public $params			= null;
	public $answered		= null;
	public $password		= null;
	public $legacy			= null;
	public $address			= null;
	public $latitude		= null;
	public $longitude		= null;
	public $content_type	= null;
	public $post_status		= null;
	public $post_type		= null;
	public $ip 				= null;
	public $private 		= null;

	private $_data			= array();

	static $_attachments    = array();
	static $_pollsQuestion  = array();
	static $_polls  		= array();
	static $_likes          = array();
	static $_commentTotal   = array();
	static $_comments   	= array();
	static $_voters   		= array();
	static $_likeAuthors   	= array();
	static $_loaded   		= array();
	static $_hasVoted   	= array();

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_posts' , 'id' , $db );
	}

	public function loadBatch( $ids )
	{
		$db = DiscussHelper::getDBO();

		if( count( $ids ) > 0 )
		{
			$query  = 'select * from ' . $this->_tbl;
			if( count($ids) == 1 )
			{
				$query  .= ' where id = ' . $db->Quote( $ids[0] );
			}
			else
			{
				$query  .= ' where id IN (' . implode(',', $ids) . ')';
			}

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			foreach( $result as $item )
			{

				$sig    = $item->id . (int) false;
				self::$_loaded[$sig] = $item;
			}

		}
	}

	public function load( $key = null , $alias = false )
	{
		// static $loaded = array();

		$alias  = ( $alias ) ? true : false;
		$sig    = $key . (int) $alias;

		if( ! isset( self::$_loaded[$sig] ) )
		{
			if( !$alias )
			{
				parent::load( $key );
				self::$_loaded[$sig]   = $this;
			}
			else
			{
				$db		= DiscussHelper::getDBO();

				if( strpos( $key, ':' ) === false )
				{
					$query	= 'SELECT id FROM ' . $this->_tbl . ' '
							. 'WHERE ' . $db->nameQuote('alias') . ' = ' . $db->Quote( $key );
				}
				else
				{
					// Try replacing ':' to '-' since Joomla replaces it
					$query	= 'SELECT id FROM ' . $this->_tbl . ' '
							. 'WHERE ' . $db->nameQuote('alias') . ' = ' . $db->Quote( JString::str_ireplace( ':' , '-' , $key ) );

				}

				$db->setQuery( $query );
				$id		= $db->loadResult();

				parent::load( $id );
				self::$_loaded[$sig]   = $this;
			}
		}

		return parent::bind(self::$_loaded[$sig]);
		//return $this->bind( $loaded[$sig] );
	}

	public function setPollQuestions( $obj )
	{
		self::$_pollsQuestion[ $this->id ] = $obj;
	}

	public function setPollQuestionsBatch( $ids = array() )
	{
		if( count( $ids ) > 0 )
		{
			$db = DiscussHelper::getDBO();

			$query  = 'select * from `#__discuss_polls_question`';
			if( count( $ids ) == 1 )
			{
				$query  .= ' where `post_id` = ' . $db->Quote( $ids[0] );
			}
			else
			{
				$gids   = implode( ',', $ids );
				$query  .= ' where `post_id` IN ( ' . $gids . ')';
			}

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item)
				{
					$poll 	= DiscussHelper::getTable( 'PollQuestion' );
					$poll->bind( $item );

					self::$_pollsQuestion[ $item->post_id ] = $poll;
				}//end foreach

			}//end if

			foreach( $ids as $id )
			{
				if(! isset( self::$_pollsQuestion[ $id ] ) )
				{
					self::$_pollsQuestion[ $id ] = false;
				}
			}

		}

	}

	public function setPolls( $obj )
	{
		$this->_data['polls'] = $obj;
	}

	public function setCustomFields( $obj )
	{
		$this->_data['customfields'] = $obj;
	}


	/**
	 * Must only be bind when using POST data
	 **/
	public function bind( $data , $post = false )
	{
		parent::bind( $data );

		if( $post )
		{
			$my = JFactory::getUser();

			if ( $this->id == 0 )
			{
				// This is to check if superadmin assign blog author during blog creation.
				if(empty($this->user_id))
				{
					$this->user_id	= $my->id;
				}
			}

			// Default joomla date obj
			$date				= DiscussHelper::getDate();
			$now				= $date->toMySQL();
			$config				= DiscussHelper::getConfig();
			$allowedTags		= explode( ',' , $config->get( 'main_allowed_tags' ) );
			$allowedAttributes	= explode( ',' , $config->get( 'main_allowed_attr' ) );
			$input				= JFilterInput::getInstance( $allowedTags , $allowedAttributes );

			$data[ 'title' ]	= isset( $data[ 'title' ] ) ? $data[ 'title' ] : '';
			$this->title		= $input->clean( $data[ 'title'] );
			$this->content		= isset( $data[ 'dc_reply_content' ] ) ? $data[ 'dc_reply_content' ] : '';
			$this->created		= !empty( $this->created ) && $this->created != '0000-00-00 00:00:00' ? $this->created : $now;
			$this->replied		= !empty( $this->replied ) ? $this->replied : $now;
			$this->modified		= $now;

			// Default values to 0
			$this->num_likes		= $this->num_likes ? $this->num_likes : 0;
			$this->num_negvote		= $this->num_negvote ? $this->num_negvote : 0;
			$this->sum_totalvote	= $this->sum_totalvote ? $this->sum_totalvote : 0;
		}

		return true;
	}


	/**
	 * Method to update parent total replies count and last reply time.
	 */
	public function addParentRepliesCount($parentId, $val)
	{
		if(empty($parentId))
		{
			return false;
		}

		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE `#__discuss_posts` SET `num_replies` = `num_replies` + ' . $db->Quote($val);

		if($val > 0)
		{
			$query .= ', `replied` = ' . $db->Quote($this->created);
		}

		$query .= ' WHERE `id` = ' . $db->Quote($parentId);
		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function setHasVotedBatch( $ids, $userId = null)
	{
		$db     = DiscussHelper::getDBO();
		$user	= JFactory::getUser( $userId );

		if( count( $ids ) > 0 )
		{
			$query  = 'SELECT `value`, `post_id` FROM `#__discuss_votes`';
			$query  .= ' WHERE `user_id` = ' . $db->Quote($user->id);
			if( count( $ids ) == 1 )
			{
				$query  .= ' AND ' . $db->nameQuote('post_id') . ' = ' . $db->Quote( $ids[0] );
			}
			else
			{
				$query  .= ' AND ' . $db->nameQuote('post_id') . ' IN ( ' . implode( ',', $ids ) . ')';
			}

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					$sig = $user->id . '-' . $item->post_id;
					self::$_hasVoted[$sig] = $item->value;
				}
			}

			foreach( $ids as $id )
			{
				$sig = $user->id . '-' . $id;
				if(! isset( self::$_hasVoted[$sig] ) )
				{
					self::$_hasVoted[$sig]  = '';
				}
			}

		}

	}

	public function hasVoted( $userId = null )
	{
		$user	= JFactory::getUser( $userId );
		$db		= DiscussHelper::getDBO();

		$sig = $user->id . '-' . $this->id;

		if( isset( self::$_hasVoted[$sig] ) )
		{
			return self::$_hasVoted[$sig];
		}

		$query  = 'SELECT `value` FROM `#__discuss_votes` WHERE `user_id` = ' . $db->Quote($user->id) . ' AND ' . $db->nameQuote('post_id') . ' = ' . $db->Quote( $this->id );

		$db->setQuery($query);
		$result	= $db->loadResult();

		self::$_hasVoted[$sig] = $result;
		return $result;
	}

	/**
	 * Override parent's behavior as we need to assign badge history when a post is being read.
	 *
	 **/
	public function hit( $pk = null )
	{
		$ip	= JRequest::getVar( 'REMOTE_ADDR' , '' , 'SERVER' );
		$my	= JFactory::getUser();

		if( !empty( $ip ) && !empty($this->id) )
		{
			$token		= md5( $ip . $this->id );

			$session	= JFactory::getSession();
			$exists		= $session->get( $token , false );

			if( $exists )
			{
				return true;
			}

			$session->set( $token , 1 );
		}

		$state	= parent::hit();

		if( $this->published == DISCUSS_ID_PUBLISHED && $my->id != $this->user_id )
		{
			// @task: Assign badge
			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.read.discussion' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_READ_POST' , $this->title ) );

			// EasySocial integrations
			DiscussHelper::getHelper( 'EasySocial' )->assignBadge( 'read.question' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_READ_POST' , $this->title ) );

			// AUP Integrations
			DiscussHelper::getHelper( 'Aup' )->assign(DISCUSS_POINTS_VIEW_DISCUSSION , $my->id , $this->title );
		}

		return $state;
	}

	public function getComments( $limit = null, $limitstart = null )
	{
		if( isset( self::$_comments[ $this->id ] ) )
		{
			return self::$_comments[ $this->id ];
		}

		$postModel 		= DiscussHelper::getModel( 'Posts' );
		self::$_comments[ $this->id ] = $postModel->getComments( $this->id, $limit, $limitstart );

		return self::$_comments[ $this->id ];
	}

	public function setCommentsBatch( $ids, $limit = null, $limitstart = null )
	{
		if( count( $ids ) > 0 )
		{
			$postModel 		= DiscussHelper::getModel( 'Posts' );

			$comments		= $postModel->getComments( $ids );

			if( count( $comments ) > 0 )
			{
				foreach( $comments as $item )
				{
					self::$_comments[ $item->post_id ][] = $item;
				}
			}

			foreach( $ids as $id )
			{
				if(! isset( self::$_comments[ $id ] ) )
				{
					self::$_comments[ $id ] = array();
				}
				else
				{
					if( $limit !== null )
					{
						self::$_comments[ $id ] = array_slice( self::$_comments[ $id ], 0, $limit);
					}
				}
			}
		}
	}



	/**
	 * Retrieves the total number of replies for this particular discussion.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	int
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function getTotalComments()
	{
		if( isset( self::$_commentTotal[ $this->id ] ) )
			return self::$_commentTotal[ $this->id ];

		// Get the post model.
		$postModel 							= DiscussHelper::getModel( 'Posts' );
		self::$_commentTotal[ $this->id ] 	= $postModel->getTotalComments( $this->id );


		return self::$_commentTotal[ $this->id ];
	}


	/**
	 * Set the total number of replies for this particular discussion batch
	 *
	 * @since	3.0
	 * @access	public
	 * @return	int
	 */
	public function setTotalCommentsBatch( $ids )
	{

		if( count( $ids ) > 0 )
		{
			$db = DiscussHelper::getDBO();

			$query	= 'SELECT COUNT(1) as `CNT`, `post_id` FROM `#__discuss_comments`';

			if( count( $ids ) == 1 )
			{
				$query	.= ' WHERE `post_id` = ' . $db->quote( $ids[0] );
			}
			else
			{
				$query	.= ' WHERE `post_id` IN (' .  implode(',', $ids ) . ')';
			}

			$query	.= ' GROUP BY `post_id`';

			$db->setQuery( $query );
			$results    = $db->loadObjectList();

			if( count( $results ) > 0 )
			{
				$items  = array();

				foreach( $results as $item )
				{
					self::$_commentTotal [$item->post_id ] = $item->CNT;
				}
			}

			foreach( $ids as $id )
			{
				if(! isset( self::$_commentTotal [ $id ] ) )
				{
					self::$_commentTotal [ $id ] = '0';
				}
			}
		}

	}


	/**
	 * Retrieves the total number of replies for this particular discussion.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	int
	 */
	public static function getTotalReplies()
	{
		// Get the post model.
		$postModel 		= DiscussHelper::getModel( 'Posts' );

		return $postModel->getTotalReplies( $this->id );
	}

	public function getTotalVotes()
	{
		if( !isset($this->_data['totalvotes']) )
		{
			$db 	= DiscussHelper::getDBO();
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_votes' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
			$db->setQuery( $query );
			$this->_data['totalvotes'] = $db->loadResult();
		}

		return $this->_data['totalvotes'];
	}


	public function setVoterBatch($ids, $limit='5')
	{
		if( count( $ids ) > 0 )
		{

			$db 	= DiscussHelper::getDBO();
			$query	= 'SELECT a.`user_id`, b.`name`, b.`username`, c.`nickname`, a.`post_id` ';
			$query	.= ' FROM ' . $db->nameQuote('#__discuss_votes') . ' as a ';
			$query	.= ' INNER JOIN ' . $db->nameQuote('#__users') . ' as b on a.`user_id` = b.`id` ';
			$query	.= ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' as c on a.`user_id` = c.`id` ';
			if( count( $ids ) == 1 )
			{
				$query	.= ' WHERE a.`post_id` = ' . $db->Quote($ids[0]);
			}
			else
			{

				$query	.= ' WHERE a.`post_id` IN (' . implode( ',', $ids ) . ')';
			}
			$query	.= ' LIMIT 0, ' . $limit;

			$db->setQuery($query);

			$result = $db->loadObjectList();

			foreach($result as $item)
			{
				self::$_voters[$item->post_id][] = $item;
			}

			foreach( $ids as $id)
			{
				if( ! isset( self::$_voters[$id] ) )
				{
					self::$_voters[$id] = array();
				}
			}

		}
	}


	public function getVoters($postid, $limit='5')
	{
		if( isset( self::$_voters[$postid] ) )
		{
			return self::$_voters[$postid];
		}

		$db 	= DiscussHelper::getDBO();
		$query	= 'SELECT a.`user_id`, b.`name`, b.`username`, c.`nickname` '
				. ' FROM ' . $db->nameQuote('#__discuss_votes') . ' as a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' as b on a.`user_id` = b.`id` '
				. ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' as c on a.`user_id` = c.`id` '
				. ' WHERE a.`post_id` = ' . $db->Quote($postid) . ' '
				. ' ORDER BY a.`created` DESC'
				. ' LIMIT 0, ' . $limit;

		$db->setQuery($query);
		self::$_voters[$postid] = $db->loadObjectList();

		return self::$_voters[$postid];
	}

	public function getTags()
	{
		if( !isset($this->_data['tags']) )
		{
			$db		= DiscussHelper::getDBO();
			if( !class_exists( 'EasyDiscussModelPostsTags' ) )
			{
				JLoader::import( 'poststags' , DISCUSS_MODELS );
			}
			$model	= DiscussHelper::getModel( 'PostsTags' );
			$this->_data['tags'] = $model->getPostTags( $this->id );
		}

		return $this->_data['tags'];
	}

	public function getId()
	{
		return $this->id;
	}

	public function getTitle()
	{
		if( !isset($this->_data['title']) )
		{
			$this->_data['title'] = DiscussHelper::wordFilter( $this->title );
		}

		// Need escape
		return $this->_data['title'];
	}

	public function getContent()
	{
		if( !isset($this->_data['content']) )
		{
			$this->_data['content'] = DiscussHelper::wordFilter( $this->content );
		}
		return $this->_data['content'];
	}

	/**
	 * Returns the @DiscussPostAccess object.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getAccess( DiscussCategory $category )
	{
		require_once DISCUSS_CLASSES . '/postaccess.php';

		$access 	= new DiscussPostAccess( $this , $category );

		return $access;
	}

	public function setAttachmentsData( $type, $ids = array() )
	{
		if( count( $ids ) > 0 )
		{
			$db = DiscussHelper::getDBO();

			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_attachments' );
			if( count( $ids ) == 1 )
			{
				$query	.= ' WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $ids[0] );
			}
			else
			{
				$uids    = implode(',', $ids);
				$query	.= ' WHERE ' . $db->nameQuote( 'uid' ) . ' IN (' . $uids . ')';
			}

			$query	.= ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
			$query	.= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item)
				{
					$table	= JTable::getInstance( 'Attachments' , 'Discuss' );
					$table->bind( $item );

					$type = explode("/", $item->mime);
					$table->attachmentType = $type[0];

					self::$_attachments[ $item->uid ][] = $table;
				}//end foreach

				foreach( $ids as $id )
				{
					if(! isset( self::$_attachments[ $id ] ) )
					{
						self::$_attachments[ $id ] = array();
					}
				}

			}//end if
		}
	}


	public function getAttachments()
	{
		if( empty( $this->id ) )
			return false;


		if(! isset( self::$_attachments[ $this->id ] ) )
		{
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_attachments' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $this->getType() ) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
			$db->setQuery( $query );

			$result	= $db->loadObjectList();

			$attachments = false;

			if( $result )
			{
				$attachments	= array();
				foreach( $result as $row )
				{
					$table	= JTable::getInstance( 'Attachments' , 'Discuss' );
					$table->bind( $row );

					$type = explode("/", $row->mime);
					$table->attachmentType = $type[0];

					$attachments[]	= $table;
				}
			}

			self::$_attachments[ $this->id ] = $attachments;

		}

		return self::$_attachments[ $this->id ];
	}

	/**
	 * Binds poll items for this post.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	boolean		Determines if this post is a new post or not.
	 * @param	Array		An array of poll values.
	 */
	public function bindPolls( $isNew = true , $polls = array() , $removePolls = array() , $multiplePolls = false , $pollQuestion = '', $pollsOri = array() )
	{
		$config 		= DiscussHelper::getConfig();

		// Do not process any poll items if its not enabled.
		if( !$config->get( 'main_polls' ) && $this->isQuestion() )
		{
			return false;
		}

		// If there's no polls passed in, we shouldn't be doing anything either.
		if( !$polls )
		{
			$this->setError( 'No poll values provided' );
			return false;
		}

		// Create a new poll question for this post.
		$table 	= DiscussHelper::getTable( 'PollQuestion' );
		$table->loadByPost( $this->id );

		// Set the poll items
		$table->post_id		= $this->id;
		$table->title 		= $pollQuestion;
		$table->multiple	= $config->get( 'main_polls_multiple' ) ? $multiplePolls : false;

		// Store the main poll question.
		$table->store();

		// If there's a list of items to be removed, process it here.
		if( !$isNew && $removePolls )
		{
			$remove	= explode( ',' , $removePolls );

			foreach( $remove as $removePollId )
			{
				$removePollId 	= (int) $removePollId;
				$poll			= DiscussHelper::getTable( 'Poll' );
				$poll->load( $removePollId );
				$poll->delete();
			}
		}

		for( $i = 0; $i < count($polls); $i++ )
		{
			$item    = $polls[$i];
			$itemOri = isset( $pollsOri[$i] ) ? $pollsOri[$i] : '';


			$value		= (string) $item;
			$valueOri 	= (string) $itemOri;

			if( trim( $value ) == '' )
			{
				continue;
			}

			$poll	= DiscussHelper::getTable( 'Poll' );

			if( empty( $valueOri ) && !empty( $value ) )
			{
				// this is a new item.
				//echo 'ha';
				$poll->set( 'value' 		, $value );
				$poll->set( 'post_id'		, $this->id );
				$poll->store();
			}
			else if( !empty( $valueOri ) && !empty( $value )  )
			{
				// update existing value.
				//echo 'la';
				$poll->loadByValue( $valueOri , $this->id );
				$poll->set( 'value' 		, $value );
				$poll->store();
			}
		}

		return true;
	}

	/**
	 * Binds specific custom fields.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	Array	An array of fields values.
	 */
	public function bindCustomFields( $fields, $fieldId )
	{
		if( empty($fields) || empty($fieldId) )
		{
			return false;
		}

		//If the first record is not empty is because empty text and textarea will have empty array
		if( !empty($fields[0]) )
		{
			$fieldTable = DiscussHelper::getTable( 'CustomFieldsValue' );
			$fieldTable->field_id 	= $fieldId;
			$fieldTable->value 		= serialize( $fields );
			$fieldTable->post_id	= $this->id;

			return $fieldTable->store();
		}
	}

	/*
	 * Binds specific parameters which can be used by the caller.
	 */
	public function bindParams( $post )
	{
		$params = DiscussHelper::getRegistry( '' );

		foreach( $post as $key => $value )
		{
			if( preg_match( '/params\_.*/i' , $key ) )
			{
				if( is_array( $value ) )
				{
					$total	= count( $value );
					$key	= str_ireplace( '[]' , '' , $key );

					for( $i = 0;$i < $total;$i++ )
					{
						if( !empty( $value[ $i ] ) )
						{
							// Strip off all html tags from the input since we don't want to allow them to embed html codes in the fields.
							$value[$i]	= strip_tags( $value[ $i ] );

							$params->set( $key . $i , $value[ $i ] );
						}
					}
				}
				else
				{
					$params->set( $key , $value );
				}
			}
		}

		$this->params = $params->toString( 'INI' );
	}

	public function bindAttachments()
	{
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		// @task: Do not allow file attachments if its disabled.
		if( !$config->get( 'attachment_questions' ) )
		{
			return false;
		}

		$allowed	= explode( ',' , $config->get( 'main_attachment_extension' ) );
		$files		= JRequest::getVar( 'filedata' , array() , 'FILES');

		if( empty( $files ) )
		{
			return false;
		}

		$total	= count( $files[ 'name' ] );


		// @rule: Handle empty files.
		if( empty( $files['name'][0] ) )
		{
			$total  = 0;
		}

		if( $total < 1 )
		{
			return false;
		}


		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.utilities.utility' );

		// @rule: Create default media path
		$path   = DISCUSS_MEDIA . '/' . trim( $config->get( 'attachment_path' ) , DIRECTORY_SEPARATOR );

		if( !JFolder::exists( $path ) )
		{
			JFolder::create( $path );
			JFile::copy( DISCUSS_ROOT . '/index.html' , $path . '/index.html' );
		}

		$maxSize	= (double) $config->get( 'attachment_maxsize' ) * 1024 * 1024;

		for( $i = 0; $i < $total; $i++ )
		{
			$extension  = JFile::getExt( $files[ 'name' ][ $i ] );

			// Skip empty data's.
			if( !$extension )
			{
				continue;
			}

			// @rule: Check for allowed extensions
			if( !isset( $extension ) || !in_array( strtolower($extension) , $allowed ) )
			{
				$mainframe->enqueueMessage( JText::sprintf('COM_EASYDISCUSS_FILE_ATTACHMENTS_INVALID_EXTENSION', $files[ 'name' ][ $i ]) , 'error' );
					$this->setError( JText::sprintf('COM_EASYDISCUSS_FILE_ATTACHMENTS_INVALID_EXTENSION', $files[ 'name' ][ $i ]) );
				return false;
			}
			else
			{
				$size   = $files[ 'size' ][ $i ];

				// @rule: File size should not exceed maximum allowed size
				if( !empty( $size ) && ($size < $maxSize || $maxSize == 0 ) )
				{
					$name			= DiscussHelper::getHash( $files[ 'name' ][ $i ] . DiscussHelper::getDate()->toMySQL() );
					$attachment = DiscussHelper::getTable('Attachments');

					$attachment->set( 'path'		, $name );
					$attachment->set( 'title'		, $files[ 'name' ][ $i ] );
					$attachment->set( 'uid' 		, $this->id );
					$attachment->set( 'type'		, $this->getType() );
					$attachment->set( 'created'		, DiscussHelper::getDate()->toMySQL() );
					$attachment->set( 'published'	, true );
					$attachment->set( 'mime'		, $files[ 'type' ][ $i ] );
					$attachment->set( 'size'		, $size );

					JFile::copy( $files[ 'tmp_name' ][ $i ] , $path . '/' . $name );
					$attachment->store();

					// Create a thumbnail if attachment is an image
					if( DiscussHelper::getHelper( 'Image' )->isImage($files['name'][$i]) )
					{
						require_once DISCUSS_CLASSES . '/simpleimage.php';
						$image	= new SimpleImage;

						$image->load($files['tmp_name'][$i]);
						$image->resizeToFill( 160 , 120 );
						$image->save($path . '/' . $name . '_thumb', $image->image_type);
					}
				}
				else
				{
					$mainframe->enqueueMessage( JText::sprintf('COM_EASYDISCUSS_FILE_ATTACHMENTS_MAX_SIZE_EXCLUDED', $files[ 'name' ][ $i ], $config->get( 'attachment_maxsize' ) ) , 'error' );
					$this->setError( JText::sprintf('COM_EASYDISCUSS_FILE_ATTACHMENTS_MAX_SIZE_EXCLUDED', $files[ 'name' ][ $i ], $config->get( 'attachment_maxsize' ) ) );
					return false;
				}
			}
		}

		return true;
	}

	/*
	 * Returns the permalink of the current post data.
	 */
	public function getPermalink( $external = false )
	{
		if( !isset($this->_data['permalink']) )
		{
			$this->_data['permalink'] = DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $this->id );
		}
		return $this->_data['permalink'];
	}

	/**
	 * Given a user id, determine if the user has liked this discussion or reply.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The user's id.
	 */
	public function isLiked( $userId )
	{
		return $this->isLikedBy( $userId );
	}

	/**
	 * Gets the owner of the discussion or reply.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getOwner()
	{
		static $owners 	= null;

		if( !isset( $owners[ $this->user_id ] ) )
		{
			$owner	= new stdClass();

			// Initialize default values first.
			$owner->id		= 0;
			$owner->name	= JText::_( 'COM_EASYDISCUSS_GUEST' );
			$owner->link	= 'javascript:void(0)';

			// @TODO: Fill this with guest avatar.
			$owner->avatar		= DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default.png';
			$owner->guest		= true;
			$owner->signature	= '';
			$user	= DiscussHelper::getTable( 'Profile' );

			if( $this->user_id > 0 )
			{
				$user->load( $this->user_id );

				$owner->id			= $this->user_id;
				$owner->name		= $user->getName();
				$owner->link		= $user->getLink();
				$owner->avatar		= $user->getAvatar();
				$owner->guest		= false;
				$owner->signature	= $user->getSignature( 'true' );
			}

			$owner->role		= $user->id > 0 ? $user->getRole() : '';
			$owner->roleid		= $user->id > 0 ? $user->getRoleId() : '';
			$owner->rolelabel	= $user->id > 0 ? $user->getRoleLabelClassname() : '';

			$owners[ $this->user_id ] = $owner;
		}

		return $owners[ $this->user_id ];

	}

	/*
	 * Determines whether the current post is pending or not.
	 *
	 * @params  null
	 * @return  boolean     True if pending false otherwise.
	 */
	public function isPending()
	{
		return $this->published == DISCUSS_ID_PENDING;
	}

	public function getParams( $key )
	{
		if( !isset($this->_data['params']) )
		{
			$result		= array();
			$pattern	= '/params_' . $key . '[0-9]=(.*)/i';
			preg_match_all( $pattern , $this->params , $matches );

			if( !empty( $matches[1] ) )
			{
				foreach( $matches[1] as $match )
				{
					$result[] = $match;
				}
			}

			$this->_data['params'] = $result;
		}

		return $this->_data['params'];
	}

	public function getReferences()
	{
		if( !isset($this->_data['references']) )
		{
			$references	= array();
			$pattern	= '/params_references[0-9]=(.*)/i';
			preg_match_all( $pattern , $this->params , $matches );

			if( !empty( $matches[1] ) )
			{
				foreach( $matches[1] as $reference )
				{
					$reference		= JString::str_ireplace('"', '', $reference);
					$reference		= JString::stristr( $reference , 'http' ) === false ? 'http://' . $reference : $reference;
					$references[]	= $reference;
				}
			}
			$this->_data['references'] = $references;
		}

		return $this->_data['references'];
	}

	public function clearAccpetedReply()
	{
		$db		= DiscussHelper::getDBO();

		$query  = 'UPDATE `#__discuss_posts` set `answered` = ' . $db->Quote( '0' );
		$query  .= ' WHERE `parent_id` = ' . $db->Quote( $this->id );

		$db->setQuery( $query );
		$db->query();
	}

	/*
	 * Returns the type of this post
	 *
	 * @param   null
	 * @return  string  questions
	 */
	public function getType()
	{
		if( $this->parent_id )
		{
			return DISCUSS_REPLY_TYPE;
		}

		return DISCUSS_QUESTION_TYPE;
	}

	public function isQuestion()
	{
		return $this->getType() == DISCUSS_QUESTION_TYPE;
	}

	public function isReply()
	{
		return $this->getType() == DISCUSS_REPLY_TYPE;
	}

	public function removePoints()
	{
		if( $this->getType() == DISCUSS_REPLY_TYPE && $this->user_id )
		{
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.remove.reply' , $this->user_id );
		}

	}

	public function delete( $pk = null )
	{

		// @rule: Unlink from 3rd party integrations
		$this->removeStream();

		// @rule: Unlink from the references table.
		$this->removeReferences();

		// Process point removals if necessary.
		$this->removePoints();

		$this->removeSubscription();

		$this->suppressNotifications();

		// @rule: Delete attachments associated with this post.
		$attachments	= $this->getAttachments();

		if( !empty( $attachments ) )
		{
			$total = count( $attachments );

			for( $i = 0 ; $i < $total; $i++ )
			{
				$attachments[ $i ]->delete();
			}
		}

		// @rule: Delete any childs
		if( !$this->parent_id )
		{
			$deletedIds = $this->deleteChilds();
		}

		// Remove polls when discussion is deleted
		$this->removePoll();

		// @rule: Delete any tags associated with this post.
		$this->deleteTags();

		// Delete comments related to this post.
		$this->deleteComments();

		// Delete any custom fields value with this post
		$this->deleteCustomFieldsValue( $this->id );

		// Delete all favourites that associate with this post
		$this->deleteAllFavourites();

		JPluginHelper::importPlugin('finder');
        $dispatcher = JDispatcher::getInstance();

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array('com_easydiscuss.post', $this));

		$state 	= parent::delete($pk);

		return $state;
	}

	public function deleteCustomFieldsValue( $id = null )
	{
		if( !$id )
		{
			return false;
		}

		$ruleModel = DiscussHelper::getModel( 'CustomFields' );
		$state = $ruleModel->deleteCustomFieldsValue( $id, 'post' );

		return $state;
	}

	public function deleteAllFavourites()
	{
		if( !$this->id )
		{
			return false;
		}

		$favModel = DiscussHelper::getModel( 'Favourites' );
		$state = $favModel->deleteAllFavourites( $this->id );

		return $state;
	}

	public function suppressNotifications()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_notifications' ) . ' SET ' . $db->nameQuote( 'state' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATION_READ )
				. ' WHERE ' . $db->nameQuote( 'cid' ) . ' = ' . $db->quote( $this->id )
				. ' AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->quote( 'com_easydiscuss' )
				. ' AND ('
				. '		' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_REPLY )
				. '		OR ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_RESOLVED )
				. '		OR ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_ACCEPTED )
				. '		OR ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_FEATURED )
				. '		OR ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_COMMENT )
				. '		OR ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_LOCKED )
				. '		OR ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_UNLOCKED )
				. '		OR ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION )
				. '		OR ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( DISCUSS_NOTIFICATIONS_LIKES_REPLIES )
				. ')';
		$db->setQuery( $query );

		$db->query();

		return true;
	}

	public function removeSubscription()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_subscription' )
				. ' WHERE ' . $db->nameQuote( 'cid' ) . ' = ' . $db->quote( $this->id )
				. ' AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( 'post' );
		$db->setQuery( $query );
		$db->query();

		return true;
	}


	/**
	 * Remove references from the reference table for this particular post.
	 **/
	public function removeReferences()
	{
		$db 	= DiscussHelper::getDBO();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_posts_references' );
		$query	.= ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}

	/**
	 * When executed, remove any 3rd party integration records.
	 */
	public function removeStream()
	{
		jimport( 'joomla.filesystem.file' );

		$config 	= DiscussHelper::getConfig();

		// @rule: Detect if jomsocial exists.
		$file 		= JPATH_ROOT . '/components/com_community/libraries/core.php';

		if( JFile::exists( $file ) && $config->get( 'integration_jomsocial_activity_new_question' ) && !$this->parent_id )
		{
			// @rule: Test if record exists first.
			$db 	= DiscussHelper::getDBO();
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__community_activities' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'app' ) . '=' . $db->Quote( 'com_easydiscuss' ) . ' '
					. 'AND ' . $db->nameQuote( 'cid' ) . '=' . $db->Quote( $this->id );

			$db->setQuery( $query );
			$exists	= $db->loadResult();

			if( $exists )
			{
				$query	= 'DELETE FROM ' . $db->nameQuote( '#__community_activities' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'app' ) . '=' . $db->Quote( 'com_easydiscuss' ) . ' '
						. 'AND ' . $db->nameQuote( 'cid' ) . '=' . $db->Quote( $this->id );

				$db->setQuery( $query );
				$db->Query();
			}
		}

		// Remove Easysocial Stream
		DiscussHelper::getHelper('EasySocial')->deleteDiscussStream($this);
	}

	/**
	 * Removes all comments related to this post.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function deleteComments( $id = null )
	{
		$db 	= DiscussHelper::getDBO();

		if( is_null( $id ) )
		{
			$id 	= $this->id;
		}

		$query 	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_comments' )
				. ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );

		$db->Query();
	}

	public function deleteChilds()
	{
		if( !$this->id )
		{
			return false;
		}

		$db		= DiscussHelper::getDBO();

		// To get the delete replies id
		$query = 'SELECT ' . $db->nameQuote( 'id' )
				. ' FROM ' . $db->nameQuote( '#__discuss_posts' )
				. ' WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$results = $db->loadResultArray();

		if( $results )
		{
			foreach( $results as $id )
			{
				$reply 	= DiscussHelper::getTable( 'Post' );
				$reply->load( $id );

				$reply->delete();
			}
		}
		// $query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
		// 		. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $this->id );
		// $db->setQuery( $query );
		// $db->Query();

		return $results;
	}

	public function deleteTags()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_posts_tags' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$db->Query();

		return true;
	}

	/**
	 * Get the post class css suffix
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostTypeSuffix()
	{
		$model 	= DiscussHelper::getModel( 'Post_Types' );
		$suffix	= $model->getSuffix( $this->post_type );

		return $suffix;
	}

	/**
	 * Get the post class css suffix
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostType()
	{
		$model 	= DiscussHelper::getModel( 'Post_Types' );
		$title 	= $model->getTitle( $this->post_type );

		return $title;
	}

	/*
	 * Retrieve the post creator's avatar
	 */
	public function getPosterAvatar()
	{
		if( !isset($this->_data['posteravatar']) )
		{
			$user = JTable::getInstance( 'Profile' , 'Discuss' );
			$user->load( $this->user_id );
			$this->_data['posteravatar'] = $user->getAvatar();
		}

		$this->_data['posteravatar'];$user->getAvatar();
	}

	public function store( $updateNulls = false )
	{
		$date   = DiscussHelper::getDate();
		$this->modified			= $date->toMySQL();

		// @since 3.0
		$this->legacy			= '0';


		if( $this->published == 1 && !empty($this->parent_id) )
		{
			$this->updateParentLastRepliedDate();
		}

		return parent::store();
	}

	/**
	 * Resets all the votes for this particular discussion / reply.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function resetVotes()
	{
		$db 	= DiscussHelper::getDBO();

		$query 	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_votes' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );

		$db->setQuery( $query );
		$db->Query();

		// Once the vote items are removed, we need to update the sum_totalvote column.
		$this->sum_totalvote	= 0;

		return $this->store();
	}

	public function updateParentLastRepliedDate()
	{
		$db = DiscussHelper::getDBO();

		if( !empty($this->parent_id) )
		{
			$query  = 'UPDATE `#__discuss_posts` SET `replied` = ' . $db->Quote( $this->created );
			$query  .= ' WHERE `id` = ' . $db->Quote( $this->parent_id );

			$db->setQuery( $query );
			$db->query();
		}

		return true;
	}

	/**
	 * Tests if the user has already voted for this discussion's poll before.
	 *
	 * @access	public
	 * @param	int $userId		The user id to check for.
	 * @return	boolean			True if voted, false otherwise.
	 */
	public function hasVotedPoll( $userId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' '
			. 'AND ' . $db->nameQuote( 'poll_id' ) . ' IN('
			. 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id )
			. ')';
		$db->setQuery( $query );
		$voted	= $db->loadResult();

		return $voted > 0;
	}

	/**
	 * Returns the poll question
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getPollQuestion()
	{
		if( !isset( self::$_pollsQuestion[ $this->id ] ) )
		{
			$poll 	= DiscussHelper::getTable( 'PollQuestion' );

			if( $this->id )
			{
				$poll->loadByPost( $this->id );
			}

			self::$_pollsQuestion[ $this->id ] = $poll;
		}

		return self::$_pollsQuestion[ $this->id ];
	}

	/**
	 * Return a list of polls for this discussion
	 *
	 **/
	public function getPolls()
	{
		if( isset( self::$_polls[ $this->id ] ) )
		{
			return self::$_polls[ $this->id ];
		}


		if( !isset($this->_data['polls']) )
		{

			$my = JFactory::getUser();
			$session = JFactory::getSession();
			$session->set( 'userid', $my->id );

			$polls	= array();

			if( empty( $this->id ) )
			{
				$this->_data['polls'] = $polls;
			}
			else
			{
				$db		= DiscussHelper::getDBO();
				$query	= 'SELECT a.*, count(b.`user_id`) as `meVoted`,';
				$query	.= ' (select sum(`count`) from `#__discuss_polls` where `post_id`='. $db->Quote( $this->id ) . ') as `totalVoted`';
				$query	.= ' FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' AS a';
				$query  .= ' left join `#__discuss_polls_users` as b on a.`id` = b.`poll_id` and b.`user_id` = ' . $db->Quote( $my->id );
				if($my->id == 0)
				{
					$query .= ' AND b.session_id =' . $db->Quote( $session->getId() );
				}
				$query	.= ' WHERE a.' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
				$query  .= ' GROUP BY a.' . $db->nameQuote( 'id' );
				$query	.= ' ORDER BY a.' . $db->nameQuote( 'id' ) . ' ASC';

				$db->setQuery( $query );

				if( $items = $db->loadObjectList() )
				{
					foreach( $items as $item )
					{
						$poll	= DiscussHelper::getTable( 'Poll' );
						$poll->bind( $item );

						$poll->meVoted  	= $item->meVoted;
						$poll->totalVoted  	= $item->totalVoted;

						$polls[]	= $poll;
					}
				}

				$this->_data['polls'] = $polls;
			}
		}

		return $this->_data['polls'];
	}


	public function setPollsBatch( $ids )
	{
		if( count( $ids ) > 0 )
		{
			$my = JFactory::getUser();
			$session = JFactory::getSession();
			$session->set( 'userid', $my->id );

			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT a.*, count(b.`user_id`) as `meVoted`,';
			$query	.= ' sum( c.`count` ) as `totalVoted`';
			$query	.= ' FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' AS a';
			$query  .= ' left join `#__discuss_polls_users` as b on a.`id` = b.`poll_id` and b.`user_id` = ' . $db->Quote( $my->id );
			if($my->id == 0)
			{
				$query .= ' AND b.session_id =' . $db->Quote( $session->getId() );
			}
    		$query  .= ' left join #__discuss_polls as c on a.post_id = c.post_id';

			if( count( $ids ) == 1 )
			{
				$query	.= ' WHERE a.' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $ids[0] );
			}
			else
			{
				$query	.= ' WHERE a.' . $db->nameQuote( 'post_id' ) . ' IN (' . implode( ',', $ids ) . ')';
			}
			$query  .= ' GROUP BY a.' . $db->nameQuote( 'id' );

			$db->setQuery( $query );

			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					$poll	= DiscussHelper::getTable( 'Poll' );
					$poll->bind( $item );

					$poll->meVoted  	= $item->meVoted;
					$poll->totalVoted  	= $item->totalVoted;

					self::$_polls[ $item->post_id ][] = $poll;
				}
			}

			foreach( $ids as $id )
			{
				if( ! isset( self::$_polls[ $id ] ) )
				{
					self::$_polls[ $id ]    = array();
				}
			}

		}
	}


	/**
	 * Delete polls that are related to this post.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function removePoll()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );

		$db->setQuery( $query );
		$rows	= $db->loadObjectList();

		if( !$rows )
		{
			return false;
		}

		if( $rows )
		{

			foreach( $rows as $row )
			{
				$poll	= DiscussHelper::getTable( 'Poll' );

				$poll->bind( $row );

				$poll->delete();
			}
		}

		// Remove any poll question if necessary.
		$pollQuestion 	= DiscussHelper::getTable( 'PollQuestion' );

		if( $pollQuestion->loadByPost( $this->id ) )
		{
			$pollQuestion->delete();
		}

		return true;
	}

	public function removePollVote( $userId )
	{
		$polls 	= $this->getPolls();

		foreach( $polls as $poll )
		{
			$poll->removeExistingVote( $userId , $this->id );
		}
		$this->updatePollsCount();
	}

	/**
	 * Recalculates all votes for the particular vote items.
	 */
	public function updatePollsCount()
	{
		$db		= DiscussHelper::getDBO();

		$polls	= $this->getPolls();

		foreach( $polls as $poll )
		{

			// Unset the meVoted and totalVoted
			unset( $poll->meVoted );
			unset( $poll->totalVoted );

			$poll->updateCount();
		}
	}

	/**
	 * Retrieve total number of replies for this particular discussion
	 *
	 **/
	public function getReplyCount()
	{
		if( !isset($this->_data['replycount']) )
		{
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl ) . ' '
					. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $this->id );
			$db->setQuery( $query );
			$this->_data['replycount']	= $db->loadResult();
		}

		return $this->_data['replycount'];
	}

	public function getReplies( $limit = 10 , $limitstart = 0 )
	{
		if( !isset($this->_data['replies']) )
		{
			$replies	= array();
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT *, count(b.id) as `total_vote_cnt` FROM ' . $db->nameQuote( $this->_tbl ) . ' '
					. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_votes' ) . ' AS `b` '
					. 'ON a.' . $db->nameQuote( 'id' ) . '=b.' . $db->nameQuote( 'post_id' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
					. 'LIMIT ' . $limitstart . ',' . $limit;
			$db->setQuery( $query );
			$result	= $db->loadObjectList();

			if( $result	= $db->loadObjectList() )
			{
				foreach( $result as $res )
				{
					$post	= DiscussHelper::getTable( 'Post' );
					$post->bind( $res );

					$replies[]	= $post;
				}
			}

			$this->_data['replies'] = $replies;
		}

		return $this->_data['replies'];
	}

	public function isFeatured()
	{
		return (bool) $this->featured;
	}

	public function trimEmail( $content )
	{
		$config	= DiscussHelper::getConfig();

		if( $config->get( 'layout_editor' ) != 'bbcode' )
		{
			// Remove img tags
			$content 	= strip_tags( $content , '<p><div><table><tr><td><thead><tbody><br><br />' );

			return $content;
		}

		if( $config->get('main_notification_max_length') > '0' )
		{
			$content = $this->truncateContentByLength( $content, '0', $config->get('main_notification_max_length') );
		}

		// Remove video codes from the e-mail since it will not appear on e-mails
		$content 	= DiscussHelper::getHelper( 'Videos' )->strip( $content );

		return $content;
	}

	public function truncateContentByLength( $content, $start, $length )
	{
		// By default $start = 0 means start counting from the beginning of the given string until the given $length
		$append		= '...';
		$content	= substr( $content, $start, $length );
		$content	= $content . $append;

		return $content;
	}

	public function getMyCustomFields( $postId = null, $aclId = null )
	{
		$fieldModel = DiscussHelper::getModel( 'CustomFields' );
		$fields = $fieldModel->getMyFields( $postId, $aclId );

		return $fields;
	}

	public function mapCustomFieldsSession( $dbFields )
	{
		if( isset( 	$this->_data['customfields'] ) && count( $this->_data['customfields'] ) > 0 )
		{
			for( $i = 0; $i < count( $dbFields ); $i++ )
			{
				$row =& $dbFields[ $i ];

				foreach( $this->_data['customfields'] as $key => $val )
				{
					if( isset( $val[ $row->id ] ) && !empty( $val[ $row->id ] ) )
					{
						$values = '';
						if( $row->type == 'text' || $row->type == 'area' )
						{
							$values = array( $val[ $row->id ][0] );
						}
						else
						{
							$values = $val[ $row->id ];
						}

						$row->value 	= serialize( $values );
						break;
					}
				}
			}
		}

		return $dbFields;
	}

	/**
	 * Retrieves the html code for the like authors.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getLikeAuthors()
	{
		static $loaded = null;

		if( is_null( $loaded ) )
		{
			$loaded		= DiscussHelper::getHelper( 'Likes' )->getLikesHTML( $this->id );
		}

		return $loaded;
	}


	public function getLikeAuthorsObject( $id )
	{
		if( isset( self::$_likeAuthors[$id] ) )
		{
			return self::$_likeAuthors[$id];
		}

		return null;
	}

	public function setLikeAuthorsBatch( $ids )
	{
		$db 	= DiscussHelper::getDBO();
		$config = DiscussHelper::getConfig();

		if( count( $ids ) > 0 )
		{

			$displayFormat	= $config->get('layout_nameformat');
			$displayName	= '';

			switch($displayFormat){
				case "name" :
					$displayName = 'a.name';
					break;
				case "username" :
					$displayName = 'a.username';
					break;

				case "nickname" :
				default :
					$displayName = 'IF(c.`nickname` != \'\', c.`nickname`, a.`name`)';
					break;
			}

			$query	= 'SELECT a.id as `user_id`, b.id, b.`content_id`, ' . $displayName . ' AS `displayname`';
			$query	.= ' FROM ' . $db->nameQuote( '#__discuss_likes' ) . ' AS b';
			$query	.= ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS a';
			$query	.= '    on b.created_by = a.id';
			$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_users' ) . ' AS c';
			$query	.= '    on b.created_by = c.id';
			$query	.= ' WHERE b.`type` = '. $db->Quote('post');

			if( count( $ids ) == 1 )
			{
				$query	.= ' AND b.`content_id` = ' . $db->Quote($ids[0]);
			}
			else
			{
				$query	.= ' AND b.`content_id` in ( ' . implode( ',', $ids )  . ')';
			}

			$db->setQuery($query);

			$result  = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					self::$_likeAuthors[ $item->content_id ][] = $item;
				}
			}

			foreach( $ids as $id )
			{
				if(! isset( self::$_likeAuthors[ $id ] ) )
				{
					self::$_likeAuthors[ $id ] = array();
				}
			}
		}
	}

	public function setLikedByBatch( $ids, $userId )
	{
		$db = DiscussHelper::getDBO();

		if( count( $ids ) > 0 )
		{
			$query	= 'SELECT `id`, `content_id` FROM `#__discuss_likes`';
			$query	.= ' WHERE `type` = ' . $db->Quote( 'post' );
			if( count( $ids ) == 1 )
			{
				$query	.= ' AND `content_id` = ' . $db->Quote($ids[0]);
			}
			else
			{
				$cids   = implode( ',', $ids );
				$query	.= ' AND `content_id` IN (' . $cids . ')';
			}
			$query	.= ' AND `created_by` = ' . $db->Quote($userId);

			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count( $result ) > 0 )
			{
				foreach( $result as $item )
				{
					$key    = $item->content_id . $userId;
					self::$_likes[ $key ]   = $item->id;
				}
			}

			foreach( $ids as $id )
			{
				$key    = $id . $userId;
				if( ! isset( self::$_likes[ $key ] ) )
				{
					self::$_likes[ $key ]   = '';
				}
			}

		}
	}

	/**
	 * Retrieves the html code for the like authors.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The user's id.
	 */
	public function isLikedBy( $userId )
	{
		if( empty( $userId ) )
		{
			return false;
		}

		$key 	= $this->id . $userId;

		if( !isset( self::$_likes[ $key ] ) )
		{
			$model 		= DiscussHelper::getModel( 'Likes' );
			self::$_likes[ $key ]		= $model->isLike( 'post' , $this->id , $userId );
		}

		return self::$_likes[ $key ];
	}

	/**
	 * Determines if the discussion is password protected.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	boolean	True if the post is protected.
	 */
	public function isProtected()
	{
		$config = DiscussHelper::getConfig();

		if( $config->get( 'main_password_protection' ) && !empty( $this->password ) )
		{
			// Detect if user set any values in the session.
			$session 	= JFactory::getSession();
			$password 	= $session->get( 'DISCUSSPASSWORD_' . $this->id , '' , 'com_easydiscuss' );

			if( $this->password == $password )
			{
				return false;
			}

			return true;
		}

		return false;
	}

	public function isFavBy( $userId )
	{
		static $loaded = null;

		if( !isset( $loaded ) )
		{
			$model = DiscussHelper::getModel( 'Favourites' );

			// Check to see is it favourited?
			$loaded = $model->isFav( $this->id, $userId );
		}

		return $loaded;
	}

	public function getMyFavCount()
	{
		$model = DiscussHelper::getModel( 'Favourites' );
		$result = $model->getFavouritesCount( $this->id );

		return $result;
	}

	/**
	 * Sends a ping request to pingomatic servers.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 * @return	bool	True if success, false otherwise.
	 *
	 */
	public function ping()
	{
		if( $this->published != DISCUSS_ID_PUBLISHED )
		{
			return false;
		}

		$config 	= DiscussHelper::getConfig();
		if( !$config->get( 'integration_pingomatic' ) )
		{
			return false;
		}

		$pingomatic 	= DiscussHelper::getHelper( 'Pingomatic' );
		return $pingomatic->ping( $this->title, DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $this->id , true , true ) );
	}

	/**
	 * Sends an auto post request to social networks such as Facebook, Twitter etc.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 * @return	boolean	True if success, false otherwise.
	 */
	public function autopost()
	{
		// Only allow post that are really published.
		if( !$this->published )
		{
			return false;
		}

		$category = DiscussHelper::getTable( 'Category' );
		$category->load( $this->category_id );

		// Only allow post that are posted in a public category.
		if(! $category->canPublicAccess() )
		{
			return false;
		}

		$config 	= DiscussHelper::getConfig();

		// Set generic callback URL.
		$callback	= DiscussRouter::getRoutedUrl( 'index.php?option=com_easydiscuss&view=post&id=' . $this->id , false , true );

		// These are the default social sites which we need to ping.
		$sites		= array( 'facebook' , 'twitter' );

		foreach( $sites as $site )
		{
			if( $config->get( 'main_autopost_' . $site ) )
			{
				$oauth	= DiscussHelper::getTable( 'OAuth' );
				$state 	= $oauth->loadByType( $site );

				// Determine if this discussion is already shared on the social site.
				$oauthPost 	= DiscussHelper::getTable( 'OauthPosts' );
				$shared 	= $oauthPost->exists( $this->id , $oauth->id );

				if( !$shared && $state && !empty( $oauth->access_token) )
				{
					$consumer	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( $site , $config->get( 'main_autopost_' . $site . '_id' ) , $config->get( 'main_autopost_' . $site . '_secret' ) , $callback );

					// Set access token for the social site.
					$consumer->setAccess( $oauth->access_token );

					// Try to share the post to the site.
					$status 	= $consumer->share( $this );

					// @TODO: Add error logging when something fail here.

					// When the psot is shared we need to keep a record of this to prevent from sending duplicate updates.
					$oauthPost->post_id		= $this->id;
					$oauthPost->oauth_id	= $oauth->id;
					$oauthPost->store();
				}
			}
		}
	}

	/**
	 * Use the post assignment table to return the latest assignee
	 */
	public function getAssigneeId()
	{
		$asssignment	= DiscussHelper::getTable( 'PostAssignment' );
		$asssignment->load( $this->id );

		return $asssignment->assignee_id;
	}

	public function getLabel()
	{
		$postlabel	= DiscussHelper::getTable( 'PostLabel' );
		$postlabel->load($this->id);

		$label	= DiscussHelper::getTable( 'Label' );
		$label->load($postlabel->post_label_id);

		$this->label = $label;

		return $this->label;
	}

	public function getAssignment()
	{
		$assignment = DiscussHelper::getTable( 'PostAssignment' );
		$assignment->load( $this->id );
		$this->assignment = $assignment;

		$assignee = DiscussHelper::getTable( 'Profile' )->load( $assignment->assignee_id );
		$this->assignee = $assignee;

		return $this->assignment;
	}

	public function moveChilds( $parentId = null, $newCatId = null )
	{
		if( empty($newCatId) || empty($parentId) )
		{
			return false;
		}

		$db		= DiscussHelper::getDBO();

		$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' )
				. ' SET ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $newCatId )
				. ' WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $parentId );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}

	public function isPollLocked()
	{
		$db = DiscussHelper::getDBO();

		$query = 'SELECT ' . $db->nameQuote( 'locked' )
				. ' FROM ' . $db->nameQuote( '#__discuss_polls_question' )
				. ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );

		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}

	public function lockPolls()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_polls_question' )
				. ' SET ' . $db->nameQuote( 'locked' ) . '=' . $db->Quote( 1 )
				. ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$state = $db->Query();

		return $state;
	}
	public function unlockPolls()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_polls_question' )
				. ' SET ' . $db->nameQuote( 'locked' ) . '=' . $db->Quote( 0 )
				. ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$state = $db->Query();

		return $state;
	}

	/**
	 * Retrieves the status class of this post
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 */
	public function getStatusClass()
	{

		if( $this->post_status == 1 )
		{
			return '-on-hold';
		}

		if( $this->post_status == 2 )
		{
			return '-accepted';
		}

		if( $this->post_status == 3 )
		{
			return '-working-on';
		}

		if( $this->post_status == 4 )
		{
			return '-reject';
		}

		return;
	}

	/**
	 * Retrieves the status message of the post.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 */
	public function getStatusMessage()
	{
		if( $this->post_status == 1 )
		{
			return JText::_( 'COM_EASYDISCUSS_POST_STATUS_ON_HOLD' );
		}

		if( $this->post_status == 2 )
		{
			return JText::_( 'COM_EASYDISCUSS_POST_STATUS_ACCEPTED' );
		}

		if( $this->post_status == 3 )
		{
			return JText::_( 'COM_EASYDISCUSS_POST_STATUS_WORKING_ON' );
		}

		if( $this->post_status == 4 )
		{
			return JText::_( 'COM_EASYDISCUSS_POST_STATUS_REJECT' );
		}

		return;
	}

	/**
	 * Triggers the content plugin
	 *
	 * @since	3.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function triggerReply()
	{
		$config		= DiscussHelper::getConfig();

		if( !$config->get( 'main_content_trigger_replies' ) )
		{
			return;
		}

		// process content plugins
		DiscussEventsHelper::importPlugin( 'content' );
		DiscussEventsHelper::onContentPrepare('reply', $postTable);

		$event 	= new stdClass();

		$args 	= array( &$this );

		$results						= DiscussEventsHelper::onContentBeforeDisplay( 'reply' , $args );
		$event->beforeDisplayContent 	= trim(implode("\n", $results));

		$results						= DiscussEventsHelper::onContentAfterDisplay('reply', $args );
		$event->afterDisplayContent		= trim(implode("\n", $results));
		$this->event 	= $event;
	}
}
