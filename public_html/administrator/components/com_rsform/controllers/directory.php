<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerDirectory extends RsformController
{
	public function __construct() {
		parent::__construct();
		
		$this->registerTask('apply', 'save');
	}

	public function manage() {
        $app = JFactory::getApplication();

		$app->input->set('view', 'directory');
        $app->input->set('layout', 'default');
		
		parent::display();
	}
	
	public function edit() {
        $app = JFactory::getApplication();

		$app->input->set('view', 	'directory');
		$app->input->set('layout', 	'edit');
		
		parent::display();
	}
	
	public function saveOrdering() {
		$db		= JFactory::getDbo();
		$cids	= JFactory::getApplication()->input->get('cid',array(),'array');
		$formId	= JFactory::getApplication()->input->getInt('formId',0);
		
		foreach ($cids as $key => $order)
		{
		    $object = (object) array(
		        'componentId' => $key,
                'formId'      => $formId,
                'ordering'    => $order
            );

		    $query = $db->getQuery(true)
                ->select($db->qn('componentId'))
                ->from($db->qn('#__rsform_directory_fields'))
                ->where($db->qn('componentId') . ' = ' . $db->q($key))
                ->where($db->qn('formId') . ' = ' . $db->q($formId));
		    if ($db->setQuery($query)->loadResult())
            {
                $db->updateObject('#__rsform_directory_fields', $object, array('componentId', 'formId'));
            }
            else
            {
                $db->insertObject('#__rsform_directory_fields', $object);
            }
		}
		
		echo 'Ok';
		exit();
	}
	
	public function saveDetails() {
		$db		= JFactory::getDbo();
		$cids	= JFactory::getApplication()->input->get('cid',array(),'array');
		$formId	= JFactory::getApplication()->input->getInt('formId',0);
		
		foreach ($cids as $key => $val)
		{
            $object = (object) array(
                'componentId'   => $key,
                'formId'        => $formId,
                'indetails'     => $val
            );

            $query = $db->getQuery(true)
                ->select($db->qn('componentId'))
                ->from($db->qn('#__rsform_directory_fields'))
                ->where($db->qn('componentId') . ' = ' . $db->q($key))
                ->where($db->qn('formId') . ' = ' . $db->q($formId));
            if ($db->setQuery($query)->loadResult())
            {
                $db->updateObject('#__rsform_directory_fields', $object, array('componentId', 'formId'));
            }
            else
            {
                $query->clear()
                    ->select('MAX( '  . $db->qn('ordering') . ')')
                    ->from($db->qn('#__rsform_directory_fields'))
                    ->where($db->qn('formId') . ' = ' . $db->q($formId));
                $object->ordering = (int) $db->setQuery($query)->loadResult() + 1;

                $db->insertObject('#__rsform_directory_fields', $object);
            }
		}
		
		echo 'Ok';
		exit();
	}
	
	public function save() {
		$data = JFactory::getApplication()->input->get('jform',array(),'array');
		
		$model = $this->getModel('directory');
		
		if (!$model->save($data)) {
			$this->setMessage($model->getError(),'error');
		} else {
			$this->setMessage(JText::_('RSFP_SUBM_DIR_SAVED'));
		}
		
		$task = $this->getTask();
		switch ($task) {
			case 'save':
				$link = 'index.php?option=com_rsform&view=directory';
			break;
			
			case 'apply':
				$tab	= JFactory::getApplication()->input->getInt('tab', 0);
				$link	= 'index.php?option=com_rsform&view=directory&layout=edit&formId='.$data['formId'].'&tab='.$tab;
			break;
		}
		
		$this->setRedirect($link);
	}
	
	public function cancel() {
		$this->setRedirect('index.php?option=com_rsform&view=directory');
	}
	
	public function cancelform() {
		$app 	= JFactory::getApplication();
		$jform	= $app->input->get('jform',array(),'array');
		$formId = $jform['formId'];
		$app->redirect('index.php?option=com_rsform&view=forms&layout=edit&formId='.$formId);
	}
	
	public function changeAutoGenerateLayout() {
        $app            = JFactory::getApplication();
        $db 			= JFactory::getDbo();
		$formId 		= $app->input->getInt('formId');
		$name           = $app->input->get('ViewLayoutName');
		$status         = $app->input->getInt('status');

		$query = $db->getQuery(true)
            ->select($db->qn('formId'))
            ->from($db->qn('#__rsform_directory'))
            ->where($db->qn('formId') . ' = ' . $db->q($formId));

		$object = (object) array(
		    'formId'                 => $formId,
            'ViewLayoutAutogenerate' => $status,
            'ViewLayoutName'         => $name
        );
		
		$db->setQuery($query);
		if (!$db->loadResult())
		{
		    $db->insertObject('#__rsform_directory', $object);
		}
		else
        {
            $db->updateObject('#__rsform_directory', $object, array('formId'));
        }
		
		jexit();
	}
	
	public function saveName() {
        $app    = JFactory::getApplication();
        $db     = JFactory::getDbo();
		$formId = $app->input->getInt('formId');
		$name   = $app->input->get('ViewLayoutName');

        $query = $db->getQuery(true)
            ->select($db->qn('formId'))
            ->from($db->qn('#__rsform_directory'))
            ->where($db->qn('formId') . ' = ' . $db->q($formId));

        $object = (object) array(
            'formId'         => $formId,
            'ViewLayoutName' => $name
        );

        $db->setQuery($query);
        if (!$db->loadResult())
        {
            $db->insertObject('#__rsform_directory', $object);
        }
        else
        {
            $db->updateObject('#__rsform_directory', $object, array('formId'));
        }
		
		jexit();
	}
	
	public function generate() {
		$db 	= JFactory::getDbo();
		$app    = JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$layout = $app->input->getCmd('layoutName');

        $query = $db->getQuery(true)
            ->select($db->qn('formId'))
            ->from($db->qn('#__rsform_directory'))
            ->where($db->qn('formId') . ' = ' . $db->q($formId));

        $object = (object) array(
            'formId' => $formId,
        );

        $db->setQuery($query);
        if (!$db->loadResult())
        {
            $db->insertObject('#__rsform_directory', $object);
        }
		
		$model = $this->getModel('directory');
		$model->getDirectory();
		$model->_directory->ViewLayoutName = $layout;
		$model->autoGenerateLayout();
		
		echo $model->_directory->ViewLayout;
		jexit();
	}
	
	public function remove() {
		$model	= $this->getModel('directory');
		$cids	= JFactory::getApplication()->input->get('cid',array(),'array');
		
		$model->remove($cids);
		
		$this->setRedirect('index.php?option=com_rsform&view=directory');
	}
}