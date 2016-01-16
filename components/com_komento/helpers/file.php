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

Komento::import( 'helper', 'file' );

class KomentoFileHelper
{
	public function upload( $fileItem, $fileName = '', $storagePath = '', $published = 1 )
	{
		// $fileItem['name'] = filename
		// $fileItem['type'] = mime
		// $fileItem['tmp_name'] = temporary source
		// $fileItem['size'] = size

		if( empty( $fileItem ) )
		{
			return false;
		}

		// store record first
		$uploadtable = Komento::getTable( 'uploads' );

		$now = Komento::getDate()->toMySQL();
		$uploadtable->created = $now;

		$profile = Komento::getProfile();
		$uploadtable->created_by = $profile->id;

		$uploadtable->published = $published;

		$uploadtable->mime = $fileItem['type'];

		$uploadtable->size = $fileItem['size'];

		if( $fileName == '' )
		{
			$fileName = $fileItem['name'];
		}
		$uploadtable->filename = $fileName;

		if( $storagePath == '' )
		{
			$config = Komento::getConfig();
			$storagePath = $config->get( 'upload_path' );
		}
		$uploadtable->path = $storagePath;

		if( !$uploadtable->upload() )
		{
			return false;
		}

		$source = $fileItem['tmp_name'];
		$destination = $uploadtable->getFilePath();

		jimport( 'joomla.filesystem.file' );
		if( !JFile::copy( $source , $destination ) )
		{
			$uploadtable->rollback();
			return false;
		}

		return $uploadtable->id;
	}

	public function attach( $id, $uid )
	{
		$table = Komento::getTable( 'uploads' );
		$state = $table->load( $id );

		if( !$state )
		{
			return false;
		}

		$table->uid = $uid;

		return $table->store();
	}

	public function clearAttachments( $uid )
	{
		$model = Komento::getModel( 'uploads' );
		$attachments = $model->getAttachments( $uid );

		foreach( $attachments as $attachment )
		{
			$attachment->delete();
		}

		return true;
	}
}
