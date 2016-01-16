<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class KomentoViewSystem extends KomentoAdminView
{
	public function getArticles()
	{
		$ajax = Komento::getAjax();

		$component = JRequest::getString( 'component' );

		$sql = Komento::getSql();

		$sql->select( '#__komento_comments' )
			->column( 'cid', 'cid', 'DISTINCT' )
			->where( 'component', $component )
			->order( 'cid' );

		$result = $sql->loadColumn();

		$ajax->resolve( $result );

		return $ajax->send();
	}

	public function getArticleStatistics()
	{
		$ajax = Komento::getAjax();

		// check depth column first
		$db = Komento::getDBO();
		if( !$db->isColumnExists( '#__komento_comments', 'depth' ) )
		{
			$query = 'ALTER TABLE  `#__komento_comments` ADD `depth` INT(11) NOT NULL DEFAULT \'0\' AFTER `rgt`';
			$db->setQuery( $query );
			if( !$db->query() )
			{
				$ajax->reject();
				return $ajax->send();
			}
		}

		$component = JRequest::getString( 'component' );
		$cid = JRequest::getString( 'cid' );

		$query = "SELECT DISTINCT `component`, `cid` FROM `#__komento_comments`";

		if( !empty( $component ) && $component !== 'all' )
		{
			$query .= " WHERE `component` = '$component'";

			if( !empty( $cid ) && $cid !== 'all' )
			{
				$query .= " AND `cid` = '$cid'";
			}
		}

		$sql = Komento::getSql();
		$sql->raw( $query );

		$parents = $sql->loadObjectList();

		$ajax->resolve( $parents );
		return $ajax->send();
	}

	public function populateDepth()
	{
		$db = Komento::getDBO();
		$ajax = Komento::getAjax();

		$component = JRequest::getString( 'component' );
		$cid = JRequest::getString( 'cid' );

		$query = 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__komento_comments' );
		$query .= ' WHERE ' . $db->nameQuote( 'component' ) . ' = ' . $db->quote( $component );
		$query .= ' AND ' . $db->nameQuote( 'cid' ) . ' = ' . $db->quote( $cid );
		$query .= ' AND ' . $db->nameQuote( 'parent_id' ) . ' = ' . $db->quote( '0' );

		$db->setQuery( $query );

		$parents = $db->loadResultArray();

		if( !empty( $parents ) )
		{
			foreach( $parents as $parent )
			{
				$this->populateChildDepth( $parent, 1 );
			}
		}

		$ajax->resolve();
		return $ajax->send();
	}

	private function populateChildDepth( $id, $depth )
	{
		$children = $this->getChildrenId( $id );

		if( !empty( $children ) )
		{
			$ids = implode( ',', $children );

			$this->updateDepth( $ids, $depth );

			foreach( $children as $child )
			{
				$this->populateChildDepth( $child, $depth + 1 );
			}
		}
	}

	private function getChildrenId( $id )
	{
		$db = Komento::getDBO();

		$query = 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__komento_comments' );
		$query .= ' WHERE ' . $db->nameQuote( 'parent_id' ) . ' = ' . $db->quote( $id );

		$db->setQuery( $query );

		$children = $db->loadResultArray();

		return $children;
	}

	private function updateDepth( $ids, $depth )
	{
		$db = Komento::getDBO();

		$query = 'UPDATE ' . $db->nameQuote( '#__komento_comments' ) . ' SET ' . $db->nameQuote( 'depth' ) . ' = ' . $db->quote( $depth );
		$query .= ' WHERE ' . $db->nameQuote( 'id' ) . ' IN(' . $db->quote( $ids ) . ')';

		$db->setQuery( $query );
		$db->query();
	}

	public function fixStructure()
	{
		$ajax = Komento::getAjax();

		$component = JRequest::getString( 'component' );
		$cid = JRequest::getString( 'cid' );

		$parents = $this->getParents( $component, $cid );

		// Set boundary to start from 1
		$boundary = 1;

		// Fix all the parent first
		foreach( $parents as $parent )
		{
			$this->fixItemStructure( $parent, $boundary, 0 );
		}

		// Now we start fixing the childrens
		foreach( $parents as $parent )
		{
			$this->fixChildStructure( $parent );
		}

		$ajax->resolve();
		return $ajax->send();
	}

	public function normalizeStructure()
	{
		$sql = Komento::getSql();
		$ajax = Komento::getAjax();

		$component = JRequest::getString( 'component' );
		$cid = JRequest::getString( 'cid' );

		// Normalize all invalid parent id first
		$query = "UPDATE `#__komento_comments` as `a`";
		$query .= " LEFT JOIN `#__komento_comments` as `b`";
		$query .= " ON `a`.`parent_id` = `b`.`id`";
		$query .= " AND `a`.`component` = `b`.`component`";
		$query .= " AND `a`.`cid` = `b`.`cid`";
		$query .= " SET `a`.`parent_id` = 0, `a`.`depth` = 0";
		$query .= " WHERE `b`.`id` is null";
		$query .= " AND `a`.`component` = '$component'";
		$query .= " AND `a`.`cid` = '$cid'";
		$query .= " AND `a`.`parent_id` <> 0";

		$sql->raw( $query );
		$sql->query();

		$sql->clear();

		// Now we normalize the structure of lft rgt values
		$sql->select( '#__komento_comments' )
			->where( 'component', $component )
			->where( 'cid', $cid )
			->order( 'created' );

		$result = $sql->loadObjectList();

		$boundary = 1;

		foreach( $result as $row )
		{
			$table = Komento::getTable( 'comments' );
			$table->bind( $row );

			$table->lft = $boundary++;
			$table->rgt = $boundary++;

			$table->store();
		}

		$ajax->resolve();
		return $ajax->send();
	}

	private function fixChildStructure( $id )
	{
		$parent = Komento::getTable( 'comments' );
		$parent->load( $id );

		$boundary = $parent->lft + 1;
		$depth = $parent->depth + 1;

		$children = $this->getChildren( $id );

		if( !empty( $children ) )
		{
			$total = count( $children );

			$this->pushBoundaries( $parent, $total );

			// Fix all the direct children first
			foreach( $children as $child )
			{
				$this->fixItemStructure( $child, $boundary, $depth );
			}

			foreach( $children as $child )
			{
				$this->fixChildStructure( $child );
			}
		}
	}

	private function fixItemStructure( $id, &$boundary, $depth )
	{
		$item = Komento::getTable( 'comments' );
		$item->load( $id );

		$item->lft = $boundary++;
		$item->rgt = $boundary++;

		$item->depth = $depth;

		$item->store();
	}

	private function getParents( $component, $cid )
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_comments' )
			->column( 'id' )
			->where( 'component', $component )
			->where( 'cid', $cid )
			->where( 'parent_id', 0 )
			->order( 'created' );

		$result = $sql->loadResultArray();

		return $result;
	}

	private function getChildren( $id )
	{
		// TODO: Change this to use parent table->getChildren() function

		$sql = Komento::getSql();

		$sql->select( '#__komento_comments' )
			->column( 'id' )
			->where( 'parent_id', $id )
			->order( 'created' );

		$result = $sql->loadResultArray();

		return $result;
	}

	private function pushBoundaries( $item, $count )
	{
		$diff = $count * 2;

		$sql = Komento::getSql();

		$query = "UPDATE `#__komento_comments` SET `lft` = `lft` + $diff WHERE `component` = '$item->component' AND `cid` = '$item->cid' AND `lft` > $item->lft";

		$sql->raw( $query );
		$sql->query();

		$query = "UPDATE `#__komento_comments` SET `rgt` = `rgt` + $diff WHERE `component` = '$item->component' AND `cid` = '$item->cid' AND `rgt` > $item->lft";

		$sql->raw( $query );
		$sql->query();

	}
}
