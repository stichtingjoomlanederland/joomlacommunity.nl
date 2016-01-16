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
?>
<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<!-- left -->
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_INTEGRATIONS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_TOOLBAR' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_TOOLBAR_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_TOOLBAR' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_jomsocial_toolbar' , $this->config->get( 'integration_jomsocial_toolbar' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_new_question' , $this->config->get( 'integration_jomsocial_activity_new_question' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_CONTENT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_CONTENT_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_CONTENT' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_new_question_content' , $this->config->get( 'integration_jomsocial_activity_new_question_content' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_reply_question' , $this->config->get( 'integration_jomsocial_activity_reply_question' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_CONTENT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_CONTENT_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_CONTENT' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_reply_question_content' , $this->config->get( 'integration_jomsocial_activity_reply_question_content' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_CONTENT_LENGTH' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" size="5" value="<?php echo $this->config->get( 'integration_jomsocial_activity_content_length' );?>" />
						<?php echo JText::_( 'COM_EASYDISCUSS_CHARACTERS' ); ?>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_USERPOINTS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_USERPOINTS_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_USERPOINTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_jomsocial_points' , $this->config->get( 'integration_jomsocial_points' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_QUESTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_QUESTION_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_QUESTION' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_jomsocial_activity_likes' , $this->config->get( 'integration_jomsocial_activity_likes' ) );?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">&nbsp;</td>
	</tr>
</table>
