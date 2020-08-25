<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldAuthorname extends JFormField {
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Authorname';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput() {
		$user_info = array(JHtml::_('select.option', 'username', JText::_('COM_RSCOMMENTS_USER_USERNAME'),'value', 'text'),JHtml::_('select.option', 'name', JText::_('COM_RSCOMMENTS_USER_NAME'),'value', 'text'));
		$user_info[] = JHtml::_('select.option', 'cb', JText::_('COM_RSCOMMENTS_USER_CB'),'value', 'text', file_exists(JPATH_SITE.'/components/com_comprofiler/comprofiler.php') ? false : true);
		
		return JHtml::_('select.genericlist', $user_info, $this->name, 'class="custom-select"', 'value', 'text', $this->value);
	}
}