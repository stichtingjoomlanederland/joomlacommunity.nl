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

class KomentoModelUploads extends KomentoModel
{
	public function getAttachments( $uid )
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_uploads' )
			->where( 'uid', $uid )
			->order( 'created' );

		$result = $sql->loadObjectList();

		$attachments = array();

		foreach( $result as $row )
		{
			$table = Komento::getTable( 'uploads' );
			$table->bind( $row );

			$attachments[] = $table;
		}

		return $attachments;
	}
}
