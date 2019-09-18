<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JFormHelper::loadFieldClass('list');

class JFormFieldComponents extends JFormFieldList {
	
	protected $type = 'Components';

	protected function getOptions() {
		$options = array();
		$components = RSCommentsHelperAdmin::getComponents();
		
		foreach($components as $component) {
			$options[] = JHtml::_('select.option', $component, RSCommentsHelperAdmin::component($component));
		}
		
		
		return array_merge(parent::getOptions(), $options);
	}
}