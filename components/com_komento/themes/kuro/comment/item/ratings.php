<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php if ($system->config->get('enable_ratings') && $row->ratings) { ?>
<script type="text/javascript">
Komento.require()
.script('komento.ratings')
.done(function($)
{
	$('[data-rating-item]').implement(Komento.Controller.Ratings.Item);
});
</script>
<div class="kmt-ratings-comment">
	<div class="kmt-ratings-stars" data-rating-item>
		<input type="radio" name="ratings" value="1" title="Very poor"<?php echo ($row->ratings == 1 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="2" title="Poor"<?php echo ($row->ratings == 2 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="3" title="Not that bad"<?php echo ($row->ratings == 3 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="4" title="Fair"<?php echo ($row->ratings == 4 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="5" title="Average"<?php echo ($row->ratings == 5 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="6" title="Almost good"<?php echo ($row->ratings == 6 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="7" title="Good"<?php echo ($row->ratings == 7 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="8" title="Very good"<?php echo ($row->ratings == 8 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="9" title="Excellent"<?php echo ($row->ratings == 9 ) ? ' checked="checked"' : '';?> />
		<input type="radio" name="ratings" value="10" title="Perfect"<?php echo ($row->ratings == 10 ) ? ' checked="checked"' : '';?> />
	</div>
	<span class="kmt-ratings-title">
		<?php echo JText::sprintf('COM_KOMENTO_RATING_RATED_TOTAL', $row->ratings / 2);?>
	</span>
</div>
<?php } ?>