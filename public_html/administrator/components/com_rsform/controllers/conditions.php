<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerConditions extends RsformController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 'save');
	}
	
	public function save()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$model 	= $this->getModel('conditions');
		$task 	= $this->getTask();
		$formId = $model->getFormId();
		
		// Save
		$cid = $model->save();

        $link = 'index.php?option=com_rsform&view=conditions&layout=edit&formId=' . $formId . '&tmpl=component';

		if ($cid)
        {
            $link .= '&cid=' . $cid;
            $msg = JText::_('RSFP_CONDITION_SAVED');
        }
        else
        {
            $msg = JText::_('RSFP_CONDITION_ERROR');
        }

        if ($task == 'save')
        {
            $link .= '&close=1';
        }

		$this->setRedirect($link, $msg);
	}
	
	public function remove()
	{
		$model  = $this->getModel('conditions');
		$formId = $model->getFormId();
		$app    = JFactory::getApplication();
		
		$model->remove();
		
		$app->input->set('view', 'forms');
        $app->input->set('layout', 'edit_conditions');
        $app->input->set('tmpl', 'component');
        $app->input->set('formId', $formId);
		
		parent::display();
		jexit();
	}
	
	public function showConditions()
	{
		$model  = $this->getModel('conditions');
		$formId = $model->getFormId();
        $app    = JFactory::getApplication();

        $app->input->set('view', 'forms');
        $app->input->set('layout', 'edit_conditions');
        $app->input->set('tmpl', 'component');
        $app->input->set('formId', $formId);
		
		parent::display();
        jexit();
	}
}