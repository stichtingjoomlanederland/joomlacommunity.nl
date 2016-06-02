<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.3.1.490
 * @date        2016-05-18
 */

// no direct access
defined('_JEXEC') or die;

$adContent = JString::trim($displayData['params']->get('ads-custom-content', ''));
if (empty($adContent))
{
	return '';
}

?>

<div class="wbamp-container wbamp-ad">
	<div class="wbamp-amp-tag wbamp-custom" id="wbamp-custom-1">
		<?php echo $adContent; ?>
	</div>
</div>
