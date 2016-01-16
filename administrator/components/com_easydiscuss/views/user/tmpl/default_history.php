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
<div class="row-fluid ">
	<div class="span12">
		<h3><?php echo JText::_( 'User History' ); ?></h3>
		<hr />
		<ul class="user-history unstyled">
			<?php if( $this->history ){ ?>
				<?php foreach( $this->history as $history ){ ?>
					<li class="mb-10">
						<span><?php echo $history->created;?> - </span>
						<span><?php echo $history->title; ?></span>
					</li>
				<?php } ?>
			<?php } else { ?>
				<li>
					<div class="small"><?php echo JText::_( 'COM_EASYDISCUSS_NO_HISTORY_GENERATED_YET' );?></div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>