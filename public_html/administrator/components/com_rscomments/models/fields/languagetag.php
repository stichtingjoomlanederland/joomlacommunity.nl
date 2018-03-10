<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

use Joomla\CMS\Language\LanguageHelper;

class JFormFieldLanguagetag extends JFormField {
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Languagetag';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput() {
		$tag 	= JFactory::getApplication()->input->get('tag','');

		if(!empty($tag)) {
			$html = '<input type="hidden" name="'.$this->name.'" value="'.$tag.'" />';
		} else {
			$languages	= LanguageHelper::getKnownLanguages();
			$db			= JFactory::getDbo();
			$query		= $db->getQuery(true);

			$query->select('DISTINCT('.$db->qn('tag').')')
				->from($db->qn('#__rscomments_messages'));
			
			$db->setQuery($query);
			$exclude_langs = $db->loadObjectList();

			foreach($exclude_langs as $lang)
				unset($languages[$lang->tag]);

			if(!empty($languages)) {
				foreach($languages as $language)
					$language_list[] = JHtml::_('select.option', $language['tag'], '('.$language['tag'].') '.$language['name']);

				$html = JHtml::_('select.genericlist', $language_list, $this->name , '', 'value', 'text', '');
			}
		}

		return $html;
	}
	
	public function getLabel() {
		$tag = JFactory::getApplication()->input->get('tag','');

		if (empty($tag)) 
			$label = parent::getLabel();
		else 
			$label = '';

		return $label;
	}
}