<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldCaptcha extends RSFormProField
{
	// backend preview
	public function getPreviewInput() {
		$type 	 = $this->getProperty('IMAGETYPE', 'FREETYPE');
		$caption = $this->getProperty('CAPTION', '');
		
		if ($type == 'INVISIBLE') {
			$captchaOutput = '{hidden captcha}';
		} else {
			$flow		 = $this->getProperty('FLOW', 'VERTICAL');
			$componentId = $this->getProperty('componentId');
			$refresh 	 = $this->getProperty('SHOWREFRESH', 'NO');
			$attr		 = $this->getAttributes();
			$type 		 = 'text';
			
			// Start building the image HTML
			// Image source
			$src = JRoute::_('index.php?option=com_rsform&task=captcha&componentId='.$componentId.'&format=image&sid='.mt_rand());
			// Image HTML
			$image = '<img'.
					 ' src="'.$src.'"'.
					 ' id="captcha'.$componentId.'"'.
					 ' alt="'.$this->escape($caption).'"'.
					 ' />';
			// Start building the HTML input
			$input = '<input';
			// Set the type & value
			$input .= ' type="'.$this->escape($type).'"'.
					  ' value=""';
			// Name & id
			$input .= ' id="captchaTxt'.$componentId.'"';
			// Close the tag
			$input .= ' />';
			
			// Add the refresh button
			$refreshBtn = '';
			if ($refresh) {
				$text		= $this->getProperty('REFRESHTEXT', '');
				$base 		= JRoute::_('index.php?option=com_rsform&task=captcha&componentId='.$componentId.'&format=image');
				$onclick 	= "RSFormPro.refreshCaptcha('$componentId', '$base'); return false;";
				$refreshBtn = ' <a href="javascript:void(0)" onclick="'.$onclick.'">'.$text.'</a>';
			}
			
			$captchaOutput = $image.($flow == 'VERTICAL' ? '<br/>' :'').$input.$refreshBtn;
		}
		
		$html = '<td>'.$caption.'</td><td>'.$captchaOutput.'</td>';
		return $html;
	}
	
	// functions used for rendering in front view
	protected function getImageInput() {
		$name		 = $this->getName();
		$flow		 = $this->getProperty('FLOW', 'VERTICAL');
		$caption	 = $this->getProperty('CAPTION', '');
		$componentId = $this->getProperty('componentId');
		$refresh 	 = $this->getProperty('SHOWREFRESH', 'NO');
		$attr		 = $this->getAttributes();
		$type 		 = 'text';
		$additional  = '';
		
		// Start building the image HTML
		// Image source
		$src = JRoute::_('index.php?option=com_rsform&task=captcha&componentId='.$componentId.'&format=image&sid='.mt_rand());
		// Image HTML
		$image = '<img'.
				 ' src="'.$src.'"'.
				 ' id="captcha'.$componentId.'"'.
				 ' alt="'.$this->escape($caption).'"'.
				 ' />';
		// Start building the HTML input
		$input = '<input';
		// Parse Additional Attributes
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type, size, maxlength) can be overwritten
				// directly from the Additional Attributes area
				if ($key == 'type' && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		// Set the type & value
		$input .= ' type="'.$this->escape($type).'"'.
				  ' value=""';
		// Name & id
		$input .= ' name="'.$this->escape($name).'"'.
				  ' id="captchaTxt'.$componentId.'"';
		// Additional HTML
		$input .= $additional;
		// Close the tag
		$input .= ' />';
		
		// Add the refresh button
		$refreshBtn = '';
		if ($refresh) {
			$text		= $this->getProperty('REFRESHTEXT', '');
			$base 		= JRoute::_('index.php?option=com_rsform&task=captcha&componentId='.$componentId.'&format=image');
			$onclick 	= "RSFormPro.refreshCaptcha('$componentId', '$base'); return false;";
			$refreshBtn = ' <a href="javascript:void(0)" '.$this->getRefreshAttributes().' onclick="'.$onclick.'">'.$text.'</a>';
		}
		
		return $this->setFieldOutput($image, $input, $refreshBtn, $flow);
	}
	
	protected function setFieldOutput($image, $input, $refreshBtn, $flow) {
		// Vertical flow needs a <br /> tag
		return $image.($flow == 'VERTICAL' ? '<br/>' :'').$input.$refreshBtn;
	}
	
	protected function getInvisibleInput() {
		$componentId = $this->getProperty('componentId');
		$words  	 = $this->getWords();
		$word   	 = $words[array_rand($words, 1)];
		$styles 	 = $this->getStyles();
		$style  	 = $styles[array_rand($styles, 1)];
		
		// Now we're going to shuffle the properties of the HTML tag
		$properties = array(
			'type="text"',
			'name="'.$word.'"',
			'value=""',
			'style="'.$style.'"'
		);
		shuffle($properties);
		
		$session = JFactory::getSession();
		$session->set('com_rsform.captcha.captchaId'.$componentId, $word);
		
		return '<input '.implode(' ', $properties).' />';
	}
	
	public function getFormInput() {
		$type = $this->getProperty('IMAGETYPE', 'FREETYPE');
		if ($type == 'INVISIBLE') {
			return $this->getInvisibleInput();
		} else {
			return $this->getImageInput();
		}
	}
	
	
	// @desc A list of words that spam bots might auto-complete
	protected function getWords() {
		return array('Website', 'Email', 'Name', 'Address', 'User', 'Username', 'Comment', 'Message');
	}
	
	// @desc A list of styles
	protected function getStyles() {
		return array(
			'display: none !important', 'position: absolute !important; left: -4000px !important; top: -4000px !important;',
			'position: absolute !important; left: -4000px !important; top: -4000px !important; display: none !important;',
			'position: absolute !important; display: none !important;',
			'display: none !important; position: absolute !important; left: -4000px !important; top: -4000px !important;'
		);
	}
	
	// @desc All captcha textboxes should have a 'rsform-captcha-box' class for easy styling
	public function getAttributes() {
		$attr = parent::getAttributes();
		if (strlen($attr['class'])) {
			$attr['class'] .= ' ';
		}
		$attr['class'] .= 'rsform-captcha-box';

		return $attr;
	}
	
	protected function getRefreshAttributes() {
		$attr = array(
			'class="rsform-captcha-refresh-button"'
		);
		
		return implode(' ', $attr);
	}
}