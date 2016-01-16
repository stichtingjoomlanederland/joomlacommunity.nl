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

/************************************************************************************************

DEVELOPER'S NOTE - To integrate com_komento to yours, simply refer to the follwing samples:

*************************************************************************************************

2 LINES SIMPLE VERSION:

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php' );
Komento::commentify( 'com_yourextension', $content, array( 'params' => '') );

************************************************************************************************/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Komento Content Plugin
 *
 */
class plgContentKomento extends JPlugin
{
	private $extension = null;

	/**
	 * com_redshop
	 */
	public function onAfterDisplayProduct( &$template_desc, &$params, $data )
	{
		return $this->execute( __FUNCTION__, null, $template_desc, $params, $data );
	}

	/**
	 * com_k2
	 */
	public function onK2CommentsBlock( &$item, &$params, $limitstart )
	{
		return $this->execute( __FUNCTION__, 'k2block', $item, $params, $limitstart );
	}

	public function onK2CommentsCounter( &$item, &$params, $limitstart )
	{
		$this->extension = 'com_k2';
		return $this->execute( __FUNCTION__, 'k2counter', $item, $params, $limitstart );
	}

	public function onK2BeforeDisplayContent(&$item, &$params, $limitstart)
	{
		$this->extension = 'com_k2';
		return $this->execute( __FUNCTION__, 'k2counter', $item, $params, $limitstart );
	}

	/**
	 * com_easyblog
	 */
	public function onDisplayComments( &$blog , &$articleParams )
	{
		return $this->execute( __FUNCTION__, null, $blog, $articleParams, 0 );
	}

	/**
	 * com_content, com_flexicontent, com_virtuemart, com_ohanah
	 */
	public function onContentAfterDisplay( $context, &$article, &$params, $page = 0 )
	{
		return $this->execute( __FUNCTION__, $context, $article, $params, $page );
	}

	public function onAfterDisplayContent( &$article, &$articleParams, $limitstart, $page = 0 )
	{
		return $this->execute( __FUNCTION__, null, $article, $params, $page );
	}

	/**
	 * com_tz_portfolio
	 *
	 */
	public function onTZPortfolioCommentDisplay($context, &$article, $params)
	{
		return $this->execute(__FUNCTION__, $context, $article, $params);
	}

	/**
	 * com_jblance
	 *
	 */
	public function onJBlanceCommentDisplay($context, &$article, $params)
	{
		return $this->execute(__FUNCTION__, $context, $article, $params);
	}

	/**
	 * com_jdownloads
	 */
	public function onContentPrepare( $context, &$article, &$params, $page = 0 )
	{
		return $this->execute( __FUNCTION__, $context, $article, $params, $page );
	}

	public function onPrepareContent( &$article, &$params, $limitstart, $page = 0 )
	{
		return $this->execute( __FUNCTION__, null, $article, $params, $page );
	}

	private function execute( $eventTrigger, $context = 'none', &$article, &$params, $page = 0 )
	{
		static $bootstrap;

		// @task: load bootstrap
		if( !$bootstrap )
		{
			$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php';

			jimport('joomla.filesystem.file');

			if( !JFile::exists( $file ) )
			{
				// missing bootstrap
				return false;
			}

			require_once( $file );
			$bootstrap = true;
		}

		if( !$this->extension )
		{
			$this->extension = JRequest::getCmd( 'option' );
		}

		// Fix flexicontent's mess as they are trying to reset the option=com_flexicontent to com_content.
		if( JRequest::getVar( 'isflexicontent') )
		{
			$this->extension 	= 'com_flexicontent';
		}


		// @task: trigger onAfterEventTriggered
		if( !$result = Komento::onAfterEventTriggered( __CLASS__, $eventTrigger, $this->extension, $context, $article, $params ) )
		{
			return false;
		}

		// Passing in the data
		$options			= array();
		$options['trigger']	= $eventTrigger;
		$options['context']	= $context;
		$options['params']	= $params;
		$options['page']	= $page;

		// Ready to Commentify!
		return Komento::commentify( $this->extension, $article, $options );
	}
}
