<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class acyslidersHelper {
	var $ctrl = 'sliders';
	var $tabs = null;
	var $openPanel = false;
	var $mode = null;
	var $count = 0;
	var $name = '';
	var $options = null;

	function __construct() {
		if(!ACYMAILING_J16) {
			$this->mode = 'pane';
		} elseif(!ACYMAILING_J30) {
			$this->mode = 'sliders';
		} else {
			$this->mode = 'bootstrap';
		}
	}

	function startPane($name) { return $this->start($name); }
	function startPanel($text, $id) { return $this->panel($text, $id); }
	function endPanel() { return ''; }
	function endPane() { return $this->end(); }

	function setOptions($options = array()) {
		if($this->options == null)
			$this->options = $options;
		else
			$this->options = array_merge($this->options, $options);
	}

	function start($name, $options = array()) {
		$ret = '';
		if($this->mode == 'pane') {
			jimport('joomla.html.pane');
			if(!empty($this->options))
				$options = array_merge($options, $this->options);
			$this->tabs = JPane::getInstance('sliders', $options);
			$ret .= $this->tabs->startPane($name);
		} elseif($this->mode == 'sliders') {
			if(!empty($this->options))
				$options = array_merge($options, $this->options);
			$ret .= JHtml::_('sliders.start', $name, $options);
		} else {
			if($this->options == null)
				$this->options = $options;
			else
				$this->options = array_merge($this->options, $options);
			$this->name = $name;
			$this->count = 0;
			$ret .= '<div class="accordion" id="'.$name.'">';
		}
		return $ret;
	}

	function panel($text, $id) {
		$ret = '';
		if($this->mode == 'pane') {
			if($this->openPanel)
				$ret .= $this->tabs->endPanel();
			$ret .= $this->tabs->startPanel($text, $id);
			$this->openPanel = true;
		} elseif($this->mode == 'sliders') {
			$ret .= JHtml::_('sliders.panel', JText::_($text), $id);
		} else {
			if($this->openPanel)
				$ret .= $this->_closePanel();

			$open = '';
			if((isset($this->options['startOffset']) && $this->options['startOffset'] == $this->count) || $this->count == 0)
				$open = ' in';
			$this->count++;
			$ret .= '
<div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#'.$this->name.'" href="#'.$id.'">
        '.$text.'
      </a>
    </div>
    <div id="'.$id.'" class="accordion-body collapse'.$open.'">
      <div class="accordion-inner">
';
			$this->openPanel = true;
		}
		return $ret;
	}

	function _closePanel() {
		if(!$this->openPanel)
			return '';
		$this->openPanel = false;
		return '</div></div></div>';
	}

	function end() {
		$ret = '';
		if($this->mode == 'pane') {
			if($this->openPanel)
				$ret .= $this->tabs->endPanel();
			$ret .= $this->tabs->endPane();
		} elseif($this->mode == 'sliders') {
			$ret .= JHtml::_('sliders.end');
		} else {
			if($this->openPanel)
				$ret .= $this->_closePanel();
			$ret .= '</div>';
		}
		return $ret;
	}
}
