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
		JHtml::_('jquery.framework');

        JHtml::script('com_rsform/admin/placeholders.js', array('relative' => true, 'version' => 'auto'));
        JHtml::script('com_rsform/admin/script.js', array('relative' => true, 'version' => 'auto'));
        JHtml::script('com_rsform/admin/jquery.tag-editor.js', array('relative' => true, 'version' => 'auto'));
        JHtml::script('com_rsform/admin/jquery.caret.min.js', array('relative' => true, 'version' => 'auto'));
        JHtml::script('com_rsform/admin/validation.js', array('relative' => true, 'version' => 'auto'));
        JHtml::script('com_rsform/admin/tablednd.js', array('relative' => true, 'version' => 'auto'));
        JHtml::script('com_rsform/admin/jquery.scrollto.js', array('relative' => true, 'version' => 'auto'));

        JHtml::stylesheet('com_rsform/admin/style.css', array('relative' => true, 'version' => 'auto'));
        JHtml::stylesheet('com_rsform/admin/jquery.tag-editor.css', array('relative' => true, 'version' => 'auto'));
        JHtml::stylesheet('com_rsform/admin/rsicons.css', array('relative' => true, 'version' => 'auto'));
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

	public function captcha()
	{
		require_once JPATH_SITE.'/components/com_rsform/helpers/captcha.php';

		$componentId = JFactory::getApplication()->input->getInt('componentId');
		$captcha = new RSFormProCaptcha($componentId);

		JFactory::getSession()->set('com_rsform.captcha.captchaId'.$componentId, $captcha->getCaptcha());
		
		if (JFactory::getDocument()->getType() != 'image')
		{
			JFactory::getApplication()->close();
		}
	}
}