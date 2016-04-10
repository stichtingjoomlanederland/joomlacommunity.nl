<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$open = !$links ? 'target="_blank"' : ''; ?>

<ul class="rsepro_locations<?php echo $suffix; ?>">
<?php foreach ($locations as $location) { ?>
	<li>
		<a <?php echo $open; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&location='.rseventsproHelper::sef($location->id,$location->name),true,$itemid); ?>"><?php echo $location->name; ?></a>
	</li>
<?php } ?>
</ul>