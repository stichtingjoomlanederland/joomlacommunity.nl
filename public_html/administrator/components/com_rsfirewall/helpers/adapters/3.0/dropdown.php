<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSDropdown
{
	protected $context = '';
	protected $plural = 's';
	
	public function __construct($options=array()) {
		foreach ($options as $k => $v) {
			$this->$k = $v;
		}
	}
	
	public function show($i, $item) {
		static $init = false;
		if (!$init) {
			JHtml::_('dropdown.init');
			JFactory::getDocument()->addScriptDeclaration("
			// This fixes Joomla! jQuery.attr() bug.
			jQuery(document).ready(function($){
				contextAction = function (cbId, task) {
					$('input[name=\"cid[]\"]').removeAttr('checked');
					$('#' + cbId).prop('checked', true);
					
					Joomla.submitbutton(task);
				}
			});
			");
		}
		
		// Create dropdown items
		$context = $this->context.'.';
		JHtml::_('dropdown.edit', $item->id, $context);
		
		if (isset($item->published)) {
			JHtml::_('dropdown.divider');
			$context = $this->context.$this->plural.'.';
			if ($item->published) {
				JHtml::_('dropdown.unpublish', 'cb' . $i, $context);
			} else {
				JHtml::_('dropdown.publish', 'cb' . $i, $context);
			}
		}

		$context = $this->context.$this->plural.'.';
		JHtml::_('dropdown.trash', 'cb' . $i, $context);

		// Render dropdown list
		echo JHtml::_('dropdown.render');
	}
}