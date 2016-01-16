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

<script type="text/javascript">
Komento.require()
.script('komento.ratings')
.done(function($)
{
	$('[data-rating-form]').implement(Komento.Controller.Ratings.Form);
});
</script>
<div class="kmt-form-ratings">
	<label><?php echo JText::_('COM_KOMENTO_RATING_THIS_ITEM'); ?></label>
	<div class="kmt-ratings-stars" data-rating-form>
		<input type="radio" name="ratings" value="1" title="Very poor" class="odd" />
		<input type="radio" name="ratings" value="2" title="Poor" />
		<input type="radio" name="ratings" value="3" title="Not that bad" />
		<input type="radio" name="ratings" value="4" title="Fair" />
		<input type="radio" name="ratings" value="5" title="Average" />
		<input type="radio" name="ratings" value="6" title="Almost good" />
		<input type="radio" name="ratings" value="7" title="Good" />
		<input type="radio" name="ratings" value="8" title="Very good" />
		<input type="radio" name="ratings" value="9" title="Excellent" />
		<input type="radio" name="ratings" value="10" title="Perfect" />

		<input type="hidden" data-rating-value value="" />
	</div>
</div>