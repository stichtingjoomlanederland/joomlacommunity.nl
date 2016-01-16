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

class KomentoTableComments extends KomentoParentTable
{
	/**
	 * The id of the comment
	 * @var int
	 */
	public $id 				= null;

	/**
	 * The related component name
	 * @var string
	 */
	public $component		= null;

	/**
	 * The unique content id
	 * @var int
	 */
	public $cid				= null;

	/**
	 * The comment
	 * @var string
	 */
	public $comment			= null;

	/**
	 * The name of the commenter
	 * @var string
	 */
	public $name			= null;

	/**
	 * The title of the comment
	 * optional
	 * @var string
	 */
	public $title			= null;

	/**
	 * The email of the commenter
	 * optional
	 * @var string
	 */
	public $email			= null;

	/**
	 * The website of the commenter
	 * optional
	 * @var string
	 */
	public $url				= null;

	/**
	 * The ip of the visitor
	 * optional
	 * @var string
	 */
	public $ip				= null;

	/**
	 * The author of the comment
	 * optional
	 * @var int
	 */

	public $created_by		= null;


	/**
	 * Created datetime of the comment
	 * @var datetime
	 */

	public $created			= null;

	/**
	 * modified datetime of the comment
	 * optional
	 * @var datetime
	 */

	public $modified_by		= null;

	/**
	 * last modified user
	 * optional
	 * @var int
	 */

	public $modified		= null;

	/**
	 * deleted datetime of the comment
	 * optional
	 * @var datetime
	 */

	public $deleted_by		= null;

	/**
	 * user that deleted comment
	 * optional
	 * @var int
	 */

	public $deleted			= null;

	/**
	 * flag deleted/inappropriate/report comment
	 * @var int
	 */

	public $flag			= null;

	/**
	 * Tag publishing status
	 * @var int
	 */

	public $published		= null;

	/**
	 * comment publish datetime
	 * optional
	 * @var datetime
	 */
	public $publish_up		= null;

	/**
	 * Comment un-publish datetime
	 * optional
	 * @var datetime
	 */
	public $publish_down	= null;

	/**
	 * Comment sticked
	 * @var int
	 */
	public $sticked			= null;

	/**
	 * Comment notification sent
	 * @var int
	 */
	public $sent			= null;

	/**
	 * Comment's parent_id
	 * @var int
	 */
	public $parent_id		= null;

	/**
	 * Comment's depth
	 * @var int
	 */
	public $depth			= null;

	/**
	 * Comment lft - used in threaded comment
	 * @var int
	 */
	public $lft				= null;

	/**
	 * Comment rgt - used in threaded comment
	 * @var int
	 */
	public $rgt				= null;

	/**
	 * Comment latitude - for location
	 * @var int
	 */
	public $latitude		= null;

	/**
	 * Comment longitude - for location
	 * @var int
	 */
	public $longitude		= null;

	/**
	 * Comment address - for location
	 * @var string
	 */
	public $address			= null;

	/**
	 * Comment parameters - extended data for internal use
	 * @var string (in json format)
	 */
	public $params			= null;

	/**
	 * Comment ratings - determines the rating value for this comment
	 * @var int
	 */
	public $ratings			= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__komento_comments' , 'id' , $db );
	}

	public function getAttachments()
	{
		$model = Komento::getModel( 'uploads' );
		$attachments = $model->getAttachments( $this->id );

		return $attachments;
	}

	public function load( $keys = null, $reset = true )
	{
		$state = parent::load( $keys, $reset );

		if( !empty( $this->params ) && is_string( $this->params ) )
		{
			$this->params = json_decode( $this->params );
		}

		if( !is_object( $this->params ) )
		{
			$this->params = new stdClass();
		}

		return $state;
	}

	public function store( $updateNulls = false )
	{
		$paramsEncoded = false;

		if( is_object( $this->params ) )
		{
			$this->params = json_encode( $this->params );

			if( empty( $this->params ) )
			{
				$this->params = '{}';
			}

			$paramsEncoded = true;
		}

		$state = parent::store( $updateNulls );

		if( $paramsEncoded )
		{
			$this->params = json_decode( $this->params );
		}

		return $state;
	}
}
