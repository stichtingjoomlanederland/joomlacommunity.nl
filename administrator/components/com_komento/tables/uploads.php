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

Komento::import( 'helper', 'file' );

class KomentoTableUploads extends KomentoParentTable
{
	public $id			= null;
	public $uid			= null;
	public $filename	= null;
	public $hashname	= null;
	public $path		= null;
	public $created		= null;
	public $created_by	= null;
	public $published	= null;
	public $mime		= null;
	public $size		= null;

	public function __construct( &$db )
	{
		parent::__construct( '#__komento_uploads' , 'id' , $db );
	}

	public function getType()
	{
		$type = explode( "/", $this->mime );

		return $type[0];
	}

	public function getSubtype()
	{
		$type = explode( "/", $this->mime );

		return $type[1];
	}

	public function upload()
	{
		if( empty( $this->hashname ) )
		{
			$this->hashname = $this->hash();
		}

		return $this->store();
	}

	public function download()
	{
		$file = $this->getFilePath();

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		$length = filesize( $file );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: ' . $this->mime );
		header( 'Content-Disposition: attachment; filename="' . basename( $this->filename ) . '";' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . $length );

		ob_clean();
		flush();
		readfile( $file );
		exit;
	}

	public function rollback()
	{
		$this->delete();
	}

	private function hash()
	{
		return md5( $this->filename . Komento::getDate()->toMySQL() );
	}

	// Overwrite parent delete function
	public function delete( $pk = null )
	{
		$state = parent::delete( $pk );

		if( !$state )
		{
			return false;
		}

		$file = $this->getFilePath();

		jimport( 'joomla.filesystem.file' );

		return JFile::delete( $file );
	}

	public function getPath()
	{
		$path = KOMENTO_UPLOADS_ROOT . DIRECTORY_SEPARATOR;

		// @task: Ensure that the relativePath is set to the proper directory separator.
		$relativePath	= trim( str_ireplace( array( '/' , '\\' ) , DIRECTORY_SEPARATOR , $this->path ), DIRECTORY_SEPARATOR );

		if( !empty( $relativePath ) )
		{
			$path .= $relativePath;
		}

		// @task: Create the directory if it doesn't exist
		if( !file_exists( $path ) )
		{
			jimport( 'joomla.filesystem.folder' );
			JFolder::create( $path );
		}

		return $path;
	}

	public function getFilePath()
	{
		$file = $this->getPath() . DIRECTORY_SEPARATOR . $this->hashname;

		return $file;
	}

	public function getLink()
	{
		$link = rtrim( JURI::root(), '/' ) . '/index.php?option=com_komento&controller=file&task=download&id=' . $this->id;

		return $link;
	}

	public function getExtension()
	{
		$tmp = explode( '.', $this->filename );

		if( count( $tmp ) <= 1 )
		{
			return false;
		}

		$extension = array_pop( $tmp );

		return $extension;
	}

	public function getIconType()
	{
		$type = $this->getType();

		$class = 'file';

		switch( $type )
		{
			case 'image':
			case 'audio':
			case 'video':
			case 'text':
				$class = $type;
				break;
			case 'application':

				$extension = $this->getExtension();

				if( $extension !== false )
				{
					switch( $extension )
					{
						case 'doc':
						case 'docx':
						case 'odt':
							$class = 'document';
							break;
						case 'xls':
						case 'xlsx':
						case 'xlb':
						case 'ods':
							$class = 'spreadsheet';
							break;
						case 'ppt':
						case 'pptx':
						case 'pps':
						case 'pot':
						case 'odp':
							$class = 'slideshow';
							break;
						case 'zip':
						case 'rar':
						case 'cab':
						case 'msi':
							$class = 'archive';
							break;
						case 'pdf':
							$class = 'pdf';
							break;
					}
				}
				else
				{
					$subtype = $this->getSubtype();

					switch( $subtype )
					{
						case 'msword':
						case 'vnd.oasis.opendocument.text':
							$class = 'document';
							break;
						case 'vnd.ms-excel':
						case 'vnd.oasis.opendocument.spreadsheet':
							$class = 'spreadsheet';
							break;
						case 'vnd.ms-powerpoint':
						case 'vnd.oasis.opendocument.presentation':
							$class = 'slideshow';
							break;
						case 'zip':
						case 'x-rar':
						case 'x-rar-compressed':
						case 'x-cab':
						case 'vnd.ms-cab-compressed':
							$class = 'archive';
							break;
						case 'pdf':
							$class = 'pdf';
							break;
					}
				}
		}

		return $class;
	}

	public function isCommentAttachment( $commentid )
	{
		return ( $this->uid == $commentid );
	}
}
