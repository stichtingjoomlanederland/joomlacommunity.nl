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
<div class="t-min-width--0">
	<a href="<?php echo $category->getPermalink();?>"
		data-ed-popbox="ajax://site/views/popbox/category"
		data-ed-popbox-position="bottom-left"
		data-ed-popbox-toggle="hover"
		data-ed-popbox-offset="4"
		data-ed-popbox-type="ed-category"
		data-ed-popbox-component="o-popbox--category"
		data-ed-popbox-cache="1"
		data-args-id="<?php echo $category->id; ?>"
		class="t-d--flex t-align-items--c si-link"
	>
		<?php echo $this->html('category.identifier', $category, 'sm'); ?>
		&nbsp;
		<span class="t-text--truncate" style="max-width: 180px">
			<?php echo $category->getTitle(50);?>
		</span>
	</a>
</div>