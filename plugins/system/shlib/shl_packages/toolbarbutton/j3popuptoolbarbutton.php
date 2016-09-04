<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2016
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.580
 * @date		2016-08-25
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
class JToolbarButtonj3popuptoolbarbutton extends JToolbarButtonStandard
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
	public function fetchButton($type = 'Modal', $name = '', $text = '', $url = '', $width = 640, $height = 480, $top = 0, $left = 0, $onClose = '',
		$title = '', $params = array())
	{
		$text = JText::_($text);
		$title = JText::_($title);
		$iconClass = empty($params['iconClass']) ? '' : $params['iconClass'];
		$onclick = $this->_getOnclick(empty($params['checkListSelection']) ? false : true);
		$url = $this->_fixUrl($url);
		$buttonClass = empty($params['buttonClass']) ? 'btn btn-small modal' : $params['buttonClass'];
		$buttonTitle = empty($params['buttonTitle']) ? '' : ' title = "' . $params['buttonTitle'] . '"';
		
		$html = "<button class=\"$buttonClass\" $buttonTitle $onclick data-toggle=\"modal\" data-target=\"#modal-" . $name . "\">\n";
		$html .= empty($iconClass) ? "" : "<i class=\"" . $iconClass . "\"></i>\n";
		$html .= "$text\n";

		$html .= "</button>\n";

		// Build the options array for the modal
		$params = array();
		$params['title'] = $title;
		$params['url'] = $url;
		$params['height'] = $height;
		$params['width'] = $width;

		// render the modal
		$html .= ShlHtmlModal_helper::renderBootstrapModal('modal-' . $name, $params);

		// If an $onClose event is passed, add it to the modal JS object
		if (strlen($onClose) >= 1)
		{
			$html .= "<script>\n";
			$html .= "jQuery('#modal-" . $name . "').on('hide', function () {\n";
			$html .= $onClose . ";\n";
			$html .= "}";
			$html .= ");";
			$html .= "</script>\n";
		}

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

	protected function _getOnclick($check = false)
	{
		if (empty($check))
		{
			$onclick = 'onclick="shlBootstrap.canOpenModal = true;"';
		}
		else
		{
			$onclick = '';
			$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
			$message = addslashes($message);
			$onclick = 'onclick="if (document.adminForm.boxchecked.value==0){alert(\'' . $message
				  . '\');shlBootstrap.canOpenModal = false;return false;}else{
				  var cid = document.getElementsByName(\'cid[]\');
			      var list = \'\';
			      if (cid) {
			        var length = cid.length;
			        for ( var i = 0; i < length; i++) {
			          if (cid[i].checked) {
			            list += \'&cid[]=\' + cid[i].value;
		}
			        }
			      }
			      shlBootstrap.setSelectedIdsUrl(list);
				shlBootstrap.canOpenModal = true;return true;}"';
		}
		return $onclick;
	}

	private function _fixUrl($url)
	{
		if (substr($url, 0, 4) !== 'http')
		{
			$url = JURI::base(true) . '/' . $url;
		}

		return $url;
	}
}
