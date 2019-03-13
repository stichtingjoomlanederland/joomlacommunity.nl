<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussCompiler extends EasyDiscuss
{
	private $base = null;
	public $site = null;
	public $admin = null;

	public function __construct($location = '')
	{
		parent::__construct();

		$this->base = JURI::root() . 'media/com_easydiscuss/scripts';

		if (!defined('ED_CLI')) {
			$this->site = !$this->app->isAdmin();
			$this->admin = $this->app->isAdmin();
		} else {

			$this->site = false;
			$this->admin = false;

			if ($location == 'site') {
				$this->site = true;
			}

			if ($location == 'admin') {
				$this->admin = true;
			}
		}
	}

	/**
	 * Compiles javascripts from vendors folder
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function compile($minify = true, $force = false, $isBuild = false)
	{
		// We need to concatenate in the following orders
		$version = $this->getVersion($isBuild);

		// Get a list of files to be generated
		$files = $this->files($version, $force);

		foreach ($files as $file => $contents) {

			// Pass to closure to minify it
			if ($minify) {
				$closure = ED::closure();
				$contents = $closure->minify($contents);
			}

			JFile::write($file, $contents);
		}
	}

	/**
	 * Retrieves the script file name
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getFileName($version, $prefix = '')
	{
		$name = 'easydiscuss';

		if ($prefix) {
			$name .= '-' . $prefix;
		}

		$name .= '-' . $version . '.js';

		return $name;
	}

	/**
	 * Retrieves the version numbering that should be used for scripts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getVersion($isBuild = false)
	{
		$version = ED::getLocalVersion();

		if ($isBuild) {
			// we knwo this is to compile for the next release. so we need to increase the version by 1.
			$segments = explode('.', $version);

			//update last segment by 1
			// the segments will always be 3 elemetns
			$segments[2] = $segments[2] + 1;

			$version = implode('.', $segments);
		}

		return $version;
	}

	/**
	 * Retrieves the contents of the file and compile it
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getContents($folders, $exclusion = array())
	{
		// Cache the contents throughout the script execution
		static $items = array();

		// Determines which items was already appended into the contents
		$loaded = array();

		// Base contents
		$contents = '';

		// Always add index.html into the exclusion
		$exclusion[] = 'index.html';

		foreach ($folders as $folder) {

			if (is_file($folder)) {
				$files = array($folder);
			} else {
				// Get a list of files in this folder
				$files = JFolder::files($folder, '.', true, true);
			}

			foreach ($files as $file) {

				$fileName = basename($file);

				// If excluded, we shouldn't be including this file in the contents
				if (in_array($fileName, $exclusion)) {
					continue;
				}

				// If this was already read before, just attach the contents
				if (isset($items[$file]) && !isset($loaded[$file])) {
					$contents .= $items[$file];
					$loaded[$file] = true;
				}

				if (!isset($loaded[$file])) {
					$fileContents = JFile::read($file);
					$contents .= $fileContents;

					$items[$file] = $fileContents;
					$loaded[$file] = true;
				}
			}

		}

		return $contents;
	}

	/**
	 * Retrieve the list of files to be merged
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function files($version, $force = false)
	{
		$files = array();

		// We need to generate 2 files for the site.
		// One with jquery and the other without jquery
		if ($this->site || $force) {

			$paths = array(
							JPATH_ROOT . '/media/com_easydiscuss/scripts/vendors/require.js',
							JPATH_ROOT . '/media/com_easydiscuss/scripts/site/core.js',
							JPATH_ROOT . '/media/com_easydiscuss/scripts/vendors',
							JPATH_ROOT . '/media/com_easydiscuss/scripts/site/vendors',
							JPATH_ROOT . '/media/com_easydiscuss/scripts/site/src'
					);

			// Get contents from all scripts
			$contents = $this->getContents($paths, array('jquery.joomla.js'));

			// Get contents without jquery
			// @TODO: We also need to perform this
			// 3. Search / replace "vendors/jquery" to "vendors/jquery.joomla"
			$contentsBasic = $this->getContents($paths, array('edjquery.js'));
			$contentsBasic = str_ireplace("'vendors/edjquery'", "'vendors/jquery.joomla'", $contentsBasic);

			// Get contents with
			$files[JPATH_ROOT . '/media/com_easydiscuss/scripts/site/' . $this->getFileName($version)] = $contents;
			$files[JPATH_ROOT . '/media/com_easydiscuss/scripts/site/' . $this->getFileName($version, 'basic')] = $contentsBasic;
		}

		if ($this->admin || $force) {
			$paths = array(
							JPATH_ROOT . '/media/com_easydiscuss/scripts/vendors/require.js',
							JPATH_ROOT . '/media/com_easydiscuss/scripts/admin/core.js',
							JPATH_ROOT . '/media/com_easydiscuss/scripts/vendors',
							JPATH_ROOT . '/media/com_easydiscuss/scripts/admin/vendors',
							JPATH_ROOT . '/media/com_easydiscuss/scripts/admin/src'
					);

			// Get contents from all scripts
			$contents = $this->getContents($paths, array('jquery.joomla.js'));

			// To debug, uncomment the following
			// echo $contents;
			// exit;

			// Get contents with
			$files[JPATH_ROOT . '/media/com_easydiscuss/scripts/admin/' . $this->getFileName($version)] = $contents;
		}

		return $files;
	}

	/**
	 * Attaches whatever that is necessary
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function attach()
	{
		// Possible scenarios
		// 1. Load with jquery
		// 2. Load without jquery
		// 3.

		$url = JURI::root();
		$cdnUrl = ED::getCdnUrl();

		// If system is configured to use cdn, we should use the cdn url
		if ($this->config->get('system_cdn') && $cdnUrl) {
			$url = $cdnUrl;
		}

		// Ensure that there is no trailing / in the url
		$url = rtrim($url, '/');

		$path = $this->site ? 'site' : 'admin';

		// Production mode
		if ($this->config->get('system_environment') == 'production') {

			$version = $this->getVersion();

			// Should we load the basic version (without jquery)
			$prefix = '';

			// Only respect this for the site since we always require jquery from the back end.
			if ($this->site && !$this->config->get('system_jquery')) {
				$prefix = 'basic';
			}

			// Get the url to the script file
			$uri = $url . '/media/com_easydiscuss/scripts/' . $path . '/' . $this->getFileName($version, $prefix);

			$script = $this->createScriptTag($uri);

			// We need to define this to fix joomla in subfolder issue
			$siteConfig = $this->createScriptBlock($this->getConfiguration());

			// Ensure that document type is under html
			if ($this->doc->getType() == 'html') {
				$this->doc->addCustomTag($siteConfig);
				$this->doc->addCustomTag($script);
			}

			return;
		}

		// Development mode
		if ($this->config->get('system_environment') == 'development') {

			// On development mode we need to insert discuss_site to fix subfolder issues in requirejs
			$siteConfig = $this->createScriptBlock($this->getConfiguration());

			// Only in development mode we should load the require.js separately
			$requirejs = $this->createScriptTag($this->base . '/vendors/require.js');
			$core = $this->createScriptTag($this->base . '/' . $path . '/core.js');

			// Ensure that document type is under html
			if ($this->doc->getType() == 'html') {
				$this->doc->addCustomTag($siteConfig);
				$this->doc->addCustomTag($requirejs);
				$this->doc->addCustomTag($core);
			}
		}
	}

	/**
	 * Generates a script tag
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function createScriptTag($path)
	{
		return '<script src="' . $path . '"></script>';
	}

	/**
	 * Generates a script block
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function createScriptBlock($contents = '')
	{
		ob_start();
?>
<!--googleoff: index-->
<script type="text/javascript">
<?php echo $contents;?>
</script>
<!--googleon: index-->
<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Generates EasyDiscuss dependencies
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getConfiguration()
	{
ob_start();
?>
window.ed_site = "<?php echo JURI::root();?>";
window.ed_mobile = <?php echo (ED::responsive()->isMobile()) ? 'true' : 'false'; ?>;
<?php
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}
}
