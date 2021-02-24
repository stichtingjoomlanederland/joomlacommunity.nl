<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views/views.php';
jimport('joomla.utilities.utility');

class EasyDiscussViewMigrators extends EasyDiscussAdminView
{
	var $err = null;

	public function migrate()
	{
		$component = $this->input->get('component', '', 'string');

		if (!$component) {
			die('Invalid migration');
		}

		switch($component)
		{
			case 'com_kunena':

				$migrator = ED::migrator()->getAdapter('kunena');
				$resetHits = $this->input->get('resetHits', false, 'boolean');
				$migrateSignature = $this->input->get('migrateSignature', false, 'boolean');
				$migrateAvatar = $this->input->get('migrateAvatar', false, 'boolean');
				$replies = $this->input->get('replies', false, 'boolean');

				if ($replies) {
					$total = $this->input->get('total', 0, 'int');
					$migrator->migrateReplies($total);
				} else {
					$migrator->migrate($resetHits, $migrateSignature, $migrateAvatar);
				}

				break;

			case 'com_community':

				$migrator = ED::migrator()->getAdapter('jomsocial');

				$migrator->migrate();

				break;

			case 'vbulletin':
				$prefix = $this->input->get('prefix', '', 'string');

				$migrator = ED::migrator()->getAdapter('vbulletin');

				$migrator->migrate($prefix);

				break;

			case 'com_discussions':
				$migrator = ED::migrator()->getAdapter('discussions');

				$migrator->migrate();

				break;

			default:
				break;
		}
	}

	/**
	 * Check whether the vBulletin prefix exist
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function checkPrefix()
	{
		$db = ED::db();

		$prefix = $this->input->get('prefix', '', 'string');

		if (empty($prefix)) {
			return $this->ajax->reject(JText::sprintf('COM_EASYDISCUSS_VBULLETN_DB_PREFIX_NOT_FOUND', $prefix));
		}

		// Check if the vBulletin table exist
		$tables = $db->getTableList();
		$exist = in_array($prefix . 'thread', $tables);

		if (empty($exist)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_VBULLETN_DB_TABLE_NOT_FOUND'));
		}

		$this->ajax->resolve($prefix);
	}

	private function json_encode( $data )
	{
		$json	= new Services_JSON();
		$data	= $json->encode( $data );

		return $data;
	}

	private function json_decode( $data )
	{
		$json	= new Services_JSON();
		$data	= $json->decode( $data );

		return $data;
	}

	private function log( &$ajax , $message , $type )
	{
		if( $ajax instanceof DiscussAjaxHelper )
		{
			$ajax->updateLog( $message );
		}
		else
		{
			$ajax->script( 'appendLog("' . $type . '" , "' . $message . '");' );
		}
	}

	/**
	 * Determines if an item is already migrated
	 */
	private function migrated( $component , $externalId , $type )
	{
		$db		= ED::db();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' )
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $externalId ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( $component );
		$db->setQuery( $query );

		$exists	= $db->loadResult();
		return $exists;
	}
}
