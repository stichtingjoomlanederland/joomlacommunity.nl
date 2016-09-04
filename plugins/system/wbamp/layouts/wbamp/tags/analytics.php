<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

if (empty($displayData['analytics_data']))
{
	return;
}
?>

<amp-analytics type="googleanalytics" id="wbamp_analytics_1">
<script type="application/json">
    <?php echo json_encode($displayData['analytics_data'], defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE : false); ?>

</script>
</amp-analytics>
