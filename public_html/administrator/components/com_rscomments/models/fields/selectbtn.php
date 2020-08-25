<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldSelectbtn extends JFormField {
	
	protected $type = 'Selectbtn';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput() {
		$app			= JFactory::getApplication();
		$context		= $this->getAttribute('context');
		$component 		= $app->getUserStateFromRequest($context.'.filter.component', 'filter_component', '');
		$component_id	= $app->getUserStateFromRequest($context.'.filter.component_id', 'filter_component', '');
		$article		= RSCommentsHelperAdmin::ArticleTitle($component, $component_id);
		
		$return  = '<a href="javascript:void(0)" onclick="jQuery(\'#rscModal\').modal(\'show\');" class="btn btn-info btn-xsmall btnarticle">'.(!empty($article) ? $article : JText::_('COM_RSCOMMENTS_SELECT_ARTICLE')).'</a>';
		$return .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value="'.$this->value.'" />';
		$return .= JHtml::_('bootstrap.renderModal', 'rscModal', array('title' => JText::_('COM_RSCOMMENTS_SELECT_ARTICLE'), 'url' => 'index.php?option=com_rscomments&view=components&component='.$component.'&tmpl=component', 'bodyHeight' => 70));
		
		return $return;
	}
}