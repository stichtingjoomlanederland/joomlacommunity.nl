<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';

class EasyDiscussControllerCaptcha extends EasyDiscussController
{
	/**
	 * Generates a random captcha image
	 *
	 **/
	function display($cachable = false, $urlparams = false)
	{

		// @TODO: Run some cleaning query here to clear the database.
		JTable::addIncludePath( DISCUSS_TABLES );

		$id			= JRequest::getInt( 'captcha-id' , '' );
		$captcha	= DiscussHelper::getTable( 'Captcha' );

		// clearing the oudated keys.
		$captcha->clear();

		// load the captcha records.
		$captcha->load( $id );

		if( !$captcha->id )
		{
			return false;
		}

		// @task: Generate a very random integer and take only 5 chars max.
		$hash	= JString::substr( md5( rand( 0, 9999 ) ) , 0 , 5 );
	    $captcha->response	= $hash;
		$captcha->store();

	    // Captcha width and height
	    $width	= 100;
	    $height = 20;

	    $image	= ImageCreate( $width , $height );
	    $white	= ImageColorAllocate($image, 255, 255, 255);
	    $black	= ImageColorAllocate($image, 0, 0, 0);
	    $gray	= ImageColorAllocate($image, 204, 204, 204);

	    ImageFill( $image , 0 , 0 , $white );
		ImageString( $image , 5 , 30 , 3 , $hash , $black );
		ImageRectangle( $image , 0 , 0 , $width - 1 , $height - 1 , $gray );
		imageline( $image , 0 , $height / 2 , $width , $height / 2 , $gray );
		imageline( $image , $width / 2 , 0 , $width / 2 , $height , $gray );

		header( 'Content-type: image/jpeg' );
	    ImageJpeg( $image );
	    ImageDestroy($image);
	    exit;
	}
}
