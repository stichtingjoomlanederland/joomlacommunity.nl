<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewUser extends EasyDiscussAdminView
{
	public function insertBadge()
	{
		$userId	= JRequest::getInt( 'userId' );
		$id 	= JRequest::getInt( 'id' );
		$ajax	= DiscussHelper::getHelper( 'Ajax' );

		// This shouldn't even be happening at all.
		if( !$id || !$userId )
		{
			return $ajax->reject( JText::_( 'COM_EASYDISCUSS_INVALID_ID' ) );
		}

		$profile 	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $userId );

		if( !$profile->addBadge( $id ) )
		{
			return $ajax->reject( $profile->getError() );
		}

		$badge 	= DiscussHelper::getTable( 'Badges' );
		$badge->load( $id );

		$badgeUser 	= DiscussHelper::getTable( 'BadgesUsers' );
		$badgeUser->loadByUser( $id , $badge->id );

		$badge->reference_id 	= $badgeUser->id;
		$badge->custom 			= $badgeUser->custom;

		$user 		= JFactory::getUser( $userId );
		$this->set( 'badges' 	, array( $badge ) );
		$this->set( 'user'	, $user );
		$html = $this->loadTemplate( 'badge_item' );

		$ajax->resolve( $html );
	}

	public function saveMessage( $referenceId , $message )
	{
		$ajax 		= new Disjax();

		$badgeUser 	= DiscussHelper::getTable( 'BadgesUsers' );
		$badgeUser->load( $referenceId );
		$badgeUser->custom 	= $message;

		$badgeUser->store();

		ob_start();
		?>
		<p><?php echo JText::_( 'Custom message has been assigned to the badge.');?></p>
		<?php
		$content 	= ob_get_contents();
		ob_end_clean();

		$options			= new stdClass();
		$options->content	= $content;
		$options->title		= JText::_( 'Custom Message' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'Close' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();

	}

	public function customMessage( $id )
	{
		$ajax 				= new Disjax();
		$theme				= new DiscussThemes();

		$badgeUser 	= DiscussHelper::getTable( 'BadgesUsers' );
		$badgeUser->load( $id );

		ob_start();
		?>
		<p><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_CUSTOM_MESSAGE_DESC');?></p>

		<textarea id="customMessage" style="width:98%;height: 100px;" class="mt-20"><?php echo $badgeUser->custom;?></textarea>
		<?php
		$content 	= ob_get_contents();
		ob_end_clean();

		$options			= new stdClass();
		$options->content	= $content;
		$options->title		= JText::_( 'Custom Message' );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'Close' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button				= new stdClass();
		$button->title		= JText::_( 'Submit' );
		$button->action		= 'saveMessage("' . $id . '" );';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}

	public function deleteBadge()
	{
		$userId	= JRequest::getInt( 'userId' );
		$id 	= JRequest::getInt( 'id' );
		$ajax	= DiscussHelper::getHelper( 'Ajax' );

		// This shouldn't even be happening at all.
		if( !$id || !$userId )
		{
			return $ajax->reject( JText::_( 'COM_EASYDISCUSS_INVALID_ID' ) );
		}

		$badge 	= DiscussHelper::getTable( 'BadgesUsers' );
		$badge->loadByUser( $userId , $id );

		if( !$badge->delete() )
		{
			return $ajax->reject( $badge->getError() );
		}

		$ajax->resolve();
	}
}
