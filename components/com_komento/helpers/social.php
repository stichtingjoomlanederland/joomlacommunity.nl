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

class KomentoSocialHelper
{
	public static function shortenUrl( $link )
	{
		if( !stristr( $link, rtrim( JURI::root() , '/' ) ) )
		{
			$link = rtrim( JURI::root() , '/' ) . '/' . ltrim( $link, '/' );
		}

		if( function_exists( 'curl_init' ) )
		{
			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, "https://www.googleapis.com/urlshortener/v1/url");
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode(array("longUrl"=>$link)));
			curl_setopt($ch,CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

			$result = curl_exec($ch);

			if( $result )
			{
				curl_close($ch);

				$result = json_decode($result, true);

				if( array_key_exists( 'id', $result ) )
				{
					return $result['id'];
				}
				else
				{
					return $link;
				}
			}
			else
			{
				return $link;
			}
		}
		else
		{
			return $link;
		}
	}
}
