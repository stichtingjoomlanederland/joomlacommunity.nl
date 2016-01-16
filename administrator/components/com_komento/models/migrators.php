<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class KomentoModelAdminMigrators extends KomentoParentModel
{
	public $component;
	public $cid;
	public $publishingState;
	public $_db;

	function __construct()
	{
		$this->_db = Komento::getDBO();
	}

	function getSupportedComponents()
	{
		static $supportedComponents = array();

		if( empty( $supportedComponents ) )
		{
			$components = array_values( Komento::getHelper( 'components' )->getAvailableComponents() );

			foreach( $components as $component )
			{
				$supportedComponents[] = $this->_db->quote( $component );
			}
		}

		return $supportedComponents;
	}

	function getMigrator( $type )
	{
		$type = strtolower( $type );
		$class = 'KomentoModelMigrator' . $type;

		return class_exists($class) ? new $class() : false;
	}

	function getKomentoInsertNode( $date )
	{
		$query  = 'SELECT * FROM `#__komento_comments`';
		$query .= ' WHERE `component` = ' . $this->_db->quote( $this->component );
		$query .= ' AND `cid` = ' . $this->_db->quote( $this->cid );
		$query .= ' AND `created` > ' . $this->_db->quote( $date );
		$query .= ' AND `parent_id` = ' . $this->_db->quote( 0 );
		$query .= ' ORDER BY `created` LIMIT 1';

		$this->_db->setQuery( $query );

		return $this->_db->loadObject();
	}

	function pushKomentoComment( $base, $diff )
	{
		$query  = 'UPDATE `#__komento_comments`';
		$query .= ' SET `lft` = `lft` + ' . $diff . ', `rgt` = `rgt` + ' . $diff;
		$query .= ' WHERE `component` = ' . $this->_db->quote( $this->component );
		$query .= ' AND `cid` = ' . $this->_db->quote( $this->cid );
		$query .= ' AND `lft` >= ' . $base;

		$this->_db->setQuery( $query );
		return $this->_db->query();
	}

	function populateChildBoundaries( $child, $parent_id )
	{
		$commentsModel = Komento::getModel( 'comments' );

		$latest = $commentsModel->getLatestComment( $this->component, $this->cid, $parent_id );

		$parent = Komento::getTable( 'comments' );
		$parent->load( $parent_id );

		//adding new child comment
		$lft = $parent->lft + 1;
		$rgt = $parent->lft + 2;
		$node = $parent->lft;

		if( !empty( $latest ) )
		{
			$lft = $latest->rgt + 1;
			$rgt = $latest->rgt + 2;
			$node = $latest->rgt;
		}

		$commentsModel->updateCommentSibling( $this->component, $this->cid, $node );

		$child->lft = $lft;
		$child->rgt = $rgt;

		return $child;
	}
}

class KomentoModelMigratorRSComments extends KomentoModelAdminMigrators
{
	function save( $comment )
	{
		$new = Komento::getTable( 'comments' );

		$new->component		= $this->component;
		$new->cid			= $this->cid;
		$new->comment		= $comment->comment;
		$new->name			= $comment->name;
		$new->email			= $comment->email;
		$new->url			= $comment->website;
		// careful because rscomments save their datetime in unix format
		$new->created		= Komento::getDate( $comment->date )->toMySql();
		$new->created_by	= $comment->uid;
		$new->published		= $comment->published;
		$new->ip			= $comment->ip;
		$new->parent_id		= $comment->parent_id;
		$new->lft			= $comment->lft;
		$new->rgt			= $comment->rgt;

		if( $this->publishingState !== 'inherit' )
		{
			$new->published		= $this->publishingState;
		}

		if( !$new->store() )
		{
			return false;
		}

		return $new;
	}

