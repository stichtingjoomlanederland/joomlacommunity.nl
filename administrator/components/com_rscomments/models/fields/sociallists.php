<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldSocialLists extends JFormField {
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'SocialLists';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput() {
		jimport('joomla.filesystem.file');

		$social_links = array(
			'comprofiler' 	=> false,
			'community' 	=> false
		);

		$social_options = array(JHTML::_('select.option', '', JText::_('COM_RSCOMMENTS_NO_SOCIAL_LINK'), 'value', 'text'));
		foreach ($social_links as $component => $value) {
			$enabled = !$value ? JFile::exists(JPATH_SITE.'/components/com_'.$component.'/'.$component.'.php') : true;
			if($enabled)
				$social_options[] = JHTML::_('select.option', $component, JText::_('COM_RSCOMMENTS_'.strtoupper($component)),'value', 'text');
		}

		return JHTML::_('select.genericlist', $social_options, $this->name, '', 'value', 'text', $this->value);
	}
}