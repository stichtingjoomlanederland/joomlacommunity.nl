<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldAvatarsList extends JFormField {
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'AvatarsList';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput() {
		jimport('joomla.filesystem.file');

		$avatars = array(
			'gravatar' => true,
			'comprofiler' => false,
			'community' => false,
			'kunena' => false,
			'fireboard' => false,
			'easyblog' => false,
			'easydiscuss' => false
		);

		$avatar_options = array(JHTML::_('select.option', '', JText::_('COM_RSCOMMENTS_NO_AVATAR'), 'value', 'text'));
		foreach ($avatars as $component => $value) {
			$enabled = !$value ? JFile::exists(JPATH_SITE.'/components/com_'.$component.'/'.$component.'.php') : true;
			if($enabled)
				$avatar_options[] = JHTML::_('select.option', $component, JText::_('COM_RSCOMMENTS_'.strtoupper($component)),'value', 'text');
		}

		return JHTML::_('select.genericlist', $avatar_options, $this->name, '', 'value', 'text', $this->value);
	}
}
