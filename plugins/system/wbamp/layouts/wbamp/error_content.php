<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

// no direct access
defined('_JEXEC') or die;

?>
<div class="wbamp-container wbamp-content wbamp-error">
	<?php if (!empty($displayData['error_title'])) : ?>
		<div class="wbamp-container wbamp-error-title">
			<?php echo $displayData['error_title']; ?>
		</div>
	<?php endif; ?>

	<div class="wbamp-container wbamp-error-body">
		<?php if(WbampHelper_Edition::$id == 'full') : ?>
		<div class="wbamp-container wbamp-error-image">
		</div>
		<?php endif; ?>
		<?php echo $displayData['error_body']; ?>
	</div>
	<?php if (!empty($displayData['error_footer'])) : ?>
		<div class="wbamp-container wbamp-error-footer">
			<?php echo $displayData['error_footer']; ?>
		</div>
	<?php endif; ?>
</div>
