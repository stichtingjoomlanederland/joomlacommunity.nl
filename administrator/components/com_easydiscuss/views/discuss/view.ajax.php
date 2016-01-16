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

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewDiscuss extends EasyDiscussAdminView
{
	function getUpdates()
	{
		$version	= DiscussHelper::getVersion();
		$local		= DiscussHelper::getLocalVersion();

		// Test build only since build will always be incremented regardless of version
		$localVersion	= explode( '.' , $local );
		$localBuild		= $localVersion[2];


		if( !$version )
		{
			return JText::_('COM_EASYDISCUSS_UNABLE_TO_UPDATE_SERVER');
		}

		$remoteVersion	= explode( '.' , $version );
		$build			= $remoteVersion[ 2 ];

		$content = JText::sprintf('COM_EASYDISCUSS_VERSION_OLDER', $local);
		$content .= JText::sprintf('COM_EASYDISCUSS_VERSION_GET_LATEST_VERSION', $version);
		$state = 'outdated';

		if( $localBuild >= $build )
		{
			$content = JText::sprintf('COM_EASYDISCUSS_VERSION_LATEST', $local);
			$state = 'latest';
		}

		$ajax 	= DiscussHelper::getHelper( 'Ajax' );
		$ajax->resolve( $state, $content, $local, $version );
	}

	public function getnews()
	{
		$ajax 	= DiscussHelper::getHelper( 'Ajax' );
		$news	= DiscussHelper::getRecentNews();

		ob_start();

		if( !$news )
		{
			echo '<li class="empty">' . JText::_( 'COM_EASYDISCUSS_NO_NEWS_ITEM' ) . '</li>';
		}
		else
		{
			foreach( $news as $newsItem )
			{
				$dates	= explode( '/' , $newsItem->date );
			?>
			<li>
				<span class="updates-news">
					<a href="javascript:void(0);"><?php echo $newsItem->title; ?></a>
					<span><?php echo $newsItem->desc;?></span>
				</span>
				<span class="si-date"><span><?php echo $dates[0];?></span>may</span>
				<span class="clear"></span>
			</li>
			<?php
			}
		}

		$output = ob_get_contents();
		ob_end_clean();

		$ajax->success( $output );
	}
}
