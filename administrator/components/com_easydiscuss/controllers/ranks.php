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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';
require_once DISCUSS_HELPERS . '/filter.php';

class EasyDiscussControllerRanks extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'publish' , 'unpublish' );
	}

	public function save()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		$mainframe	= JFactory::getApplication();

		$post		= JRequest::get( 'post' );
		$ids		= isset($post['id']) ? $post['id'] : '';
		$starts		= isset($post['start']) ? $post['start'] : '';
		$ends		= isset($post['end']) ? $post['end'] : '';
		$titles		= isset($post['title']) ? $post['title'] : '';
		$removal	= isset($post['itemRemove']) ? $post['itemRemove'] : '';


		$model	= DiscussHelper::getModel( 'Ranks' , true );
		if( !empty( $removal ) )
		{
			$rids	= explode(',', $removal);
			$model->removeRanks($rids);
		}

		if( !empty($ids))
		{
			if( count($ids) > 0)
			{
				for($i = 0; $i < count( $ids ); $i++ )
				{
					$data			= array();
					$data['id']		= $ids[$i];
					$data['start']	= $starts[$i];
					$data['end']	= $ends[$i];
					$data['title']	= $titles[$i];

					$ranks	= DiscussHelper::getTable( 'Ranks' );
					$ranks->bind($data);
					$ranks->store();

				}
			}//end if
		}//end if

		$message	= JText::_('COM_EASYDISCUSS_RANKING_SUCCESSFULLY_UPDATED');

		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=ranks' );
	}
}