	function load( $id )
	{
		$query = 'SELECT * FROM `#__rscomments_comments` WHERE `IdComment` = ' . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function getUniqueComponents()
	{
		$where = ' WHERE `option` IN (' . implode( ',', $this->getSupportedComponents() ) . ')';

		$query = 'SELECT DISTINCT `option` FROM `#__rscomments_comments`' . $where . ' ORDER BY `option`';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getUniquePostId( $options = array() )
	{
		$defaultOptions	= array(
			'option'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT DISTINCT `id` FROM `#__rscomments_comments`';

		$queryWhere = array();

		if( $options['option'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'option' ) . ' = ' . $this->_db->quote( $options['option']);
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY `option`, `id`';

		$this->_db->setQuery( $query );

		return $this->_db->loadResultArray();
	}

	function getComments( $options = array() )
	{
		$defaultOptions	= array(
			'option'	=> 'all',
			'id'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT * FROM `#__rscomments_comments`';

		$queryWhere = array();

		if( $options['option'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'option' ) . ' = ' . $this->_db->quote( $options['option'] );
		}

		if( $options['id'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'id' ) . ' = ' . $this->_db->quote( $options['id'] );
		}

		if( count( $queryWhere) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY date';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCommentCount( $options = array() )
	{
		$defaultOptions	= array(
			'option'	=> 'all',
			'id'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT COUNT(1) FROM `#__rscomments_comments`';

		$queryWhere = array();

		if( $options['option'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'option' ) . ' = ' . $this->_db->quote( $options['option'] );
		}

		if( $options['id'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'id' ) . ' = ' . $this->_db->quote( $options['id'] );
		}

		if( count( $queryWhere) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

class KomentoModelMigratorSliComments extends KomentoModelAdminMigrators
{
	function save( $comment )
	{
		$new = Komento::getTable( 'comments' );

		$new->component		= $this->component;
		$new->cid			= $this->cid;
		$new->comment		= $comment->raw;
		$new->name			= $comment->name;
		$new->email			= $comment->email;
		$new->created		= $comment->created;
		$new->created_by	= $comment->user_id;
		$new->published		= $comment->status;
		$new->parent_id		= $comment->parent_id;
		$new->lft			= $comment->lft;
		$new->rgt			= $comment->rgt;

		if( $this->publishingState !== 'inherit' )
		{
			$new->published		= $this->publishingState;
		}

		if( !$new->store() )
		{
			return false;
		}

		return $new;
	}

	function load( $id )
	{
		$query = 'SELECT * FROM `#__slicomments` WHERE `id` = ' . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function getCategoryPosts( $categories = array() )
	{
		$categories = implode( ',', (array) $categories );
		$query = 'SELECT `id` FROM `#__content` WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getUniquePostId( $options = array() )
	{
		$query = 'SELECT DISTINCT `article_id` FROM `#__content`';

		$defaultOptions	= array(
			'categories'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['article_id'] = (array) $this->getCategoryPosts( $options['categories'] );
			$queryWhere[] = $this->_db->namequote( 'article_id' )  . ' IN (' . implode( ',', $options['article_id'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY `article_id`';

		$this->_db->setQuery( $query );

		return $this->_db->loadResultArray();
	}

	function getComments( $options = array() )
	{
		$query  = 'SELECT * FROM `#__slicomments`';

		$defaultOptions	= array(
			'categories'	=> 'all',
			'article_id'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['article_id'] = $this->getCategoryPosts( $options['categories'] );
		}

		if( $options['article_id'] !== 'all' )
		{
			$options['article_id'] = (array) $options['article_id'];
			$queryWhere[] = $this->_db->namequote( 'article_id' )  . ' IN (' . implode( ',', $options['article_id'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY created';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCommentCount( $options = array() )
	{
		$query = 'SELECT COUNT(1) FROM `#__slicomments`';

		$defaultOptions	= array(
			'categories'	=> 'all',
			'article_id'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['article_id'] = $this->getCategoryPosts( $options['categories'] );
		}

		if( $options['article_id'] !== 'all' )
		{
			$options['article_id'] = (array) $options['article_id'];
			$queryWhere[] = $this->_db->namequote( 'article_id' )  . ' IN (' . implode( ',', $options['article_id'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$this->_db->setQuery( $query );

		return $this->_db->loadResult();
	}
}

class KomentoModelMigratorK2 extends KomentoModelAdminMigrators
{
	// K2 comments does not have likes/votes nor parent-child relationship

	function save( $comment )
	{
		$new = Komento::getTable( 'comments' );

		$new->component		= $this->component;
		$new->cid			= $this->cid;
		$new->comment		= $comment->commentText;
		$new->name			= $comment->userName;
		$new->email			= $comment->commentEmail;
		$new->url			= $comment->commentURL;
		$new->created		= $comment->commentDate;
		$new->created_by	= $comment->userID;
		$new->published		= $comment->published;
		$new->parent_id		= $comment->parent_id;
		$new->lft			= $comment->lft;
		$new->rgt			= $comment->rgt;

		if( $this->publishingState !== 'inherit' )
		{
			$new->published		= $this->publishingState;
		}

		if( !$new->store() )
		{
			return false;
		}

		return $new;
	}

	function load( $id )
	{
		$query = 'SELECT * FROM `#__k2_comments` WHERE `id` = ' . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function getCategoryPosts( $categories = array() )
	{
		$categories = implode( ',', (array) $categories );
		$query = 'SELECT `id` FROM `#__k2_items` WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getUniquePostId( $options = array() )
	{
		$query = 'SELECT DISTINCT `itemID` FROM `#__k2_comments`';

		$defaultOptions	= array(
			'categories'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['itemID'] = (array) $this->getCategoryPosts( $options['categories'] );
			$queryWhere[] = $this->_db->namequote( 'itemID' )  . ' IN (' . implode( ',', $options['itemID'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY `itemID`';

		$this->_db->setQuery( $query );

		return $this->_db->loadResultArray();
	}

	function getComments( $options = array() )
	{
		$query  = 'SELECT * FROM `#__k2_comments`';

		$defaultOptions	= array(
			'categories'	=> 'all',
			'itemID'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['itemID'] = $this->getCategoryPosts( $options['categories'] );
		}

		if( $options['itemID'] !== 'all' )
		{
			$options['itemID'] = (array) $options['itemID'];
			$queryWhere[] = $this->_db->namequote( 'itemID' )  . ' IN (' . implode( ',', $options['itemID'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY commentDate';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCommentCount( $options = array() )
	{
		$query = 'SELECT COUNT(1) FROM `#__k2_comments`';

		$defaultOptions	= array(
			'categories'	=> 'all',
			'itemID'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['itemID'] = $this->getCategoryPosts( $options['categories'] );
		}

		if( $options['itemID'] !== 'all' )
		{
			$options['itemID'] = (array) $options['itemID'];
			$queryWhere[] = $this->_db->namequote( 'itemID' )  . ' IN (' . implode( ',', $options['itemID'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$this->_db->setQuery( $query );

		return $this->_db->loadResult();
	}
}

class KomentoModelMigratorZoo extends KomentoModelAdminMigrators
{
	// Zoo comments does not have likes/votes

	function save( $comment )
	{
		$new = Komento::getTable( 'comments' );

		$new->component		= $this->component;
		$new->cid			= $this->cid;
		$new->comment		= $comment->content;
		$new->name			= $comment->author;
		$new->email			= $comment->email;
		$new->url			= $comment->url;
		$new->created		= $comment->created;
		$new->created_by	= $comment->user_id;
		$new->published		= $comment->state;
		$new->ip			= $comment->ip;
		$new->parent_id		= $comment->parent_id;
		$new->depth			= $comment->depth;
		$new->lft			= $comment->lft;
		$new->rgt			= $comment->rgt;

		if( $this->publishingState !== 'inherit' )
		{
			$new->published		= $this->publishingState;
		}

		if( !$new->store() )
		{
			return false;
		}

		return $new;
	}

	function load( $id )
	{
		$query = 'SELECT * FROM `#__zoo_comment` WHERE `id` = ' . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function getCategoryPosts( $categories = array() )
	{
		$categories = implode( ',', (array) $categories );
		$query = 'SELECT DISTINCT `item_id` FROM `#__zoo_category_item` WHERE `category_id` IN (' . $categories . ') ORDER BY `item_id`';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getUniquePostId( $options = array() )
	{
		$query = 'SELECT DISTINCT `item_id` FROM `#__zoo_comment`';

		$defaultOptions	= array(
			'categories'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['item_id'] = (array) $this->getCategoryPosts( $options['categories'] );
			$queryWhere[] = $this->_db->namequote( 'item_id' )  . ' IN (' . implode( ',', $options['item_id'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY `item_id`';

		$this->_db->setQuery( $query );

		return $this->_db->loadResultArray();
	}

	function getComments( $options = array() )
	{
		$query  = 'SELECT * FROM `#__zoo_comment`';

		$defaultOptions	= array(
			'categories'	=> 'all',
			'item_id'		=> 'all',
			'parent_id'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['item_id'] = $this->getCategoryPosts( $options['categories'] );
		}

		if( $options['item_id'] !== 'all' )
		{
			$options['item_id'] = (array) $options['item_id'];
			$queryWhere[] = $this->_db->namequote( 'item_id' )  . ' IN (' . implode( ',', $options['item_id'] ) . ')';
		}

		if( $options['parent_id'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'parent_id' ) . ' = ' . $this->_db->quote( $options['parent_id'] );
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY created';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCommentCount( $options = array() )
	{
		$query = 'SELECT COUNT(1) FROM `#__zoo_comment`';

		$defaultOptions	= array(
			'categories'	=> 'all',
			'item_id'		=> 'all',
			'parent_id'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['item_id'] = $this->getCategoryPosts( $options['categories'] );
		}

		if( $options['item_id'] !== 'all' )
		{
			$options['item_id'] = (array) $options['item_id'];
			$queryWhere[] = $this->_db->namequote( 'item_id' )  . ' IN (' . implode( ',', $options['item_id'] ) . ')';
		}

		if( $options['parent_id'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'parent_id' ) . ' = ' . $this->_db->quote( $options['parent_id'] );
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$this->_db->setQuery( $query );

		return $this->_db->loadResult();
	}
}

class KomentoModelMigratorCJComment extends KomentoModelAdminMigrators
{
	// CJComments stores votes without tracking user voted up or down
	// The votes are stored directly into comments table and log only tracks that user have already voted

	function save( $comment )
	{
		$new = Komento::getTable( 'comments' );

		$new->component		= $comment->component;
		$new->cid			= $comment->contentid;
		$new->comment		= $comment->comment;
		$new->name			= $comment->name;
		$new->email			= $comment->email;
		$new->url			= $comment->website;
		$new->created		= $comment->date;
		$new->created_by	= $comment->userid;
		$new->published		= $comment->published;
		$new->ip			= $comment->ip;
		$new->parent_id		= $comment->parentid;
		$new->depth			= $comment->depth;
		$new->lft			= $comment->lft;
		$new->rgt			= $comment->rgt;

		if( $this->publishingState !== 'inherit' )
		{
			$new->published		= $this->publishingState;
		}

		if( !$new->store() )
		{
			return false;
		}

		return $new;
	}

	function load( $id )
	{
		$query = 'SELECT * FROM `#__comment` WHERE `id` = ' . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function getUniqueComponents()
	{
		$where = ' WHERE `component` IN (' . implode( ',', $this->getSupportedComponents() ) . ')';

		$query = 'SELECT DISTINCT `component` FROM `#__comment`' . $where . ' ORDER BY `component`';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getUniquePostId( $options = array() )
	{
		$defaultOptions	= array(
			'component'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT DISTINCT `contentid` FROM `#__comment`';

		$queryWhere = array();

		if( $options['component'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'component' ) . ' = ' . $this->_db->quote( $options['component']);
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY `component`';

		$this->_db->setQuery( $query );

		return $this->_db->loadResultArray();
	}

	function getComments( $options = array() )
	{
		$defaultOptions	= array(
			'component'	=> 'all',
			'contentid'	=> 'all',
			'parentid'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT * FROM `#__comment`';

		$queryWhere = array();

		if( $options['component'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'component' ) . ' = ' . $this->_db->quote( $options['component'] );
		}

		if( $options['contentid'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'contentid' ) . ' = ' . $this->_db->quote( $options['contentid'] );
		}

		if( $options['parentid'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'parentid' ) . ' = ' . $this->_db->quote( $options['parentid'] );
		}

		if( count( $queryWhere) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY date';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCommentCount( $options = array() )
	{
		$defaultOptions	= array(
			'component'	=> 'all',
			'contentid'	=> 'all',
			'parentid'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT COUNT(1) FROM `#__comment`';

		$queryWhere = array();

		if( $options['component'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'component' ) . ' = ' . $this->_db->quote( $options['component'] );
		}

		if( $options['contentid'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'contentid' ) . ' = ' . $this->_db->quote( $options['contentid'] );
		}

		if( $options['parentid'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'parentid' ) . ' = ' . $this->_db->quote( $options['parentid'] );
		}

		if( count( $queryWhere) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

class KomentoModelMigratorJAComment extends KomentoModelAdminMigrators
{
	// JA Comments stores votes without tracking user voted up or down
	// The votes are stored directly into comments table and log only tracks that user have already voted

	function save( $comment )
	{
		$new = Komento::getTable( 'comments' );

		$new->component		= $comment->option;
		$new->cid			= $comment->contentid;
		$new->comment		= $comment->comment;
		$new->name			= $comment->name;
		$new->email			= $comment->email;
		$new->url			= $comment->website;
		$new->created		= $comment->date;
		$new->created_by	= $comment->userid;
		$new->published		= $comment->published;
		$new->ip			= $comment->ip;
		$new->parent_id		= $comment->parentid;
		$new->depth			= $comment->depth;
		$new->lft			= $comment->lft;
		$new->rgt			= $comment->rgt;

		if( $this->publishingState !== 'inherit' )
		{
			$new->published		= $this->publishingState;
		}

		if( !$new->store() )
		{
			return false;
		}

		return $new;
	}

	function load( $id )
	{
		$query = 'SELECT * FROM `#__jacomment_items` WHERE `id` = ' . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function getUniqueComponents()
	{
		$where = ' WHERE `option` IN (' . implode( ',', $this->getSupportedComponents() ) . ')';

		$query = 'SELECT DISTINCT `option` FROM `#__jacomment_items`' . $where . ' ORDER BY `option`';
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getUniquePostId( $options = array() )
	{
		$defaultOptions	= array(
			'option'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT DISTINCT `contentid` FROM `#__jacomment_items`';

		$queryWhere = array();

		if( $options['option'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'option' ) . ' = ' . $this->_db->quote( $options['option']);
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY `option`';

		$this->_db->setQuery( $query );

		return $this->_db->loadResultArray();
	}

	function getComments( $options = array() )
	{
		$defaultOptions	= array(
			'option'	=> 'all',
			'contentid'	=> 'all',
			'parentid'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT * FROM `#__jacomment_items`';

		$queryWhere = array();

		if( $options['option'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'option' ) . ' = ' . $this->_db->quote( $options['option'] );
		}

		if( $options['contentid'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'contentid' ) . ' = ' . $this->_db->quote( $options['contentid'] );
		}

		if( $options['parentid'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'parentid' ) . ' = ' . $this->_db->quote( $options['parentid'] );
		}

		if( count( $queryWhere) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY date';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCommentCount( $options = array() )
	{
		$defaultOptions	= array(
			'option'	=> 'all',
			'contentid'	=> 'all',
			'parentid'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT COUNT(1) FROM `#__jacomment_items`';

		$queryWhere = array();

		if( $options['option'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'option' ) . ' = ' . $this->_db->quote( $options['option'] );
		}

		if( $options['contentid'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'contentid' ) . ' = ' . $this->_db->quote( $options['contentid'] );
		}

		if( $options['parentid'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'parentid' ) . ' = ' . $this->_db->quote( $options['parentid'] );
		}

		if( count( $queryWhere) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

class KomentoModelMigratorJComments extends KomentoModelAdminMigrators
{
	function save( $comment )
	{
		// extra processing before save
		// replace [quote *] to [quote]
		$pattern = '/\[quote\ .*?\]/';
		$replacement = '[quote]';
		$subject = $comment->comment;
		$comment->comment = preg_replace( $pattern, $replacement, $subject );

		$new = Komento::getTable( 'comments' );

		$new->component		= $comment->object_group;
		$new->cid			= $comment->object_id;
		$new->comment		= $comment->comment;
		$new->name			= $comment->name;
		$new->email			= $comment->email;
		$new->url			= $comment->homepage;
		$new->created		= $comment->date;
		$new->created_by	= $comment->userid;
		$new->published		= $comment->published;
		$new->ip			= $comment->ip;
		$new->parent_id		= $comment->parent;
		$new->depth			= $comment->depth;
		$new->lft			= $comment->lft;
		$new->rgt			= $comment->rgt;

		if( $this->publishingState !== 'inherit' )
		{
			$new->published		= $this->publishingState;
		}

		if( !$new->store() )
		{
			return false;
		}

		return $new;
	}

	function load( $id )
	{
		$query = 'SELECT * FROM `#__jcomments` WHERE `id` = ' . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function getUniqueComponents()
	{
		$where = ' WHERE `object_group` IN (' . implode( ',', $this->getSupportedComponents() ) . ')';

		$query = 'SELECT DISTINCT `object_group` FROM `#__jcomments`' . $where . ' ORDER BY `object_group`';

		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}

	function getUniquePostId( $options = array() )
	{
		$defaultOptions	= array(
			'object_group'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT DISTINCT `object_id` FROM `#__jcomments`';

		$queryWhere = array();

		if( $options['object_group'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'object_group' ) . ' = ' . $this->_db->quote( $options['object_group']);
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY `object_id`';

		$this->_db->setQuery( $query );

		return $this->_db->loadResultArray();
	}

	function getComments( $options = array() )
	{
		$defaultOptions	= array(
			'level'			=> 'all',
			'object_group'	=> 'all',
			'object_id'		=> 'all',
			'thread_id'		=> 'all',
			'parent'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT * FROM `#__jcomments`';

		$queryWhere = array();

		if( $options['level'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'level' ) . ' = ' . $this->_db->quote( $options['level'] );
		}

		if( $options['object_group'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'object_group' ) . ' = ' . $this->_db->quote( $options['object_group'] );
		}

		if( $options['object_id'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'object_id' ) . ' = ' . $this->_db->quote( $options['object_id'] );
		}

		if( $options['thread_id'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'thread_id' ) . ' = ' . $this->_db->quote( $options['thread_id'] );
		}

		if( $options['parent'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'parent' ) . ' = ' . $this->_db->quote( $options['parent'] );
		}

		if( count( $queryWhere) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY date';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCommentCount( $options = array() )
	{
		$defaultOptions	= array(
			'level'			=> 'all',
			'object_group'	=> 'all',
			'object_id'		=> 'all',
			'thread_id'		=> 'all',
			'parent'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$query = 'SELECT COUNT(1) FROM `#__jcomments`';

		$queryWhere = array();

		if( $options['level'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'level' ) . ' = ' . $this->_db->quote( $options['level'] );
		}

		if( $options['object_group'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'object_group' ) . ' = ' . $this->_db->quote( $options['object_group'] );
		}

		if( $options['object_id'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'object_id' ) . ' = ' . $this->_db->quote( $options['object_id'] );
		}

		if( $options['thread_id'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'thread_id' ) . ' = ' . $this->_db->quote( $options['thread_id'] );
		}

		if( $options['parent'] !== 'all' )
		{
			$queryWhere[] = $this->_db->namequote( 'parent' ) . ' = ' . $this->_db->quote( $options['parent'] );
		}

		if( count( $queryWhere) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	function saveLikes( $oldid, $newid )
	{
		$query  = 'INSERT INTO `#__komento_actions` ( type, comment_id, action_by, actioned )';
		$query .= ' SELECT ' . $this->_db->quote( 'likes' ) . ' AS type, ' . $this->_db->quote( $newid ) . ' AS comment_id, userid, date FROM `#__jcomments_votes`';
		$query .= ' WHERE `value` = ' . $this->_db->quote( '1' );
		$query .= ' AND `commentid` = ' . $this->_db->quote( $oldid );

		$this->_db->setQuery( $query );

		return $this->_db->query();
	}
}

class KomentoModelMigratorEasyBlog extends KomentoModelAdminMigrators
{
	function save( $comment )
	{
		$new = Komento::getTable( 'comments' );

		$new->component		= $this->component;
		$new->cid			= $this->cid;
		$new->comment		= $comment->comment;
		$new->name			= $comment->name;
		$new->email			= $comment->email;
		$new->url			= $comment->url;
		$new->created		= $comment->created;
		$new->created_by	= $comment->created_by;
		$new->published		= $comment->published;
		$new->sent			= $comment->sent;
		$new->parent_id		= $comment->parent_id;
		$new->depth			= $comment->depth;
		$new->lft			= $comment->lft;
		$new->rgt			= $comment->rgt;

		if( $this->publishingState !== 'inherit' )
		{
			$new->published		= $this->publishingState;
		}

		if( !$new->store() )
		{
			return false;
		}

		return $new;
	}

	function load( $id )
	{
		$query = 'SELECT * FROM `#__easyblog_comment` WHERE `id` = ' . $this->_db->quote( $id );
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

	function getCategoryPosts( $categories = array() )
	{
		if( !empty( $categories ) )
		{
			$categories = implode( ',', $categories );
			$query = 'SELECT `id` FROM `#__easyblog_post` WHERE `category_id` IN (' . $categories . ') ORDER BY `id`';
			$this->_db->setQuery( $query );
			return $this->_db->loadResultArray();
		}

		return array();
	}

	function getUniquePostId( $options = array() )
	{
		$query = 'SELECT DISTINCT `post_id` FROM `#__easyblog_comment`';

		$defaultOptions	= array(
			'categories'	=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( !empty( $options['categories'] ) && $options['categories'] !== 'all' )
		{
			$options['post_id'] = (array) $this->getCategoryPosts( $options['categories'] );
			$queryWhere[] = $this->_db->namequote( 'post_id' )  . ' IN (' . implode( ',', $options['post_id'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' ORDER BY `post_id`';

		$this->_db->setQuery( $query );

		return $this->_db->loadResultArray();
	}

	function getComments( $options = array() )
	{
		$query  = 'SELECT x.*, COUNT(y.id) - 1 AS depth FROM `#__easyblog_comment` AS x';
		$query .= ' INNER JOIN `#__easyblog_comment` AS y';
		$query .= ' ON x.post_id = y.post_id';
		$query .= ' AND x.lft BETWEEN y.lft AND y.rgt';

		$defaultOptions	= array(
			'categories'	=> 'all',
			'post_id'		=> 'all',
			'depth'			=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( $options['categories'] !== 'all' )
		{
			$options['post_id'] = $this->getCategoryPosts( $options['categories'] );
		}

		if( !empty( $options['post_id' ] ) && $options['post_id'] !== 'all' )
		{
			$options['post_id'] = (array) $options['post_id'];
			$queryWhere[] = $this->_db->namequote( 'x' ) . '.' . $this->_db->namequote( 'post_id' )  . ' IN (' . implode( ',', $options['post_id'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$query .= ' GROUP BY x.id';
		$query .= ' ORDER BY x.lft';

		if( $options['depth'] !== 'all' )
		{
			$query = 'SELECT * FROM (' . $query . ') AS x WHERE `depth` = ' . $this->_db->quote( $options['depth'] );
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCommentCount( $options = array() )
	{
		$query = 'SELECT COUNT(1) FROM `#__easyblog_comment`';

		$defaultOptions	= array(
			'post_id'		=> 'all'
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryWhere = array();

		if( !empty( $options['post_id' ] ) && $options['post_id'] !== 'all' )
		{
			$options['post_id'] = (array) $options['post_id'];
			$queryWhere[] = $this->_db->namequote( 'post_id' )  . ' IN (' . implode( ',', $options['post_id'] ) . ')';
		}

		if( count( $queryWhere ) > 0 )
		{
			$query .= ' WHERE ' . implode( ' AND ', $queryWhere );
		}

		$this->_db->setQuery( $query );

		return $this->_db->loadResult();
	}

	function saveLikes( $ebid, $kmtid )
	{
		$query  = 'INSERT INTO `#__komento_actions` ( type, comment_id, action_by, actioned )';
		$query .= ' SELECT ' . $this->_db->quote( 'likes' ) . ' AS type, ' . $this->_db->quote( $kmtid ) . ' AS comment_id, created_by, created FROM `#__easyblog_likes`';
		$query .= ' WHERE `type` = ' . $this->_db->quote( 'comment' );
		$query .= ' AND `content_id` = ' . $this->_db->quote( $ebid );

		$this->_db->setQuery( $query );

		return $this->_db->query();
	}

	function getChildren( $ebid )
	{
		$eb = $this->load( $ebid );

		$query  = 'SELECT * FROM ( SELECT x.*, COUNT(y.id) - 1 AS depth FROM `#__easyblog_comment` AS x';
		$query .= ' INNER JOIN `#__easyblog_comment` AS y';
		$query .= ' ON x.post_id = y.post_id';
		$query .= ' AND x.lft BETWEEN y.lft AND y.rgt';
		$query .= ' WHERE x.post_id = ' . $this->_db->quote( $eb->post_id );
		$query .= ' AND y.post_id = ' . $this->_db->quote( $eb->post_id );
		$query .= ' AND x.lft BETWEEN ' . $this->_db->quote( $eb->lft ) . ' AND ' . $this->_db->quote( $eb->rgt );
		$query .= ' AND y.lft BETWEEN ' . $this->_db->quote( $eb->lft ) . ' AND ' . $this->_db->quote( $eb->rgt );
		$query .= ' GROUP BY x.id';
		$query .= ' ORDER BY x.lft ) AS x WHERE depth = 1';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

class KomentoModelMigratorCustom extends KomentoModelAdminMigrators
{
	function getUniquePostId( $data )
	{
		$query = 'SELECT DISTINCT ' . $this->_db->nameQuote( $data->contentid ) . ' FROM ' . $this->_db->nameQuote( $data->table ) . ' ORDER BY ' . $this->_db->nameQuote( $data->contentid );
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResultArray();
		return $result;
	}

	function getData( $data )
	{
		$query = 'SELECT ';

		$columns = array();
		if( $data->component == 'kmt-none' )
		{
			$columns[] = $this->_db->quote( $data->componentFilter ) . ' AS `component`';
		}
		else
		{
			$columns[] = $this->_db->nameQuote( $data->component ) . ' AS `component`';
		}

		if( $data->contentid != 'kmt-none' )
		{
			$columns[] = $this->_db->namequote( $data->contentid ) . ' AS `cid`';
		}

		if( $data->comment != 'kmt-none' )
		{
			$columns[] = $this->_db->nameQuote( $data->comment ) . ' AS `comment`';
		}

		if( $data->date != 'kmt-none' )
		{
			$columns[] = $this->_db->nameQuote( $data->date ) . ' AS `created`';
		}

		if( $data->authorid != 'kmt-none' )
		{
			$columns[] = $this->_db->nameQuote( $data->authorid ) . ' AS `created_by`';
		}

		if( $data->name != 'kmt-none' )
		{
			$columns[] = $this->_db->nameQuote( $data->name ) . ' AS `name`';
		}

		if( $data->email != 'kmt-none' )
		{
			$columns[] = $this->_db->nameQuote( $data->email ) . ' AS `email`';
		}

		if( $data->homepage != 'kmt-none' )
		{
			$columns[] = $this->_db->nameQuote( $data->homepage ) . ' AS `url`';
		}

		if( $data->published != 'kmt-none' )
		{
			$columns[] = $this->_db->nameQuote( $data->published ) . ' AS `published`';
		}

		if( $data->ip != 'kmt-none' )
		{
			$columns[] = $this->_db->nameQuote( $data->ip ) . ' AS `ip`';
		}

		$query .= implode( ',', $columns );
		$query .= ' FROM ' . $this->_db->nameQuote( $data->table );

		if( $data->component != 'kmt-none' )
		{
			$query .= ' WHERE ' . $this->_db->nameQuote( $data->component ) . ' = ' . $this->_db->quote( $data->componentFilter );
		}

		if( $data->date != 'kmt-none' )
		{
			$query .= ' ORDER BY ' . $this->_db->nameQuote( $data->date );
		}

		if( !isset( $data->start ) )
		{
			$data->start = 0;
		}

		if( $data->cycle != 0 )
		{
			$query .= ' LIMIT ' . $data->start . ', ' . $data->cycle;
		}

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result;
	}

	function getCount( $data )
	{
		$query = 'SELECT COUNT(1) FROM ' . $this->_db->nameQuote( $data->table );

		if( $data->component != 'kmt-none' )
		{
			$query .= ' WHERE ' . $this->_db->nameQuote( $data->component ) . ' = ' . $this->_db->quote( $data->componentFilter );
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}
