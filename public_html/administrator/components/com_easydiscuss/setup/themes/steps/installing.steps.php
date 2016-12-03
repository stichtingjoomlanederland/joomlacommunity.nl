<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
	<b class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_DB' );?>
	</b>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
	<div class="notes"></div>
</li>
<li class="pending" data-progress-admin>
	<b class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_ADMIN');?>
	</b>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
	<div class="notes"></div>
</li>
<li class="pending" data-progress-site>
	<b class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_SITE');?>
	</b>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
	<div class="notes"></div>
</li>
<li class="pending" data-progress-languages>
	<b class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_LANGUAGES');?>
	</b>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
	<div class="notes"></div>
</li>
<li class="pending" data-progress-media>
	<b class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_MEDIA');?>
	</b>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
	<div class="notes"></div>
</li>
<li class="pending" data-progress-syncdb>
	<b class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING_DB_SYNCHRONIZATION');?>
	</b>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
	<div class="notes"></div>
</li>
<li class="pending" data-progress-postinstall>
	<b class="split__title">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_POST_INSTALLATION_CLEANUP');?>
	</b>
	<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INITIALIZING');?></span>
	<div class="notes"></div>
</li>
