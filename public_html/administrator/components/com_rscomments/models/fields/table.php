<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldTable extends JFormField {
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Table';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput() {
		$db 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		$tables = $db->getTableList();
		
		foreach($tables as $field)
			$tables_list[] = JHtml::_('select.option', $field, $field);

		$html = JHtml::_('select.genericlist', $tables_list, 'table', ' class="" onchange="rsc_update_cols(this.value);"', 'value', 'text', '');
		return $html;
	}
}