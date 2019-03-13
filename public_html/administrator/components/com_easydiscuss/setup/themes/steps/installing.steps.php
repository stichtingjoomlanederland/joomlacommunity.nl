<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<li class="pending" data-progress-sql>
	<div class="progress-icon">
		<i class="icon-radio-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_DB' );?>
	</div>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
</li>

<li class="pending" data-progress-admin>
	<div class="progress-icon">
		<i class="icon-radio-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_ADMIN');?>
	</div>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
</li>

<li class="pending" data-progress-site>
	<div class="progress-icon">
		<i class="icon-radio-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_SITE');?>
	</div>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
</li>

<li class="pending" data-progress-languages>
	<div class="progress-icon">
		<i class="icon-radio-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_LANGUAGES');?>
	</div>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
</li>

<li class="pending" data-progress-media>
	<div class="progress-icon">
		<i class="icon-radio-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_MEDIA');?>
	</div>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
</li>

<li class="pending" data-progress-syncdb>
	<div class="progress-icon">
		<i class="icon-radio-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_DB_SYNCHRONIZATION');?>
	</div>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
</li>

<li class="pending" data-progress-postinstall>
	<div class="progress-icon">
		<i class="icon-radio-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_POST_INSTALLATION_CLEANUP');?>
	</div>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
</li>