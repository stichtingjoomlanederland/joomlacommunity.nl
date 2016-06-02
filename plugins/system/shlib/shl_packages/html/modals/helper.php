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

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die();

class ShlHtmlModal_helper
{

	/**
	 * Prepares a string suitable for use when creating a SqueezeBox modal
	 *
	 * @param array $params
	 * @return string options
	 */
	public static function makeSqueezeboxOptions($params = array())
	{
		// Setup options object
		$opt = array();

		$opt['ajaxOptions'] = (isset($params['ajaxOptions']) && (is_array($params['ajaxOptions']))) ? $params['ajaxOptions'] : null;
		$opt['size'] = (isset($params['size']) && (is_array($params['size']))) ? $params['size'] : null;
		$opt['sizeLoading'] = (isset($params['sizeLoading']) && (is_array($params['sizeLoading']))) ? $params['sizeLoading'] : null;
		$opt['marginInner'] = (isset($params['marginInner']) && (is_array($params['marginInner']))) ? $params['marginInner'] : null;
		$opt['marginImage'] = (isset($params['marginImage']) && (is_array($params['marginImage']))) ? $params['marginImage'] : null;

		$opt['overlayOpacity'] = (isset($params['overlayOpacity'])) ? $params['overlayOpacity'] : null;
		$opt['classWindow'] = (isset($params['classWindow'])) ? $params['classWindow'] : null;
		$opt['classOverlay'] = (isset($params['classOverlay'])) ? $params['classOverlay'] : null;
		$opt['disableFx'] = (isset($params['disableFx'])) ? $params['disableFx'] : null;

		$opt['onOpen'] = (isset($params['onOpen'])) ? $params['onOpen'] : null;
		$opt['onClose'] = (isset($params['onClose'])) ? $params['onClose'] : null;
		$opt['onUpdate'] = (isset($params['onUpdate'])) ? $params['onUpdate'] : null;
		$opt['onResize'] = (isset($params['onResize'])) ? $params['onResize'] : null;
		$opt['onMove'] = (isset($params['onMove'])) ? $params['onMove'] : null;
		$opt['onShow'] = (isset($params['onShow'])) ? $params['onShow'] : null;
		$opt['onHide'] = (isset($params['onHide'])) ? $params['onHide'] : null;

		$opt['fxOverlayDuration'] = (isset($params['fxOverlayDuration'])) ? $params['fxOverlayDuration'] : null;
		$opt['fxResizeDuration'] = (isset($params['fxResizeDuration'])) ? $params['fxResizeDuration'] : null;
		$opt['fxContentDuration'] = (isset($params['fxContentDuration'])) ? $params['fxContentDuration'] : null;

		$options = ShlSystem_Convert::arrayToJSObject($opt);

		$options = substr($options, 0, 1) == '{' ? substr($options, 1) : $options;
		$options = substr($options, -1) == '}' ? substr($options, 0, -1) : $options;

		return $options;
	}

	/**
	 * Method to render a Bootstrap modal
	 *
	 * @param   string  $selector  The ID selector for the modal.
	 * @param   array   $params    An array of options for the modal.
	 * @param   string  $footer    Optional markup for the modal footer
	 *
	 * @return  string  HTML markup for a modal
	 *
	 * @since   3.0
	 */
	public static function renderBootstrapModal($selector = 'modal', $params = array(), $footer = '')
	{
		// Ensure the behavior is loaded
		JHtml::_('bootstrap.framework');

		$params['selector'] = $selector;
		$js = '
			<script>
			(function() {
						var params = ' . json_encode($params) . ';
						shlBootstrap.registerModal(params);
						})();
			</script>';

		return $js;
	}

	public static function modalLink($name, $text, $url, $width = 640, $height = 480, $top = 0, $left = 0, $onClose = '', $title = '',
		$params = array())
	{
		$iconClass = empty($params['iconClass']) ? '' : $params['iconClass'];
		$onclick = 'onclick="shlBootstrap.canOpenModal = true;"';
		$linkClass = empty($params['linkClass']) ? 'btn btn-link' : $params['linkClass'];
		$linkTitle = empty($params['linkTitle']) ? $title : $params['linkTitle'];
		$linkType = empty($params['linkType']) ? 'button' : $params['linkType'];

		$html = "<$linkType class=\"$linkClass\" title=\"$linkTitle\" $onclick data-toggle=\"modal\" data-target=\"#modal-" . $name . "\"";
		if($linkType == 'a') {
			$html .= ' href="javascript:void(0);"';
		}
		$html .= ">";
		$html .= empty($iconClass) ? "" : "<i class=\"" . $iconClass . "\"></i>";
		$html .= "$text";

		$html .= "</$linkType>";

		// Build the options array for the modal
		$params = array();
		$params['title'] = $title;
		$params['url'] = $url;
		$params['height'] = $height;
		$params['width'] = $width;

		// render the modal
		$html .= self::renderBootstrapModal('modal-' . $name, $params);

		// If an $onClose event is passed, add it to the modal JS object

		return $html;
	}
}
