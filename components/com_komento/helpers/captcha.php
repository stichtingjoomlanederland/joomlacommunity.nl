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

class KomentoCaptchaHelper extends JObject
{
	static $instance = null;

	/**
	 * Get the captcha class
	 */
	public function getInstance()
	{
		if (is_null(self::$instance))
		{
			$config	= Komento::getConfig();
			$class	= '';

			if( $config->get( 'antispam_captcha_enable' ) == 1 )
			{
				if( $config->get( 'antispam_captcha_type') == 0)
				{
					$class = 'captcha';
				}
				else
				{
					if( $config->get( 'antispam_captcha_type') == 1 && $config->get('antispam_recaptcha_public_key') && $config->get('antispam_recaptcha_private_key') )
					{
						$class = 'recaptcha';
					}
				}
			}

			if( empty($class) )
			{
				self::$instance = false;
				return false;
			}

			$file = KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . strtolower($class) . '.php';

			if( !JFile::exists($file) )
			{
				self::$instance = false;
				return false;
			}

			require_once( $file );

			$class = 'Komento' . ucfirst( strtolower($class) );

			if ( !class_exists($class) )
			{
				return false;
			}

			self::$instance = new $class();
		}

		return self::$instance;
	}

	/**
	 * Retrieves the html codes for the ratings.
	 *
	 **/
	public function getHTML()
	{
		return self::$instance->getHTML();
	}

	/**
	 * Verify the captcha input
	 */
	public function verify( $data, $params = array() )
	{
		return self::$instance->verify( $data );
	}

	/**
	 * Return error message
	 * @return	string	The json output for ajax calls
	 **/
	public function getError( $i = null, $toString = true )
	{
		return self::$instance->getError();
	}

	/**
	 * Reloads the captcha image.
	 */
	public function getReloadSyntax()
	{
		return self::$instance->getReloadSyntax();
	}
}
