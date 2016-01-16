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

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgUserKomentoUsers extends JPlugin
{
	function plgUserKomentoUsers(& $subject, $config)
	{
		if(JFile::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_komento'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'))
		{
			require_once (JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_komento'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');
		}
		parent::__construct($subject, $config);
	}

	public function init()
	{
		static $loaded;

		if (empty($loaded)) {

			$file = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_komento'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';

			if (!JFile::exists($file)) {
				return false;
			}

			require_once ($file);

			$loaded = true;
		}

		return true;
	}

	function onUserBeforeDelete($user)
	{
		$this->onBeforeDeleteUser($user);
	}

	function onBeforeDeleteUser($user)
	{
		if (!$this->init()) {
			return;
		}

		if( is_object($user))
		{
			$user   = get_object_vars( $user );
		}

		$userId	= $user['id'];
		$newId	= $this->getNewOwnership( $userId );

		$this->removeSubscription( $userId );
		$this->removeActions( $userId );
		$this->removeComments( $userId );

		// $this->transferActions( $userId, $0 );
		// $this->transferComments( $userId, 0 );
	}

	function getNewOwnership( $userId )
	{
		return Komento::getConfig()->get( 'orphanitem_ownership' );
	}

	function removeSubscription( $userId )
	{
		$db		= Komento::getDBO();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__komento_subscription' );
		$query	.= ' WHERE ' . $db->nameQuote( 'userid' ) . ' = ' . $db->quote( $userId );

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function transferComments( $userId, $newId )
	{
		$db		= Komento::getDBO();

		$query  = 'UPDATE ' . $db->nameQuote( '#__komento_comments' );
		$query .= ' SET ' . $db->nameQuote( 'created_by' ) . ' = ' . $db->quote( $newId );
		$query .= ' WHERE ' . $db->nameQuote( 'created_by' ) . ' = ' .  $db->quote( $userId );

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeComments( $userId )
	{
		$db = Komento::getDBO();

		$query  = 'DELETE FROM ' . $db->nameQuote( '#__komento_comments' );
		$query .= ' WHERE ' . $db->nameQuote( 'created_by' ) . ' = ' .  $db->quote( $userId );

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function transferActions( $userId, $newId )
	{
		$db = Komento::getDBO();

		$query  = 'UPDATE ' . $db->nameQuote( '#__komento_actions' );
		$query .= ' SET ' . $db->nameQuote( 'action_by' ) . ' = ' . $db->quote( $newId );
		$query .= ' WHERE ' . $db->nameQuote( 'action_by' ) . ' = ' . $db->quote( $userId );

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeActions( $userId )
	{
		$db = Komento::getDBO();

		$query  = 'DELETE FROM ' . $db->nameQuote( '#__komento_actions' );
		$query .= ' WHERE ' . $db->nameQuote( 'action_by' ) . ' = ' . $db->quote( $userId );

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}
}
