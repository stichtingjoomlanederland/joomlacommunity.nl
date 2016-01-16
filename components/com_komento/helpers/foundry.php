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

class KomentoFoundryHelper
{
	/**
	 * This renders the necessary bootstrap data into the html headers.
	 */
	public function bootstrap()
	{
		static $isRendered	= false;

		$doc 				= JFactory::getDocument();

		if( !$isRendered && $doc->getType() == 'html' )
		{
			// @task: Include dependencies from foundry.
			require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'foundry' . DIRECTORY_SEPARATOR . '3.1' . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . 'configuration.php' );

			$config = Komento::getConfig();

			$environment = JRequest::getVar( 'komento_environment' , $config->get( 'komento_environment' ) );

			$folder	= 'scripts';

			// @task: Let's see if we should load the dev scripts.
			if( $environment == 'development' )
			{
				$folder		= 'scripts_';
			}

			$doc->addScript( rtrim( JURI::root() , '/' ) . '/media/com_komento/' . $folder . '/abstract.js' );

			$isRendered		= true;
		}

		return $isRendered;
	}
}
