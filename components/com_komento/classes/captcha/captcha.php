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

class KomentoCaptcha extends JObject
{
	public function getHTML()
	{
		$captcha			= Komento::getTable( 'Captcha', 'KomentoTable' );
		$captcha->created	= Komento::getDate()->toMySQL();
		$captcha->store();

		$theme		= Komento::getTheme();
		$theme->set( 'id' , $captcha->id );
		$theme->set( 'url', $this->getCaptchaUrl( $captcha->id ) );
		return $theme->fetch( 'comment/captcha.php' );
	}

	public function verify( $data, $params = array() )
	{
		if (!array_key_exists('captcha-response', $data) || !array_key_exists('captcha-id', $data) )
		{
			return false;
		}

		$captcha	= Komento::getTable( 'Captcha', 'KomentoTable' );
		$captcha->load( $data['captcha-id'] );

		if( empty( $captcha->response ) || !$captcha->verify($data['captcha-response']) )
		{
			$this->setError( JText::_('COM_KOMENTO_CAPTCHA_INVALID_RESPONSE') );
			return false;
		}

		return true;
	}

	public function getReloadSyntax()
	{
		if ($currentId = JRequest::getInt( 'captcha-id' ))
		{
			$ref	= Komento::getTable( 'Captcha' , 'KomentoTable' );
			$ref->load( $currentId );
			$ref->delete();
		}

		// Regenerate a new captcha object
		$captcha	= Komento::getTable( 'Captcha' , 'KomentoTable' );
		$captcha->created	= Komento::getDate()->toMySQL();
		$captcha->store();

		$url	= $this->getCaptchaUrl( $captcha->id );

		$reloadData = array(
			'image'	=> $url,
			'id'	=> $captcha->id
		);

		return $reloadData;
	}

	public function getCaptchaUrl( $id )
	{
		$base = 'index.php?option=com_komento&controller=captcha&captcha-id=' . $id . '&tmpl=component';

		$url = JURI::root() . $base;

		return $url;
	}
}
