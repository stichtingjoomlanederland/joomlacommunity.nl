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

jimport('joomla.filesystem.file' );
jimport('joomla.filesystem.folder' );

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

class DiscussFeedsHelper
{
	function addHeaders( $feedUrl )
	{
		$config		= DiscussHelper::getConfig();
		$document	= JFactory::getDocument();

		// If rss is disabled or the current view type is not of html, do not add the headers
		if( !$config->get('main_rss') || $document->getType() != 'html' )
		{
			return false;
		}

		$enabled	= $config->get( 'main_feedburner' );
		$url		= $config->get( 'main_feedburner_url' );

		require_once DISCUSS_HELPERS . '/router.php';
		$sef  = DiscussRouter::isSefEnabled();
		$concat		= $sef ? '?' : '&';

		if( $enabled && !empty( $url ) )
		{
			$document->addHeadLink( $url , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			return;
		}

		// Add default rss feed link
		$document->addHeadLink( DiscussRouter::_( $feedUrl ) . $concat . 'format=feed&type=rss' , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
		$document->addHeadLink( DiscussRouter::_( $feedUrl ) . $concat . 'format=feed&type=atom' , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
	}

	public function getFeedURL( $url , $atom = false)
	{
		require_once DISCUSS_HELPERS . '/router.php';
		$sef  		= DiscussRouter::isSefEnabled();
		$join		= $sef ? '?' : '&';
		$url		= DiscussRouter::_( $url ) . $join . 'format=feed';
		$url		.= $atom ? '&type=atom' : '&type=rss';

		return $url;
	}
}
