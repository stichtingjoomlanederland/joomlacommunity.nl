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

class DiscussCaptchaHelper
{
	/**
	 * Retrieves the html codes for the ratings.
	 *
	 * @param	int	$uid	The unique id for the item that is being rated
	 * @param	string	$type	The unique type for the item that is being rated
	 * @param	string	$command	A textual representation to request user to vote for this item.
	 * @param	string	$element	A dom element id.
	 **/
	public function getHTML()
	{
		$config		= DiscussHelper::getConfig();
		$my			= JFactory::getUser();

		require_once DISCUSS_CLASSES . DIRECTORY_SEPARATOR . 'captcha.php';

		// Name conflict with captcha table
		return DiscussCaptchaClasses::getHTML();
	}

	public function verify( $discussCaptcha )
	{
		$config		= DiscussHelper::getConfig();
		$output		= '';
		$my			= JFactory::getUser();

		if( !$config->get( 'antispam_easydiscuss_captcha_registered' ) && $my->id > 0 )
		{
			return true;
		}

		// @task: If recaptcha is not enabled, we assume that the built in captcha is used.
		require_once DISCUSS_CLASSES . DIRECTORY_SEPARATOR . 'captcha.php';

		if( !isset( $discussCaptcha->captchaResponse ) || !isset( $discussCaptcha->captchaId ) )
		{
			return false;
		}

		return DiscussCaptchaClasses::verify( $discussCaptcha->captchaResponse , $discussCaptcha->captchaId );
	}

	/**
	 * Throws error message and reloads the captcha image.
	 * @param	Ejax	$ejax	Ejax object
	 * @return	string	The json output for ajax calls
	 **/
	public function getError( $ajax , $post )
	{
		// $config		= DiscussHelper::getConfig();
		// $adapters	= DISCUSS_CLASSES . DIRECTORY_SEPARATOR . 'captcha';

		// if( $config->get( 'comment_recaptcha' ) && $config->get('comment_recaptcha') && $config->get('comment_recaptcha_public') )
		// {
		// 	require_once( $adapters . DIRECTORY_SEPARATOR . 'recaptcha.php' );
		// 	return EasyBlogRecaptcha::getError( $ajax , $post );
		// }

		// require_once( $adapters . DIRECTORY_SEPARATOR . 'captcha.php' );
		// return EasyBlogCaptcha::getError( $ajax , $post );
	}

	/**
	 * Reload the captcha image.
	 * @param	Ejax	$ejax	Ejax object
	 * @return	string	The javascript action to reload the image.
	 **/
	public function reload( $ajax , $captchaId )
	{
		$config		= DiscussHelper::getConfig();

		// If no captcha is enabled, ignore it.
		if( !$config->get('antispam_easydiscuss_captcha_registered') || !$config->get( 'antispam_easydiscuss_captcha' ) )
		{
			return true;
		}

		// @task: If recaptcha is not enabled, we assume that the built in captcha is used.
		// Generate a new captcha 
		if( isset( $captchaId ) )
		{
			$ref	= DiscussHelper::getTable( 'Captcha' );
			$ref->load( $captchaId );
			$ref->delete();
		}

		require_once DISCUSS_CLASSES . DIRECTORY_SEPARATOR . 'captcha.php';
		$ajax->script( DiscussCaptchaClasses::getReloadScript( $ajax , $captchaId ) );
		return true;
	}

	public function showCaptcha()
	{
		$config = DiscussHelper::getConfig();
		$my = JFactory::getUser();
		$runCaptcha = false;

		if( $config->get( 'antispam_easydiscuss_captcha' ) )
		{
			// Check to see if user is guest or registered
			if( empty($my->id) )
			{
				// If is guest
				$runCaptcha = true;
			}
			else
			{
				//If not guest, check the settings
				if( $config->get( 'antispam_easydiscuss_captcha_registered' ) )
				{
					$runCaptcha = true;
				}
			}
		}

		return $runCaptcha;
	}
}
