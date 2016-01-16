<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

// Name conflict with captcha table
class DiscussCaptchaClasses
{
	public static function getHTML()
	{
		$captcha			= DiscussHelper::getTable( 'Captcha' );
		$captcha->created	= DiscussHelper::getDate()->toMySQL();
		$captcha->store();

		$theme	= new DiscussThemes();
		$theme->set( 'id' , $captcha->id );
		return $theme->fetch( 'form.captcha.php' );
	}

	public function verify( $response , $id )
	{
		JTable::addIncludePath( DISCUSS_TABLES );
		$captcha	= DiscussHelper::getTable( 'Captcha' );
		$captcha->load( $id );

		if( empty( $captcha->response ) )
		{
			return false;
		}

		if( !$captcha->verify( $response ) )
		{
			return false;
		}

		return true;
	}

	public function getError( $ajax , $post )
	{
		$ajax->script( DiscussCaptcha::getReloadScript( $ajax, $post ) );
		// $ajax->script( 'eblog.comment.displayInlineMsg( "error" , "'.JText::_('COM_EASYBLOG_CAPTCHA_INVALID_RESPONSE').'");' );
		// $ajax->script( 'eblog.spinner.hide();' );
		// $ajax->script( "eblog.loader.doneLoading();" );
		return $ajax->send();
	}

	public function getReloadScript( $ajax , $captchaId )
	{
		JTable::addIncludePath( DISCUSS_TABLES );

		if( isset( $captchaId ) )
		{
			$ref	= DiscussHelper::getTable( 'Captcha' );
			$ref->load( $captchaId );
			$ref->delete();
		}

		//return 'eblog.captcha.reload();';
		return;
	}
}
