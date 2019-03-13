<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerRichtext extends RsformController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 'save');
	}
	
	public function show()
	{
        $app = JFactory::getApplication();

		$app->input->set('view',    'forms');
        $app->input->set('layout',  'richtext');
		
		parent::display();
	}
	
	public function save()
	{
		$db 	= JFactory::getDbo();
		$app    = JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$opener = $app->input->getCmd('opener');
		$value  = $app->input->post->get($opener, '', 'raw');
		$model  = $this->getModel('forms');

		$model->getForm();
		$lang = $model->getLang();
		if ($model->_form->Lang != $lang && !RSFormProHelper::getConfig('global.disable_multilanguage'))
		{
			$model->saveFormRichtextTranslation($formId, $opener, $value, $lang);
		}
		else
		{
		    $query = $db->getQuery(true)
                ->update($db->qn('#__rsform_forms'))
                ->set($db->qn($opener) . ' = ' . $db->q($value))
                ->where($db->qn('FormId') . ' = ' . $db->q($formId));
			$db->setQuery($query);
			$db->execute();
		}

		/**
		 * Add feedback in the modal window
		 */
        $app->enqueueMessage(JText::_('RSFP_CHANGES_SAVED'));

		if ($this->getTask() == 'apply')
			return $this->setRedirect('index.php?option=com_rsform&task=richtext.show&opener='.$opener.'&formId='.$formId.'&tmpl=component');

        JFactory::getDocument()->addScriptDeclaration("window.close();");
	}
	
	public function preview()
	{
	    $app    = JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$opener = $app->input->getCmd('opener');
		
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
            ->select($db->qn($opener))
            ->from($db->qn('#__rsform_forms'))
            ->where($db->qn('FormId') . ' = ' . $db->q($formId));
		$db->setQuery($query);
		$value = $db->loadResult();
		
		$model = $this->getModel('forms');
		$model->getForm();
		$lang = $model->getLang();
		$translations = RSFormProHelper::getTranslations('forms', $formId, $lang);
		if ($translations && isset($translations[$opener]))
			$value = $translations[$opener];
		
		echo $value;
	}
}