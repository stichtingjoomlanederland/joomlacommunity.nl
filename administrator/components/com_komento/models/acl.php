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

class KomentoModelAdminAcl extends KomentoParentModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	protected $total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	protected $data = null;


	protected $systemComponents = array();

	public function __construct($config = array())
	{
		parent::__construct($config);

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_komento.acls.limit', 'limit', $mainframe->getCfg('list_limit', 20), 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$this->systemComponents = array(
				'com_config', 'com_finder', 'com_media', 'com_redirect', 'com_users', 'com_content', 'com_komento'
			);
	}

	public function getComponents()
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_acl' )
			->column( 'component', 'component', 'distinct' )
			->order( 'component' );

		$components = $sql->loadResultArray();

		if( empty( $components ) )
		{
			$components = array( 'com_content' );
		}

		return $components;
	}

	public function updateUserGroups( $component )
	{
		$userGroups	= Komento::getUsergroups();
		$userGroupIDs = array();

		foreach( $userGroups as $userGroup )
		{
			$userGroupIDs[] = $userGroup->id;
		}

		$sql = Komento::getSql();
		$sql->select( '#__komento_acl' )
			->column( 'cid' )
			->where( 'component', $component )
			->where( 'type', 'usergroup' );

		$current	= $sql->loadResultArray();

		Komento::import( 'helper', 'acl' );
		$defaultset = KomentoACLHelper::getEmptySet( true );

		$json = Komento::getJSON();
		$defaultset = $json->encode( $defaultset );

		foreach( $userGroupIDs as $userGroupID )
		{
			if( !in_array( $userGroupID, $current ) )
			{
				$table = Komento::getTable( 'acl' );
				$table->cid = $userGroupID;
				$table->component = $component;
				$table->type = 'usergroup';
				$table->rules = $defaultset;

				$table->store();
			}
		}
	}

	public function getData( $component = 'com_component', $type = 'usergroup', $cid = 0 )
	{
		static $_cache = array();

		$idx = $component . $type . $cid;

		if (isset($_cache[$idx])) {
			return $_cache[$idx];
		}

		$sql = Komento::getSql();

		$sql->select( '#__komento_acl' )
			->column( 'rules' )
			->where( 'component', $component )
			->where( 'type', $type )
			->where( 'cid', $cid )
			->order( 'type' );

		$rulesets = $sql->loadResult();

		if( empty( $rulesets ) )
		{
			$rulesets = new stdClass();
		}
		else
		{
			$json = Komento::getJSON();
			$rulesets = $json->decode( $rulesets );
		}

		Komento::import( 'helper', 'acl' );
		$defaultset = KomentoACLHelper::getEmptySet();

		foreach( $defaultset as $section => &$rules )
		{
			foreach( $rules as $key => &$value )
			{
				if( isset( $rulesets->$key ) )
				{
					$value = $rulesets->$key;
				}
			}
		}

		$_cache[$idx] = $defaultset;

		return $defaultset;
	}

	public function save( $data )
	{
		$component = $data['target_component'];
		unset( $data['target_component'] );

		$cid = $data['target_id'];
		unset( $data['target_id'] );

		$type = $data['target_type'];
		unset( $data['target_type'] );

		Komento::import( 'helper', 'acl' );
		$defaultset = KomentoACLHelper::getEmptySet( true );

		foreach( $defaultset as $key => $value )
		{
			if( isset( $data[$key] ) )
			{
				$defaultset->$key = $data[$key] ? true : false;
			}
		}

		$table = Komento::getTable( 'Acl' );
		$table->compositeLoad( $cid, $type, $component );

		$json = Komento::getJSON();
		$table->rules = $json->encode( $defaultset );

		return $table->store();
	}
}
