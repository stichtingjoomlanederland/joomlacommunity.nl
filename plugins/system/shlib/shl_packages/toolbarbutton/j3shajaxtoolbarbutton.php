<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.522
 * @date		2016-05-17
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a popup window button
 *
 * @package 	Joomla.Framework
 * @subpackage		HTML
 * @since		1.5
 */
class JToolbarButtonj3shajaxtoolbarbutton extends JToolbarButtonStandard
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Popup';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string   $type     Unused string, formerly button type.
	 * @param   string   $name     Modal name, used to generate element ID
	 * @param   string   $text     The link text
	 * @param   string   $url      URL for popup
	 * @param   integer | string  $width    Width of popup
	 * @param   integer | string  $height   Height of popup
	 * @param   integer  $top      Top attribute.  [@deprecated  Unused, will be removed in 4.0]
	 * @param   integer  $left     Left attribute. [@deprecated  Unused, will be removed in 4.0]
	 * @param   string   $onClose  JavaScript for the onClose event.
	 * @param   string   $title    The title text
	 *
	 * @return  string  HTML string for the button
	 *
	 * @since   3.0
	 */
	public function fetchButton($type = 'Modal', $name = '', $text = '', $url = '', $params = array())
	{
		$iconClass = empty($params['iconClass']) ? '' : $params['iconClass'];
		$onclick = $this->_getOnclick($name, $url, $params);
		$buttonClass = empty($params['buttonClass']) ? 'btn btn-small' : $params['buttonClass'];
		$buttonTitle = empty($params['buttonTitle']) ? '' : ' title = "' . $params['buttonTitle'] . '"';
		
		$html = "<button class=\"$buttonClass\" $buttonTitle $onclick>\n";
		$html .= empty($iconClass) ? "" : "<i class=\"" . $iconClass . "\">\n";
		$html .= "</i>\n";
		$html .= "$text\n";

		$html .= "</button>\n";

		return $html;
	}

	/**
	 * Get the button id
	 *
	 * @param   string  $type  Button type
	 * @param   string  $name  Button name
	 *
	 * @return  string	Button CSS Id
	 *
	 * @since   3.0
	 */
	public function fetchId($type = 'Standard', $name = '', $text = '', $task = '', $list = true, $hideMenu = false)
	{
		return $this->_parent->getName() . '-' . "popup-$name";
	}

	protected function _getOnclick($name, $url, $params)
	{
		$url = $this->_fixUrl($url);

		$commands = array();

		// calculate the various parts of the ajax call params, encoded as JSon
		if (!empty($params['update']))
		{
			$commands['update'] = $params['update'];
		}

		// make that a json object
		$json = ShlSystem_Convert::arrayToJSObject($commands);

		// calculate the full command
		$onclick = 'onclick="shAjaxHandler(\'' . $name . '\',' . $json . ',' . $params['closewindow'] . ');"';

		return $onclick;
	}

	private function _fixUrl($url)
	{
		if (substr($url, 0, 4) !== 'http')
		{
			$url = JURI::base() . $url;
		}

		return $url;
	}
}
