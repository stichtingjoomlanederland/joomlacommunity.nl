<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.6.0.607
 * @date        2016-10-31
 */

defined('_JEXEC') or die;

/**
 * Display version and edition information
 *
 */
class JFormFieldWbsticker extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'wbedition';

	public function getInput()
	{
		return '';
	}

	public function getLabel()
	{
		// no autoloading right now
		include_once JPATH_ROOT . '/plugins/system/wbamp/helpers/edition.php';
		$html = array();

		// add link to switch tips on/off
		ShlHtml_Manager::getInstance()->addStylesheet('wbamp_be', array('files_path' => '/media/plg_wbamp/assets/default', 'assets_bundling' => false));
		$html[] = '<div class="wbedition-sticker">';

		if (WbampHelper_Edition::$id != 'full')
		{
			// a bit of css
			$html[] = '<div>wbAMP Community edition</div>';
		}

		$html[] = '<button class="wbtip-switch wbtip-hide" type="button" onclick="weeblrApp.tips.hideTips(\'hide\');">Hide Tips</button>';
		$html[] = '<button class="wbtip-switch wbtip-show" type="button" onclick="weeblrApp.tips.hideTips(\'show\');">Show Tips</button>';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
