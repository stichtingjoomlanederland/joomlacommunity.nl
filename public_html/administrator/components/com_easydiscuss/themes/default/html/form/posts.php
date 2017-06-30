<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row" data-form-post-wrapper>
	<div class="col-lg-12">
		<div class="input-group">
			<input type="text" id="<?php echo $id;?>-placeholder" class="form-control" value="<?php echo $title;?>" disabled="disabled" />
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" data-form-remove-post>
					<i class="fa fa-times"></i>
				</button>
				<button class="btn btn-default" type="button" data-form-browse-posts>
					<i class="fa fa-folder-o"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_SELECT_POST_BUTTON');?>
				</button>
			</span>
		</div>
		<input type="hidden" name="<?php echo $name;?>" id="<?php echo $id;?>" value="<?php echo $selected;?>" />
	</div>
</div>

<script type="text/javascript">
ed.require(['edq'], function($) {

	window.insertPost = function(id, name) {
		$('#<?php echo $id;?>-placeholder').val(name);
		$('#<?php echo $id;?>').val(id);

		EasyDiscuss.dialog().close();
	}

	$('[data-form-remove-post]').on('click', function() {
		var button = $(this);
		var parent = button.parents('[data-form-post-wrapper]');

		// Reset the form
		parent.find('input[type=hidden]').val('');
		parent.find('input[type=text]').val('');
	});

	$('[data-form-browse-posts]').on('click', function() {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('admin/views/subscription/browse')
		});
	});

});

</script>