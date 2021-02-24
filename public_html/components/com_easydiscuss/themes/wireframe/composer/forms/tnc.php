<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="t-flex-grow--1" data-ed-tnc>
	<div class="o- t-lg-mb--lg">
		<div class="o-form-check t-mr--md">
			<input type="checkbox" 
				name="tnc-<?php echo $type;?>" 
				id="tnc-<?php echo $type;?>" 
				class="o-form-check-input" 
				data-ed-tnc-checkbox
				<?php echo ED::tnc()->hasAcceptedTnc($type) ? 'checked="checked"' : '' ?>
			/>

			<label for="tnc-<?php echo $type;?>" class="o-form-check-label">
				<?php echo JText::_('COM_EASYDISCUSS_I_HAVE_READ_AND_AGREED');?> 
				<a href="javascript:void(0);" style="text-decoration: underline;" data-ed-tnc-preview>
					<?php echo JText::_('COM_EASYDISCUSS_TERMS_AND_CONDITIONS');?>
				</a>  
			</label>
		</div>
	</div>
</div>