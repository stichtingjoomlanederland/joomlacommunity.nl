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
<div id="ed" class="ed-mod ed-mod--similar-discussions <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<?php if ($posts) { ?>
				<?php foreach ($posts as $post) { ?>
					<div class="o-card t-bg--100">
						<div class="o-card__body l-stack">
							<?php 
							$maxLength = 50;
							$title = (EDJString::strlen($post->title) > $maxLength) ? substr($post->title, 0, $maxLength) . '...' : $post->title;
							?>

							<a href="<?php echo $post->permalink; ?>" class="o-title si-link t-d--inline-block l-spaces--sm"><?php echo $title; ?></a>
							
							<div class="o-meta t-flex-grow--1 l-cluster">
								<div class="">
									<div class="">
										<a href="<?php echo EDR::getCategoryRoute($post->category_id); ?>"><?php echo $post->category_name; ?></a>
									</div>

									<div class="">
										<?php echo $post->duration; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } else { ?>
				<div class="o-card t-bg--100">
					<div class="o-card__body l-stack">
						<?php echo JText::_('MOD_EASYDISCUSS_SIMILAR_DISCUSSIONS_NO_ENTRIES'); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>