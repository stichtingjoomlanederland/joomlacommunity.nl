<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

/**
 * @package		Joomla
 * @subpackage	Media
 */
class DiscussImageHelper
{
	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return boolean
	 */
	public static function isImage( $fileName )
	{
		static $imageTypes = 'gif|jpg|jpeg|png';
		return preg_match("/$imageTypes/i",$fileName);
	}

	public static function getFileExtention($fileName)
	{
		if(empty($fileName))
			return false;

		$data	= explode('.', $fileName);
		return $data[count($data) - 1];
	}

	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return file type
	 */
	public static function getTypeIcon( $fileName )
	{
		// Get file extension
		return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	}

	/**
	 * Checks if the file can be uploaded
	 *
	 * @param array File information
	 * @param string An error message to be returned
	 * @return boolean
	 */
	public static function canUpload( $file, &$err )
	{
		//$params = JComponentHelper::getParams( 'com_media' );
		$config = DiscussHelper::getConfig();
		$maxSize = $config->get( 'main_upload_maxsize' );

		// Convert MB to B
		$maxSize = $maxSize * 1024 * 1024;

		if(empty($file['name'])) {
			$err = JText::_( 'COM_EASYDISCUSS_EMPTY_FILENAME' );
			return false;
		}

		jimport('joomla.filesystem.file');
		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$err = JText::_( 'COM_EASYDISCUSS_INVALID_FILENAME' );
			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		if(! DiscussImageHelper::isImage($file['name']) )
		{
			$err = JText::_( 'COM_EASYDISCUSS_INVALID_IMG' );
			return false;
		}

		$maxWidth	= 160;
		$maxHeight	= 160;

		// maxsize should get from eblog config
		//$maxSize	= 2000000; //2MB
		//$maxSize	= 200000; //200KB
		//$maxSize = (int) $params->get( 'main_upload_maxsize', 0 );

		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$err = JText::_( 'COM_EASYDISCUSS_FILE_TOO_LARGE' );
			return false;
		}

		$user = JFactory::getUser();
		$imginfo = null;

		if(($imginfo = getimagesize($file['tmp_name'])) === FALSE) {
			$err = JText::_( 'COM_EASYDISCUSS_IMAGE_CORRUPT' );
			return false;
		}

