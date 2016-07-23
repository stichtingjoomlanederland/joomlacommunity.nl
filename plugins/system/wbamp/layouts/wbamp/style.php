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

$css = array();

// shared LESS generated css
$filename = ShlHtml_Manager::getInstance()
                           ->getMediaLink(
	                           'wbamp_fe',
	                           'css',
	                           array('files_path' => '/media/plg_wbamp/assets/default', 'url_root' => JPATH_ROOT, 'assets_bundling' => false)
                           );
$css['wbamp_fe'] = WbampHelper_File::getIncludedFile($filename);

// additional CSS
if (WbampHelper_Edition::$id == 'full')
{
	// Full edition components
	$filename = ShlHtml_Manager::getInstance()
	                           ->getMediaLink(
		                           'wbamp_fe',
		                           'css',
		                           array('files_path' => '/media/plg_wbamp/assets_full/default', 'url_root' => JPATH_ROOT, 'assets_bundling' => false)
	                           );
	if (file_exists($filename))
	{
		$css['wbamp_fe_full'] = WbampHelper_File::getIncludedFile($filename);
	}

	// CSS from themes. Themes can add some css, remove existing (except user set) or a mix of both
	$css['theme'] = '';
	ShlSystem_Factory::dispatcher()->trigger('onWbAMPGetCss', array($displayData['theme'], & $css, $displayData));

	// Custom template (mostly Joomla stuff overrides)
	$filename = ShlHtml_Manager::getInstance()
	                           ->getMediaLink(
		                           'template',
		                           'css',
		                           array('files_path' => '/media/tpl_' . strtolower($displayData['joomla_template']) . '/assets/default', 'url_root' => JPATH_ROOT, 'assets_bundling' => false)
	                           );
	if (file_exists($filename))
	{
		$css['template'] = WbampHelper_File::getIncludedFile($filename);
	}
}

// override by user input CSS
$css['user'] = JString::trim($displayData['custom_style']);

$customCss = trim(implode("\n", $css));
if (empty($customCss))
{
	return;
}
?>

<style amp-custom>

	<?php echo "\n\t" . $customCss . "\n";	?>

</style>
