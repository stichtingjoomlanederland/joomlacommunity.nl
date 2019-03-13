<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerEmails extends RsformController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 'save');
	}
	
	public function save()
	{
	    $app    = JFactory::getApplication();
		$model	= $this->getModel('forms');
		$row	= $model->saveemail();
		$type	= $app->input->getCmd('type', 'additional');
		
		if ($this->getTask() == 'apply')
        {
            return $this->setRedirect('index.php?option=com_rsform&task=forms.emails&type='.$type.'&cid='.$row->id.'&formId='.$row->formId.'&tmpl=component&update=1');
        }

        JFactory::getDocument()->addScriptDeclaration("window.opener.updateemails({$row->formId}, '{$type}');window.close();");
	}
	
	public function remove()
	{
		$db		= JFactory::getDbo();
        $app    = JFactory::getApplication();
		$cid	= $app->input->getInt('cid');
		$formId = $app->input->getInt('formId');
		$type	= $app->input->getCmd('type','additional');
		$view	= $type == 'additional' ? 'forms' : 'directory';
		
		if ($cid)
		{
		    $query = $db->getQuery(true)
                ->delete($db->qn('#__rsform_emails'))
                ->where($db->qn('id') . ' = ' . $db->q($cid));
			$db->setQuery($query);
			$db->execute();

			$references = array(
                $cid . '.fromname',
                $cid . '.subject',
                $cid . '.message'
            );

			// Delete translations
            $query->clear()
                ->delete($db->qn('#__rsform_translations'))
                ->where($db->qn('reference') . ' = ' . $db->q('emails'))
                ->where($db->qn('reference_id') . ' IN (' . implode(',', $db->q($references)) . ')');
			$db->setQuery($query);
			$db->execute();
		}
		
		$app->input->set('view', $view);
		$app->input->set('layout', 'edit_emails');
		$app->input->set('tmpl', 'component');
		$app->input->set('formId', $formId);
		$app->input->set('type', $type);
		
		parent::display();
		jexit();
	}
	
	public function update()
	{
        $app    = JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$view	= $app->input->getCmd('type', 'additional') == 'additional' ? 'forms' : 'directory';
		
		$app->input->set('view', $view);
		$app->input->set('layout', 'edit_emails');
		$app->input->set('tmpl', 'component');
		$app->input->set('formId', $formId);
		
		parent::display();
		jexit();
	}
}