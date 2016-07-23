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

class WbampClass_Theme extends JPlugin
{
	public $pack     = 'default';
	public $packName = 'Default';
	public $id       = 'default';
	public $name     = 'default';

	protected $userValues = null;

	/**
	 * Returns theme ids, used to build a select
	 * list for users
	 *
	 * @return array
	 */
	public function onWbAMPGetThemes(& $themes)
	{
		$themes = empty($themes) ? array() : $themes;
		$themes[] = array('id' => $this->id, 'pack' => $this->pack, 'name' => $this->name, 'packName' => $this->packName);

		return true;
	}

	/**
	 * Add our JLayouts to the list of available ones
	 *
	 * @param $layoutPaths
	 */
	public function onWbAMPGetLayoutsPaths($theme, & $layoutPaths)
	{
		if ($theme == $this->pack . '.' . $this->id)
		{
			array_unshift($layoutPaths, JPATH_ROOT . '/plugins/wbampthemes/' . $this->id . '/layouts');
		}
		return true;
	}

	/**
	 * Adds/modify CSS of the rendered AMP page
	 *
	 * @param $theme
	 * @param $css
	 * @param $displayData
	 * @return bool
	 */
	public function onWbAMPGetCss($theme, & $css, $displayData)
	{
		// are we the currently selected theme?
		if ($theme != $this->pack . '.' . $this->id)
		{
			return true;
		}

		// load our CSS from file
		$filename = ShlHtml_Manager::getInstance()
		                           ->getMediaLink(
			                           'theme',
			                           'css',
			                           array('files_path' => '/plugins/wbampthemes/' . $this->pack . '/theme', 'url_root' => JPATH_ROOT, 'assets_bundling' => false, 'assets_mode' => ShlHtml_Manager::DEV)
		                           );
		// override default
		$css['wbamp_fe'] = WbampHelper_File::getIncludedFile($filename);

		// remove comments and new lines
		$css['wbamp_fe'] = $this->compress($css['wbamp_fe']);

		// nuke full edition css
		$css['wbamp_fe_full'] = '';

		// replace the tags
		$css['wbamp_fe'] = $this->replaceTags($css['wbamp_fe'], $this->params);

		return true;
	}

	/**
	 * We do a simple compression (remove comments and NL)
	 * We can't use the already gulp-compressed version of the
	 * css, as gulp-clean-css kills (most of) our tokens
	 *
	 * @param $css
	 * @return mixed
	 */
	protected function compress($css)
	{
		$regExp = '#\/\*.*\*\/#usU';
		$css = preg_replace($regExp, "", $css);
		$css = str_replace("\n", '', $css);
		$css = str_replace(' {  ', '{', $css);
		$css = str_replace(';}', '}', $css);
		$css = str_replace(';  ', ';', $css);
		$css = str_replace(' + ', '+', $css);
		$css = str_replace(': ', ':', $css);

		return $css;
	}

	/**
	 * Process a customizable theme CSS, replacing
	 * tags by their user-set values, or default
	 * value if none set
	 *
	 * @param $content
	 * @return mixed
	 */
	protected function replaceTags($rawCss, $userSetValues)
	{
		if (empty($rawCss))
		{
			return $rawCss;
		}

		$regex = '#\'{wb_([^}]*)}\'#';
		$this->userValues = $userSetValues;
		$css = preg_replace_callback($regex, array($this, '_replace'), $rawCss);

		return $css;
	}

	/**
	 * preg_replace callback, replaces a tag based on user-set values
	 *
	 * @param $match
	 * @return mixed
	 */
	protected function _replace($match)
	{
		$value = $match[0];

		// set user-set value, or use default if none
		if (!empty($match[1]))
		{
			list($name, $defaultValue) = explode('\\', $match[1]);
			$value = $this->userValues->get($name, $defaultValue);
		}

		return $value;
	}
}
