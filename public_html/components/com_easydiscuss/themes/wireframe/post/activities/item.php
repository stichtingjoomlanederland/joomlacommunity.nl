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
$contents = $log->getMessage();
?>
<?php if ($contents) { ?>
<div class="ed-timeline__item">
	<div class="ed-timeline-log">
		<div class="t-d--flex t-align-items--c">
			<div class="ed-timeline-log__media">
				<div class="ed-timeline-log__icon">
					<i class="fas <?php echo $log->getIcon(); ?> fa-fw t-text--500"></i>
				</div>
			</div>
			<div>
				<?php echo $contents; ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>