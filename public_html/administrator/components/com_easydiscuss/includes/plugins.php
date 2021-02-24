<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/easydiscuss.php');

class EasyDiscussPlugins extends JPlugin
{
	public $app = null;
	public $input = null;

	private $templateVariables = array();

	protected $group = null;
	protected $element = null;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
	}

	/**
	 * Allows child classes to assign variables to the template
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function assign($key, $value)
	{
		$this->templateVariables[$key] = $value;
	}

	/**
	 * Retrieves the current Joomla template
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getCurrentJoomlaTemplate()
	{
		static $template = null;

		if (is_null($template)) {
			$model = ED::model('Themes');
			$template = $model->getCurrentTemplate();
		}

		return $template;
	}

	/**
	 * Retrieves the override path of the plugin
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getOverridePath($relative = false)
	{
		$path = '';

		if (!$relative) {
			$path .= JPATH_ROOT;
		}

		$path .= '/templates/' . $this->getCurrentJoomlaTemplate() . '/html/plg_' . $this->group . '_' . $this->element;

		return $path;
	}

	/**
	 * Retrieves the plugin params
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getPluginParams()
	{
		static $cache = array();
		
		$key = md5($this->group . $this->element);

		if (!isset($cache[$key])) {
			$plugin	= JPluginHelper::getPlugin($this->group, $this->element);
			$cache[$key] = new JRegistry($plugin->params);
		}

		return $cache[$key];
	}

	/**
	 * Retrieves the path of the plugin
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getPath($relative = false)
	{
		$path = '';

		if (!$relative) {
			$path .= JPATH_ROOT;
		}

		$path .= '/plugins/' . $this->group . '/' . $this->element;

		return $path;
	}

	/**
	 * Determines if the plugin has template override
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function hasOverride($namespace)
	{
		$file = $this->getOverridePath() . '/' . $namespace . '.php';

		if (JFile::exists($file)) {
			return true;
		}

		return false;
	}

	/**
	 * Load helpers for the module
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function html()
	{
		$theme = ED::themes();
		$args = func_get_args();

		$output = call_user_func_array(array($theme, 'html'), $args);

		return $output;
	}

	/**
	 * Retrieves the output of the template file for a plugin
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function output($namespace)
	{
		$extension = '.php';
		$path = $this->getPath() . '/themes/' . $namespace;
		$file = $path . $extension;
		$hasOverride = $this->hasOverride($namespace);

		if ($hasOverride) {
			$file = $this->getOverridePath() . '/' . $namespace . $extension;
		}

		extract($this->templateVariables);

		ob_start();
		include($file);
		$contents = ob_get_contents();
		ob_end_clean();

		// Include script file if it has a script file
		$scriptFile = $path . '.js';
		$scriptFileExists = JFile::exists($scriptFile);
		
		if ($scriptFileExists) {
			ob_start();
			?>
			<script>
			<?php 
			include($scriptFile);
			?>
			</script>
			<?php
			$contents .= ob_get_contents();
			ob_end_clean();			
		}

		return $contents;
	}
}
