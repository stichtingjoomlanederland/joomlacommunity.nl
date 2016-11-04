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

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('list');

/**
 * Select list with installed and published components
 *
 */
class JFormFieldWbampthemeselector extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'wbampthemeselector';

	private $_items = null;

	protected function getOptions()
	{
		$options = $this->getItems();
		$default = array(
			'value' => '',
			'text' => JText::_('PLG_SYSTEM_WBAMP_DEFAULT_THEME')
		);
		array_unshift($options, $default);
		reset($options);

		return $options;
	}

	private function getItems()
	{
		if (is_null($this->_items))
		{
			try
			{
				$this->_items = array();
				$themes = array();

				JPluginHelper::importPlugin('wbampthemes');
				ShlSystem_Factory::dispatcher()->trigger('onWbAMPGetThemes', array(&$themes));

				// remove common extensions:
				if (!empty($themes))
				{
					foreach ($themes as $theme)
					{
						$this->_items[] = (object) array(
							'value' => $theme['pack'] . '.' . $theme['id'],
							'text' => $theme['name'] . ' (' . $theme['packName'] . ')'
						);
					}
				}
			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
			}
		}

		return $this->_items;
	}
}
