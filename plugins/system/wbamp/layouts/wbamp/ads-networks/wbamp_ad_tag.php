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

/**
 * Renders an amp-ad tag based on short code set by user in content
 * Short code syntax =
 * wbamp-embed type="ad"  -> will render the ad defined in plugin params
 * wbamp-embed type="ad" width="123px" height="456px" ad-type="a9" data-xxxx -> will render
 *   the ad tag as defined by the short-code attributes; "ad-type" will be turned into "type"
 *
 */

$attributes = empty($displayData['attributes']) ? array() : $displayData['attributes'];
$attributesStrings = array();
foreach ($displayData['attributes'] as $key => $value)
{
	$attributesStrings[] = $key . '=' . json_encode($value);
}

$attributesString = implode(' ', $attributesStrings);
?>
<amp-ad class="wbamp-ad wbamp-ad-from-tag" <?php echo $attributesString; ?>
	id="wbamp-ad-<?php echo md5(mt_rand() . $attributesString); ?>">
</amp-ad>
