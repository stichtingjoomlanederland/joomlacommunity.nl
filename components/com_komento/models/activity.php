<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class KomentoModelActivity extends KomentoModel
{
	public $_total = null;

	public function add( $type, $comment_id, $uid )
	{
		$table	= Komento::getTable( 'activities' );

		$table->type		= $type;
		$table->comment_id	= $comment_id;
		$table->uid			= $uid;
		$table->created		= Komento::getDate()->toMySQL();
		$table->published	= 1;

		return $table->store();
	}

	public function delete( $comment_id )
	{
		$sql = Komento::getSql();
		$sql->delete( '#__komento_activities' );
		$sql->where( 'comment_id', $comment_id );

		return $sql->query();
	}

	public function getUserActivities( $id, $options = array() )
	{
		// comments, likes, recommends, articles, forum post, feature... need a hook to get 3rd party content what say you?

		// define default values
		$defaultOptions	= array(
			'type'		=> 'like,comment,reply',
			'sort'		=> 'latest',
			'start'		=> 0,
			'limit'		=> 10,
			// 'search'	=> '', future todo
			'published'	=> 1,
			'component'	=> 'all',
			'cid'		=> 'all'
		);

		// take the input values and clear unexisting keys
		$options	= array_merge($defaultOptions, $options);

		$sql = $this->buildQuery( $id, $options );

		return $sql->loadObjectList();
	}

	public function getTotalUserActivities( $id, $options = array() )
	{
		if( empty( $this->_total ) )
		{
			// define default values
			$defaultOptions	= array(
				'type'		=> 'like,comment,reply',
				'published'	=> 1,
				'component'	=> 'all',
				'cid'		=> 'all'
			);

			$options	= array_merge($defaultOptions, $options);

			$sql = $this->buildQuery( $id, $options );

			$query = $sql->getTotalSql();

			$sql->db->setQuery( $query );

			$this->_total = $sql->db->loadResult();
		}

		return $this->_total;
	}

	private function buildQuery( $id, $options )
	{
		$sql = Komento::getSql();


		$sql->select( '#__komento_activities', 'a' )
			->column( 'a.*' )
			->column( 'b.component' )
			->column( 'b.cid' )
			->column( 'b.comment' )
			->column( 'b.name' )
			->column( 'b.created_by' )
			->column( 'b.parent_id' )
			->leftjoin( '#__komento_comments', 'b' )
			->on( 'a.comment_id', 'b.id' );

		if( $id !== 'all' )
		{
			$sql->where( 'a.uid', $id );
		}

		$sql->where( 'a.published', $options['published'] );
		$sql->where( 'b.published', 1 );

		if( $options['component'] !== 'all' )
		{
			$sql->where( 'b.component', $options['component'] );
		}

		if( $options['cid'] !== 'all' )
		{
			if( !is_array( $options['cid'] ) )
			{
				$options['cid'] = explode( ',', $options['cid'] );
			}

			if( count( $options['cid'] ) > 1 )
			{
				$sql->where( 'b.cid', $options['cid'], 'in' );
			}
			else
			{
				$sql->where( 'b.cid', $options['cid'][0] );
			}
		}

		if( $options['type'] !== 'all' )
		{
			if( !is_array( $options['type'] ) )
			{
				$options['type'] = explode( ',', $options['type'] );
			}

			if( count( $options['type'] ) > 1 )
			{
				$sql->where( 'a.type', $options['type'], 'in' );
			}
			else
			{
				$sql->where( 'a.type', $options['type'][0] );
			}
		}

		if( isset( $options['sort'] ) )
		{
			switch( $options['sort'] )
			{
				case 'oldest':
					$sql->order( 'a.created' );
					break;
				case 'latest':
				default:
					$sql->order( 'a.created', 'desc' );
					break;
			}
		}

		if( isset( $options['start'] ) && isset( $options['limit'] ) )
		{
			$sql->limit( $options['start'], $options['limit'] );
		}

		return $sql;
	}
}
