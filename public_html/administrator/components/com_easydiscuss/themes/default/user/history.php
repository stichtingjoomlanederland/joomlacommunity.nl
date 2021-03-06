<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="history" class="tab-pane">
	<div class="row">
		<div class="col-md-10">
			<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_USER_HISTORY'); ?>
				<div class="panel-body">
					<div class="">
						<div class="o-form-group">
							<div class="">
								<ul class="user-history unstyled">
									<?php if( $history ){ ?>
										<?php foreach( $history as $history ){ ?>
											<li class="t-mb--md">
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
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
