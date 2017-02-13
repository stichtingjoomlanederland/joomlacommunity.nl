<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformController extends JControllerLegacy
{
	public function __construct()
	{
		parent::__construct();

		JHtml::_('behavior.framework');

		$version 	= new RSFormProVersion();
		$v 			= (string) $version;
		$doc 		= JFactory::getDocument();

		if (RSFormProHelper::isJ('3.0')) {
			JHtml::_('jquery.framework');
		} else {
			$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/jquery.js?v='.$v);
		}
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/placeholders.js?v='.$v);
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/script.js?v='.$v);
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/jquery.tag-editor.js?v='.$v);
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/jquery.caret.min.js?v='.$v);
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/validation.js?v='.$v);
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/tablednd.js?v='.$v);
		$doc->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/js/jquery.scrollto.js?v='.$v);

		$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/css/style.css?v='.$v);
		if (!RSFormProHelper::isJ('3.0')) {
			$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/css/style25.css?v='.$v);
		}
		$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/css/jquery.tag-editor.css?v='.$v);
		$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/css/rsdesign.css?v='.$v);
		// load the font
		$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/css/fonts/rsicons.css?v='.$v);
	}

	public function mappings()
	{
		JFactory::getApplication()->input->set('view', 'forms');
		JFactory::getApplication()->input->set('layout', 'edit_mappings');
		JFactory::getApplication()->input->set('tmpl', 'component');

		parent::display();
	}

	public function changeLanguage()
	{
		$formId  	 = JFactory::getApplication()->input->getInt('formId');
		$tabposition = JFactory::getApplication()->input->getInt('tabposition');
		$tab		 = JFactory::getApplication()->input->getInt('tab',0);
		$tab 		 = $tabposition ? '&tab='.$tab : '';
		JFactory::getSession()->set('com_rsform.form.formId'.$formId.'.lang', JFactory::getApplication()->input->getString('Language'));

		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId.'&tabposition='.$tabposition.$tab);
	}

	public function changeEmailLanguage()
	{
		$input	 = JFactory::getApplication()->input;
		$formId  = $input->getInt('formId');
		$cid	 = $input->getInt('id');
		$type	 = $input->getCmd('type');

		JFactory::getSession()->set('com_rsform.emails.emailId'.$cid.'.lang', JFactory::getApplication()->input->getString('ELanguage'));

		$this->setRedirect('index.php?option=com_rsform&task=forms.emails&type='.$type.'&tmpl=component&formId='.$formId.'&cid='.$cid);
	}

	public function layoutsGenerate()
	{
		$model = $this->getModel('forms');
		$model->getForm();
		$model->_form->FormLayoutName = JFactory::getApplication()->input->getCmd('layoutName');
		$model->autoGenerateLayout();

		echo $model->_form->FormLayout;
		exit();
	}

	public function layoutsSaveName()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$name = JFactory::getApplication()->input->getCmd('formLayoutName');

		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__rsform_forms SET FormLayoutName='".$db->escape($name)."' WHERE FormId='".$formId."'");
		$db->execute();

		exit();
	}

	public function submissionExportPDF()
	{
		$cid = JFactory::getApplication()->input->getInt('cid');
		$this->setRedirect('index.php?option=com_rsform&view=submissions&layout=edit&cid='.$cid.'&format=pdf');
	}

	/**
	 * Backup / Restore Screen
	 */
	public function backupRestore()
	{
		JFactory::getApplication()->input->set('view', 'backuprestore');
		JFactory::getApplication()->input->set('layout', 'default');

		parent::display();
	}

	public function updatesManage()
	{
		JFactory::getApplication()->input->set('view', 'updates');
		JFactory::getApplication()->input->set('layout', 'default');

		parent::display();
	}

	public function plugin()
	{
		JFactory::getApplication()->triggerEvent('rsfp_bk_onSwitchTasks');
	}

	public function setMenu()
	{
		$app    	= JFactory::getApplication();
		$formId 	= $app->input->getInt('formId');
		$component 	= JComponentHelper::getComponent('com_rsform');

		$app->setUserState('com_menus.edit.item.type', 'component');
		$app->setUserState('com_menus.edit.item.link', 'index.php?option=com_rsform&view=rsform&formId='.$formId);
		$app->setUserState('com_menus.edit.item.data', array(
			'component_id' => $component->id,
			'type'		   => 'component',
			'formId'	   => $formId
		));
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
	}

	public function captcha()
	{
		require_once JPATH_SITE.'/components/com_rsform/helpers/captcha.php';

		$componentId = JFactory::getApplication()->input->getInt('componentId');
		$captcha = new RSFormProCaptcha($componentId);

		JFactory::getSession()->set('com_rsform.captcha.captchaId'.$componentId, $captcha->getCaptcha());
		exit();
	}
}