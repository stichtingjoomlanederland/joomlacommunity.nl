<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldRSUsers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSUsers';
	
	/**
	 * Method to get the user group field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput() {
		$html = array();

		// Get excluded users
		$excluded = $this->getExcludes();
		
		// Build the script.
		$script = array();
		
		if (rseventsproHelper::isJ4()) {
			$script[] = 'function jSelectUserJ4() { ';
			$script[] = 'rsepro_add_user(jQuery(\'.field-user-input\').val(), jQuery(\'.field-user-input-name\').val());';
			$script[] = '}';
		}
		
		$script[] = 'function jSelectUser(what) {';
		$script[] = 'var id = jQuery(what).data(\'user-value\')';
		$script[] = 'var title = jQuery(what).data(\'user-name\')';
		$script[] = 'rsepro_add_user(id, title);';
		$script[] = '}';
		
		$script[] = 'function rsepro_add_user(id, title) {';
		$script[] = 'if (id == \'\' || id == \'0\') {';
		$script[] = 'jQuery(\'#rseModal\').modal(\'hide\');';
		$script[] = 'return;';
		$script[] = '}';
		$script[] = 'if (jQuery(\'#jform_jusers option[value="\'+id+\'"]\').length) {';
		$script[] = 'alert(\''.JText::_('COM_RSEVENTSPRO_USER_ALREADY_EXISTS',true).'\');';
		$script[] = 'return;';
		$script[] = '}';
		$script[] = 'jQuery(\'#jform_jusers\').append(jQuery(\'<option>\', { \'text\': title, \'value\': id, selected : true }));';
		$script[] = 'jQuery(\'#jform_jusers\').trigger(\'liszt:updated\');';
		$script[] = 'jQuery(\'#rseModal\').modal(\'hide\');';
		$script[] = '}';
		
		$script[] = 'function jRemoveUsers() {';
		$script[] = 'jQuery(\'#jform_jusers option\').remove();';
		$script[] = 'jQuery(\'#jform_jusers\').trigger(\'liszt:updated\');';
		$script[] = '}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		if (rseventsproHelper::isJ4()) {
			JFactory::getDocument()->getWebAssetManager()->useScript('webcomponent.field-user');
		}
		
		$url = 'index.php?option=com_users&view=users&layout=modal&tmpl=component&field='.$this->id.(!empty($excluded) ? ('&excluded=' . base64_encode(json_encode($excluded))) : '');
		
		if (rseventsproHelper::isJ4()) {
			$html[] = '<joomla-field-user url="'.$url.'" class="field-user-wrapper" modal=".modal" modal-width="100%" modal-height="400px" button-select=".modal_jform_jusers" input=".field-user-input" input-name=".field-user-input-name">';
			$html[] = '<input type="hidden" class="field-user-input" value="" data-onchange="jSelectUserJ4()" />';
			$html[] = '<input type="hidden" class="field-user-input-name" value="" />';
		}
		
		// Create the select field to hold the users.
		$html[] = '<div class="fltlft">';		
		$html[] = parent::getInput();
		$html[] = '</div>';
		
		// Create the users select button.
		$html[] = '<div class="button2-left">';
		$html[] = '<div class="blank">';
		if ($this->element['readonly'] != 'true')
		{
			$html[] = '<a class="modal_' . $this->id . '" href="javascript:void(0);" onclick="jQuery(\'#rseModal\').modal(\'show\')">'.JText::_('COM_RSEVENTSPRO_GROUP_ADD_USERS').'</a> / ';
			$html[] = '<a href="javascript:void(0)" onclick="jRemoveUsers();">'.JText::_('COM_RSEVENTSPRO_GROUP_REMOVE_USERS') . '</a>';
		}
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = JHtml::_('bootstrap.renderModal', 'rseModal', array('title' => '&nbsp;', 'url' => $url , 'height' => '100%', 'width' => '100%', 'modalWidth' => 80, 'bodyHeight' => 70));
		
		if (rseventsproHelper::isJ4()) {
			$html[] = '</joomla-field-user>';
		}
		
		return implode("\n", $html);
	}
	
	
	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$options= array();
		
		// Get the selected users
		$query->clear();
		$query->select('jusers');
		$query->from('#__rseventspro_groups');
		$query->where('id = '.$db->quote($jinput->getInt('id',0)));
		
		$db->setQuery($query);
		if ($users = $db->loadResult()) {
			try {
				$registry = new JRegistry;
				$registry->loadString($users);
				$users = $registry->toArray();
			} catch (Exception $e) {
				$users = array();
			}
			$users = array_map('intval',$users);
			
			if (!empty($users)) {
				// Get the options
				$query->clear();
				$query->select($db->qn('id','value'))->select($db->qn('name','text'));
				$query->from($db->qn('#__users'));
				$query->where($db->qn('id').' IN ('.implode(',',$users).')');
				
				$db->setQuery($query);
				$options = $db->loadObjectList();
			}
		}
		
		return $options;
	}
	
	protected function getExcludes() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$excludes = array();
		$jinput = JFactory::getApplication()->input;
		
		$query->clear();
		$query->select($db->qn('jusers'))
			->from($db->qn('#__rseventspro_groups'))
			->where($db->qn('jusers').' <> '.$db->q(''))
			->where($db->qn('id').' <> '.$db->q($jinput->getInt('id',0)));
		
		$db->setQuery($query);
		if ($options = $db->loadColumn()) {
			foreach ($options as $option) {
				try {
					$registry = new JRegistry;
					$registry->loadString($option);
					$option = $registry->toArray();
				} catch (Exception $e) {
					$option = array();
				}
				
				$option = array_map('intval',$option);
				$excludes = array_merge($excludes, $option);
			}
		}
		
		$excludes = array_unique($excludes);
		return !empty($excludes) ? $excludes : '';
	}
}