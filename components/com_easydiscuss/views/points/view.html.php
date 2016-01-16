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

require_once( DISCUSS_ROOT . '/views.php' );
require_once( DISCUSS_HELPERS . '/url.php' );

class EasyDiscussViewPoints extends EasyDiscussView
{
	/**
	 * Displays the user's points achievement history
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function history( $tmpl = null )
	{
		$app	= JFactory::getApplication();
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'Unable to locate the id of the user.' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( 'index.php?option=com_easydiscuss' );
			$app->close();
		}
		
		$model 		= DiscussHelper::getModel( 'Points' , true );
		$history	= $model->getPointsHistory( $id );

		foreach( $history as $item )
		{
			$date = DiscussDateHelper::dateWithOffSet( $item->created );
			$item->created = $date->toFormat( '%A, %b %e %Y' );

			$points = DiscussHelper::getHelper('Points')->getPoints( $item->command );

			if( $points )
			{
				if( $points[0]->rule_limit < 0 )
				{
					$item->class = 'badge-important';
					$item->points = $points[0]->rule_limit;
				}
				else
				{	
					$item->class = 'badge-info';
					$item->points = '+'.$points[0]->rule_limit;
				}
			}
			else
			{
				$item->class 	= 'badge-info';
				$item->points	= '+';
			}
		}

		$theme		= new DiscussThemes();

		$theme->set( 'history' , $history );
		echo $theme->fetch( 'points.history.php' );
	}

}