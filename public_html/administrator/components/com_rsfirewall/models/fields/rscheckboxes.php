<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('JPATH_PLATFORM') or die;

$jversion = new JVersion();

if ($jversion->isCompatible('3.0')) {
	JFormHelper::loadFieldClass('list');
	
	class JFormFieldRSCheckboxes extends JFormFieldList
	{
		protected $type = 'RSCheckboxes';
		
		public function __construct($form = null) {
			parent::__construct($form);
			
			static $init;
			if (!$init) {
				JHtml::_('formbehavior.chosen', 'select');
				$init = true;
			}
		}
	}
} else {
	JFormHelper::loadFieldClass('checkboxes');
	
	class JFormFieldRSCheckboxes extends JFormFieldCheckboxes
	{
		protected $type = 'RSCheckboxes';
	}
}