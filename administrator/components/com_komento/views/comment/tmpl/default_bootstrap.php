<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>
<div class="row-fluid">
	<div class="span12">
		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_('COM_KOMENTO_EDITING_COMMENT'); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_USERID'); ?></span>
					</td>
					<td>
						<input class="inputbox" type="text" id="created_by" name="created_by" size="5" value="<?php echo $this->escape($this->comment->created_by);?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_NAME'); ?></span>
					</td>
					<td>
						<input class="inputbox" type="text" id="name" name="name" size="45" value="<?php echo $this->escape($this->comment->name);?>" />
						<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_EMAIL'); ?></span>
					</td>
					<td valign="top">
						<input class="inputbox" type="text" id="email" name="email" size="45" value="<?php echo $this->escape($this->comment->email);?>" />
						<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_WEBSITE'); ?></span>
					</td>
					<td valign="top">
						<input class="inputbox" type="text" id="url" name="url" size="45" value="<?php echo $this->escape($this->comment->url);?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_COMPONENT'); ?></span>
					</td>
					<td valign="top">
						<div class="kmt-component-select">
							<?php echo JHTML::_( 'select.genericlist' , $this->components , 'component' , 'class="inputbox"' , 'value' , 'text' , $this->escape($this->comment->component) ); ?>
						</div>
					</td>
				</tr>


				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_ARTICLEID'); ?></span>
					</td>
					<td valign="top">
						<input class="inputbox" type="text" id="cid" name="cid" size="45" value="<?php echo $this->escape($this->comment->cid);?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_TEXT'); ?></span>
					</td>
					<td valign="top">
						<textarea id="comment" name="comment" class="inputbox" cols="50" rows="5"><?php echo $this->comment->comment;?></textarea>
						<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_( 'COM_KOMENTO_COMMENT_CREATED' ); ?></span>
					</td>
					<td><?php echo JHTML::_('calendar', $this->comment->created , "created", "created", '%Y-%m-%d %H:%M:%S', array('size'=>'30')); ?></td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_( 'COM_KOMENTO_COMMENT_PUBLISHED' ); ?></span>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'published' , $this->comment->published ); ?>
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_( 'COM_KOMENTO_COMMENT_STICKED' ); ?></span>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'sticked' , $this->comment->sticked ); ?>
					</td>
				</tr>
			</table>
			</fieldset>
		</div>
		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_('COM_KOMENTO_COMMENT_EXTENDED_DATA'); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_IP'); ?></span>
					</td>
					<td>
						<input class="inputbox" type="text" id="ip" name="ip" size="45" value="<?php echo $this->escape($this->comment->ip);?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_LATITUDE'); ?></span>
					</td>
					<td>
						<input class="inputbox" type="text" id="latitude" name="latitude" size="45" value="<?php echo $this->escape($this->comment->latitude);?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_LONGITUDE'); ?></span>
					</td>
					<td>
						<input class="inputbox" type="text" id="longitude" name="longitude" size="45" value="<?php echo $this->escape($this->comment->longitude);?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_ADDRESS'); ?></span>
					</td>
					<td>
						<input class="inputbox" type="text" id="address" name="address" size="45" value="<?php echo $this->escape($this->comment->address);?>" />
					</td>
				</tr>
			</table>
			</fieldset>
		</div>
	</div>
</div>
