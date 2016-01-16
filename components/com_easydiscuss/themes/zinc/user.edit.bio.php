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
?>
<script type="text/javascript">
EasyDiscuss.require()
.script( 'legacy', 'bbcode' )
.library(
	'markitup',
	'expanding'
)
.done(function($) {
	$( '#signature' )
		.markItUp($.getEasyDiscussBBCodeSettings)
		.expandingTextarea();
	$( '#description' )
		.markItUp($.getEasyDiscussBBCodeSettings)
		.expandingTextarea();
});

</script>
<div class="tab-item user-bio">
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FULLNAME'); ?></label>
		<input type="text" value="<?php echo $this->escape( $user->name ); ?>" name="fullname" class="form-control">
	</div>

	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_NICKNAME'); ?></label>
		<input type="text" value="<?php echo $this->escape( $profile->nickname ); ?>" name="nickname" class="form-control">
	</div>

	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_DESCRIPTION'); ?></label>
		<textarea name="description" id="description" class="input full-width" rows="5"><?php echo $profile->description; ?></textarea>
	</div>

	<?php if( DiscussHelper::getHelper('ACL')->allowed('show_signature') ){ ?>
	<div class="form-group">
		<label>
			<?php echo JText::_('COM_EASYDISCUSS_PROFILE_SIGNATURE'); ?> 
			<span><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SIGNATURE_INFO'); ?></span>
		</label>
		<textarea name="signature" id="signature" class="form-control"><?php echo $profile->getSignature( true ); ?></textarea>
	</div>
	<?php } ?>

</div>
