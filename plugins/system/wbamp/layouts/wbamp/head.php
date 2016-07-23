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

// canonical to the regular html page
if (!empty($displayData['canonical']))
{
	echo "\n\t" . '<link rel="canonical" href="' . $this->escape($displayData['canonical']) . '" />';
}

if (!empty($displayData['shURL']))
{
	echo "\n\t" . '<link rel="shortlink" href="' . $this->escape($displayData['shURL']) . '" />';
}

if (!empty($displayData['metadata']['robots']))
{
	echo "\n\t" . '<meta name="robots" content="' . $this->escape($displayData['metadata']['robots']) . '">';
}

if (!empty($displayData['metadata']['title']))
{
	echo "\n\t" . '<title>' . $displayData['metadata']['title'] . '</title>';
}
if (!empty($displayData['metadata']['description']))
{
	echo "\n\t" . '<meta name="description" content="' . $this->escape($displayData['metadata']['description']) . '">';
}
if (!empty($displayData['metadata']['keywords']))
{
	echo "\n\t" . '<meta name="keywords" content="' . $this->escape($displayData['metadata']['keywords']) . '">';
}

if (!empty($displayData['metadata']['publisher_id']))
{
	$publisherUrl = 'https://plus.google.com/' . htmlspecialchars($displayData['metadata']['publisher_id'], ENT_COMPAT, 'UTF-8');
	echo "\n\t" . '<link href="' . $publisherUrl . '" rel="publisher" />';
}

if (!empty($displayData['metadata']['ogp']))
{
	echo "\n\t" . $displayData['metadata']['ogp'];
}

if (!empty($displayData['metadata']['tcards']))
{
	echo "\n\t" . $displayData['metadata']['tcards'];
}

echo "\n";

// user provided custom links
$customLinks = Jstring::trim($displayData['custom_links']);
if (!empty($customLinks))
{
	echo "\n\t" . $customLinks . "\n";
}

?>

<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>

<?php echo ShlMvcLayout_Helper::render('wbamp.style', $displayData, WbampHelper_Runtime::$layoutsBasePaths); ?>

<?php if (!empty($displayData['json-ld'])) : ?>
	<script type="application/ld+json">
    <?php echo json_encode($displayData['json-ld'], defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false); ?>
	</script>
<?php endif; ?>

<?php echo ShlMvcLayout_Helper::render('wbamp.scripts', $displayData, WbampHelper_Runtime::$layoutsBasePaths); ?>

<script async src="https://cdn.ampproject.org/v0.js"></script>
