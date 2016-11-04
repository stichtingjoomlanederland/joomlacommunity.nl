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
class JFormFieldWbedition extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'wbedition';

	public function getInput()
	{
		// no autoloading right now
		include_once JPATH_ROOT . '/plugins/system/wbamp/helpers/edition.php';

		// a bit of css
		ShlHtml_Manager::getInstance()->addStylesheet('wbamp_be', array('files_path' => '/media/plg_wbamp/assets/default', 'assets_bundling' => false));

		// compute info message
		$version = JText::sprintf('PLG_SYSTEM_WBAMP_VERSION', WbampHelper_Edition::$version);
		$license = '<a href="https://weeblr.com/legal/licensing">' . JText::_('PLG_SYSTEM_WBAMP_LICENSE') . '</a>';
		$edition = WbampHelper_Edition::$name . ' - ' . JText::sprintf('PLG_SYSTEM_WBAMP_EDITION_' . strtoupper(WbampHelper_Edition::$id), WbampHelper_Edition::$url);

		$html = array();
		$html[] = '<div class="wbedition-info'
			. (WbampHelper_Edition::$id != 'full' ? ' wbedition-warning' : '')
			. '" > ' . $version . ' | ' . $license . ' | ' . $edition . '</div > ';

		return implode("\n", $html);
	}

	public function getLabel()
	{
		return '';
	}
}

