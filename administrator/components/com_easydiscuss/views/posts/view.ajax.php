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

class EasyDiscussViewPosts extends EasyDiscussAdminView
{
	public function showMoveDialog()
	{
		$ajax		= new Disjax();
		$options	= new stdClass();

		// Get list of categories.
		$categories	= DiscussHelper::populateCategories( '' , '' , 'select' , 'new_category', '' , true, true , true , true );

		$options->title = JText::_( 'COM_EASYDISCUSS_DIALOG_MOVE_TITLE' );

		$action		= DiscussHelper::getJoomlaVersion() >= '1.6' ? 'Joomla.submitbutton(\'movePosts\');' : 'submitbutton(\'movePosts\')';

		ob_start();
		?>
		<div style="display:none;" id="new_category_error" class="alert alert-error"></div>
		<p><?php echo JText::_( 'COM_EASYDISCUSS_DIALOG_MOVE_CONTENT' ); ?></p>
		<p>
			<?php echo $categories; ?>
		</p>
		<?php
		$options->content = ob_get_clean();

		$buttons 			= array();

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_CANCEL_BUTTON' );
		$button->action 	= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_MOVE_BUTTON' );
		$button->action 	= $action;
		$button->className 	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons 	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}

	public function showApproveDialog( $id )
	{
		$ajax		= new Disjax();
		$options	= new stdClass();

		$options->title = JText::_( 'COM_EASYDISCUSS_DIALOG_MODERATE_TITLE' );

		ob_start();
		?>

		<p><?php echo JText::_( 'COM_EASYDISCUSS_DIALOG_MODERATE_CONTENT' ); ?></p>
		<form id="moderate-form" name="moderate" method="post">
			<span class="float-r" id="dialog_loading"></span>
			<input type="hidden" name="option" value="com_easydiscuss" />
			<input type="hidden" name="controller" value="posts" />
			<input type="hidden" name="cid[]" value="<?php echo $id; ?>" />
			<input type="hidden" id="moderate-task" name="task" value="" />
		</form>
		<?php
		$options->content = ob_get_clean();

		$buttons 			= array();

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_REJECT_BUTTON' );
		$button->action 	= 'admin.post.moderate.unpublish();';
		$buttons[]			= $button;

		$button 			= new stdClass();
		$button->title 		= JText::_( 'COM_EASYDISCUSS_APPROVE_BUTTON' );
		$button->action 	= 'admin.post.moderate.publish();';
		$button->className 	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons 	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}
}
