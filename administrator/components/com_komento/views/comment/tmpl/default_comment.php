<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>
<div class="content col-cell">
	<div class="row">
		<div class="col-md-6">
			<fieldset class="panel form-horizontal">
				<div class="panel-head"><?php echo JText::_('COM_KOMENTO_EDITING_COMMENT'); ?></div>
				<div class="panel-body">
						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_USERID'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="created_by" name="created_by" size="5" value="<?php echo $this->escape($this->comment->created_by);?>" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_NAME'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="name" name="name" size="45" value="<?php echo $this->escape($this->comment->name);?>" />
								<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_EMAIL'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="email" name="email" size="45" value="<?php echo $this->escape($this->comment->email);?>" />
								<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_WEBSITE'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="url" name="url" size="45" value="<?php echo $this->escape($this->comment->url);?>" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_COMPONENT'); ?>
							</label>
							<div class="col-md-7">
								<div class="kmt-component-select">
									<?php echo JHTML::_( 'select.genericlist' , $this->components , 'component' , 'class="inputbox"' , 'value' , 'text' , $this->escape($this->comment->component) ); ?>
								</div>
							</div>
						</div>


						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_ARTICLEID'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="cid" name="cid" size="45" value="<?php echo $this->escape($this->comment->cid);?>" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_TEXT'); ?>
							</label>
							<div class="col-md-7">
								<textarea id="comment" name="comment" class="inputbox" cols="50" rows="5"><?php echo $this->comment->comment;?></textarea>
								<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_( 'COM_KOMENTO_COMMENT_CREATED' ); ?>
							</label>
							<div class="col-md-7">
								<?php echo JHTML::_('calendar', $this->comment->created , "created", "created", '%Y-%m-%d %H:%M:%S', array('size'=>'30')); ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_( 'COM_KOMENTO_COMMENT_PUBLISHED' ); ?>
							</label>
							<div class="col-md-7">
								<?php echo $this->renderCheckbox( 'published' , $this->comment->published ); ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_( 'COM_KOMENTO_COMMENT_STICKED' ); ?>
							</label>
							<div class="col-md-7">
								<?php echo $this->renderCheckbox( 'sticked' , $this->comment->sticked ); ?>
							</div>
						</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset class="panel">
				<div class="panel-head"><?php echo JText::_('COM_KOMENTO_COMMENT_EXTENDED_DATA'); ?></div>
				<div class="panel-body">
						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_IP'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="ip" name="ip" size="45" value="<?php echo $this->escape($this->comment->ip);?>" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_LATITUDE'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="latitude" name="latitude" size="45" value="<?php echo $this->escape($this->comment->latitude);?>" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_LONGITUDE'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="longitude" name="longitude" size="45" value="<?php echo $this->escape($this->comment->longitude);?>" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-5 control-label">
								<?php echo JText::_('COM_KOMENTO_COMMENT_ADDRESS'); ?>
							</label>
							<div class="col-md-7">
								<input class="inputbox" type="text" id="address" name="address" size="45" value="<?php echo $this->escape($this->comment->address);?>" />
							</div>
						</div>
				</div>
			</fieldset>
		</div>
	</div>
</div>
