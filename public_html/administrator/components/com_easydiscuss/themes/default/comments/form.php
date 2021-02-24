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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_ED_COMMENTS_EDIT_COMMENT_MSG'); ?>

				<div class="panel-body">
					<div class="o-form-group">
						<textarea name="comment" rows="5" class="o-form-control" cols="35" data-comment-editor><?php echo $this->html('string.escape',  $comment->comment);?></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
		</div>
	</div>

	<?php echo $this->html('form.token'); ?>

	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="comments" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="id" value="<?php echo $comment->id;?>" />
</form>