		return true;
	}

	/**
	 * This function is not using in anywhere of EasyDiscuss
	 * commented out for future use?
	 *
	 * @param array File information
	 * @param string An error message to be returned
	 * @return boolean
	 */

	// public static function canUploadImage( $file, &$err )
	// {
	// 	//$params = JComponentHelper::getParams( 'com_media' );
	// 	$params = DiscussHelper::getConfig();

	// 	if(empty($file['name'])) {
	// 		$err = 'PLEASE INPUT A FILE FOR UPLOAD';
	// 		return false;
	// 	}

	// 	jimport('joomla.filesystem.file');
	// 	if ($file['name'] !== JFile::makesafe($file['name'])) {
	// 		$err = 'WARNFILENAME';
	// 		return false;
	// 	}

	// 	$format = strtolower(JFile::getExt($file['name']));

	// 	$allowable	= explode( ',', $params->get( 'upload_extensions' ));
	// 	$ignored 	= explode(',', $params->get( 'ignore_extensions' ));
	// 	if (!in_array($format, $allowable) && !in_array($format,$ignored))
	// 	{
	// 		$err = 'WARNFILETYPE';
	// 		return false;
	// 	}

	// 	$maxSize = (int) $params->get( 'main_upload_maxsize', 0 );
	// 	if ($maxSize > 0 && (int) $file['size'] > $maxSize)
	// 	{
	// 		$err = 'WARNFILETOOLARGE';
	// 		return false;
	// 	}

	// 	$user = JFactory::getUser();
	// 	$imginfo = null;
	// 	if($params->get('restrict_uploads',1) ) {
	// 		$images = explode( ',', $params->get( 'image_extensions' ));
	// 		if(in_array($format, $images)) { // if its an image run it through getimagesize
	// 			if(($imginfo = getimagesize($file['tmp_name'])) === FALSE) {
	// 				$err = 'WARNINVALIDIMG';
	// 				return false;
	// 			}
	// 		} else if(!in_array($format, $ignored)) {
	// 			// if its not an image...and we're not ignoring it
	// 			$allowed_mime = explode(',', $params->get('upload_mime'));
	// 			$illegal_mime = explode(',', $params->get('upload_mime_illegal'));
	// 			if(function_exists('finfo_open') && $params->get('check_mime',1)) {
	// 				// We have fileinfo
	// 				$finfo = finfo_open(FILEINFO_MIME);
	// 				$type = finfo_file($finfo, $file['tmp_name']);
	// 				if(strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime)) {
	// 					$err = 'WARNINVALIDMIME';
	// 					return false;
	// 				}
	// 				finfo_close($finfo);
	// 			} else if(function_exists('mime_content_type') && $params->get('check_mime',1)) {
	// 				// we have mime magic
	// 				$type = mime_content_type($file['tmp_name']);
	// 				if(strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime)) {
	// 					$err = 'WARNINVALIDMIME';
	// 					return false;
	// 				}
	// 			} else if(!$user->authorize( 'login', 'administrator' )) {
	// 				$err = 'WARNNOTADMIN';
	// 				return false;
	// 			}
	// 		}
	// 	}

	// 	$xss_check =  JFile::read($file['tmp_name'],false,256);
	// 	$html_tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');
	// 	foreach($html_tags as $tag) {
	// 		// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
	// 		if(stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>')) {
	// 			$err = 'WARNIEXSS';
	// 			return false;
	// 		}
	// 	}
	// 	return true;
	// }

	public static function parseSize($size)
	{
		if ($size < 1024) {
			return $size . ' bytes';
		}
		else
		{
			if ($size >= 1024 && $size < 1024 * 1024) {
				return sprintf('%01.2f', $size / 1024.0) . ' Kb';
			} else {
				return sprintf('%01.2f', $size / (1024.0 * 1024)) . ' Mb';
			}
		}
	}

	public static function imageResize($width, $height, $target)
	{
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		return array($width, $height);
	}

	public static function countFiles( $dir )
	{
		$total_file = 0;
		$total_dir = 0;

		if (is_dir($dir)) {
			$d = dir($dir);

			while (false !== ($entry = $d->read())) {
				if (substr($entry, 0, 1) != '.' && is_file($dir . '/' . $entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
					$total_file++;
				}
				if (substr($entry, 0, 1) != '.' && is_dir($dir . '/' . $entry)) {
					$total_dir++;
				}
			}

			$d->close();
		}

		return array ( $total_file, $total_dir );
	}

	public static function getAvatarDimension($avatar)
	{
		//resize the avatar image
		$avatar	= JPath::clean( JPATH_ROOT . '/' . $avatar );
		$info	= getimagesize($avatar);
		if(! $info === false)
		{
			$thumb	= DiscussImageHelper::imageResize($info[0], $info[1], 60);
		}
		else
		{
			$config = DiscussHelper::getConfig();
			$thumb  = array($config->get('layout_avatarthumbwidth', 60), $config->get('layout_avatarthumbheight', 60));
		}

		return $thumb;
	}

	public static function getAvatarRelativePath($type = 'profile')
	{
		$config			= DiscussHelper::getConfig();
		$avatar_config_path = '';

		switch($type)
		{
			case 'category':
				$avatar_config_path = $config->get('main_categoryavatarpath');
				break;
			case 'profile':
			default:
				$avatar_config_path = $config->get('main_avatarpath');
				break;
		}

		$avatar_config_path = rtrim($avatar_config_path, '/');
		//$avatar_config_path = str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		return $avatar_config_path;
	}

	public static function rel2abs($rel, $base)
	{
		/* return if already absolute URL */
		if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;


		/* queries and anchors */
		if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

		/* parse base URL and convert to local variables:
			$scheme, $host, $path */
		extract(parse_url($base));

		if( isset($path) )
		{
			/* remove non-directory element from path */
			$path = preg_replace('#/[^/]*$#', '', $path);

			/* destroy path if relative url points to root */
			if ($rel[0] == '/') $path = '';
		}
		else
		{
			$path = '';
		}

		/* dirty absolute URL */
		$abs = "$host$path/$rel";
		/* replace '//' or '/./' or '/foo/../' with '/' */
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

		/* absolute URL is ready! */
		return $scheme.'://'.$abs;
	}
}
