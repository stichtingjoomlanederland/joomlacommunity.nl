<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h2 class="ed-page-title"><?php echo $title; ?></h2>

<?php if ($badges) { ?>
	<div class="ed-badges">
	<?php foreach ($badges as $badge) { ?>
		<div class="ed-badges__item"
			<?php if ($badge->achieved()) { ?>
			data-placement="top"
			data-original-title="<?php echo JText::_('COM_EASYDISCUSS_ACHIEVED_BADGE');?>"
			data-ed-provide="tooltip"
			<?php } ?>
		>
			<div class="ed-badge">
				<div class="o-media ed-badge--flag o-media--top">
					<div class="o-media__image">
						<a href="<?php echo EDR::_('view=badges&layout=listings&id=' . $badge->id);?>" class="o-avatar">
							<img src="<?php echo $badge->getAvatar();?>"/>
						</a>
					</div>
					<div class="o-media__body">
						<a href="<?php echo EDR::_('view=badges&layout=listings&id=' . $badge->id);?>" class="ed-badge__name"><?php echo JText::_($badge->title);?></a>
						<div class="ed-badge__meta t-lg-mb--md t-lg-mt--md small"><?php echo $badge->get('description');?></div>
						<a href="<?php echo EDR::_('view=badges&layout=listings&id=' . $badge->id); ?>" class="ed-badge__rank t-lg-mb--sm"><?php echo JText::sprintf('COM_EASYDISCUSS_BADGE_TOTAL_ACHIEVERS', $badge->getTotalAchievers());?></a>
					</div>
				</div>
				<div class="ed-badge__indicator">
					<i class="fa fa-trophy <?php echo $badge->achieved($user) ? ' ' : '';?>"
					></i>
				</div>
			</div>    
		</div>
	<?php } ?>
	</div>
<?php } else { ?>
	<div class="is-empty">
		<div class="o-empty o-empty--bordered">
			<div class="o-empty__content">
				<i class="o-empty__icon fa fa-book"></i>
				<div class="o-empty__text">
					<?php echo JText::_('COM_EASYDISCUSS_NO_BADGES_CREATED'); ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
