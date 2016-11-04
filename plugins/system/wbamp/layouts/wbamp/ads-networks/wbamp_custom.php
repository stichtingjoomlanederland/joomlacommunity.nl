<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.6.0.607
 * @date        2016-10-31
 */

// no direct access
defined('_JEXEC') or die;

$adContent = JString::trim($displayData['params']->get('ads-custom-content', ''));
if (empty($adContent))
{
	return '';
}

$id = empty($displayData['ad_id']) ? '1' : $displayData['ad_id'];
?>

<div class="wbamp-container wbamp-ad">
	<div class="wbamp-amp-tag wbamp-custom" id="wbamp-custom-<?php echo $id; ?>">
		<?php echo $adContent; ?>
	</div>
</div>
