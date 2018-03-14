<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerRestore extends RsformController
{
	public function start() {
		$app	= JFactory::getApplication();
		$files	= $app->input->files->get('jform', null, 'raw');
		$file	= $files['backup'];
		$model	= $this->getModel('restore');
		
		// Redirect back to the backup restore screen.
		$this->setRedirect('index.php?option=com_rsform&view=backuprestore');
		
		try {
			// Attempt at restoring the file.
			$key = $model->restore($file);
			// Get the overwrite selection
			$overwrite = $model->getOverwrite();
			
			// Get the keepId selection
			$keepId = $model->getKeepId();
			// If we're returned a key, redirect to the "restoration" screen.
			if ($key) {
				$this->setRedirect('index.php?option=com_rsform&view=restore&key='.$key.'&overwrite='.$overwrite.'&keepid='.$keepId);
				return;
			}
			
			$app->enqueueMessage(JText::_('RSFP_RESTORE_OK'));
		} catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}
	
	public function decompress() {
		$model	= $this->getModel('restore');
		
		try {
			$model->decompress();
			$this->showResponse(array(
				'status'  => 'ok',
				'step'	  => 'next-xml-headers'
			));
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function getInfo() {
		$model	= $this->getModel('restore');
		
		try {
			$metadata = $model->getInfo();
			$this->showResponse(array(
				'status'  => 'ok',
				'step'	  => 'list-info',
				'metadata'   => $metadata
			));
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function overwriteForms() {
		$model	= $this->getModel('restore');
		
		try {
			$model->overwriteForms();
			$this->showResponse(array(
				'status'  => 'ok',
				'step'	  => 'parse-form'
			));
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function parseForm() {
		$model	= $this->getModel('restore');
		
		$submissions = (int) JFactory::getApplication()->input->getCmd('submissions');
		try {
			$response = $model->parseForm();	
			$this->showResponse(array(
				'status'  => 'ok',
				'step'	  => ($submissions > 0  ? 'parse-submissions' : 'parse-form'),
				'form'	  => $response->form,
				'formId'  => $response->formId,
				'file'	  => 0,
			));
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function parseSubmissions() {
		$model	= $this->getModel('restore');
		
		try {
			$response = $model->parseSubmissions();	
			$this->showResponse(array(
				'status'   => 'ok',
				'step'	   => ($response->nextFile > 0  ? 'continue-submissions' : 'parse-form'),
				'file'	   => $response->nextFile,
				'finished' => ($response->nextFile == 0 ? 1 : 0),
				'form'	   => $response->form
			));
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	
	}
	
	public function deleteTemporaryFiles() {
		$model	= $this->getModel('restore');
		$input 	= JFactory::getApplication()->input;
		$onerror = $input->getInt('onerror', 0);
		
		try {
			$model->deleteTemporaryFiles();	
			if ($onerror) {
				$this->showResponse(array(
					'status'   => 'error',
					'message'  => 'tmp-removed'
				));
			} else {
				$this->showResponse(array(
					'status'   => 'ok',
					'step'	   => 'restore-done'
				));
			}
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}	
	
	protected function getKey() {
		$input 		= JFactory::getApplication()->input;
		return $input->getCmd('key');
	}
	
	protected function showError($message) {
		$this->showResponse(array(
			'status'  => 'error',
			'message' => $message
		));
	}
	
	protected function showResponse($data) {
		// Set proper document encoding
		JFactory::getDocument()->setMimeEncoding('application/json');
		
		// Echo the JSON encoded data.
		echo json_encode($data);
		
		// Close the application.
		JFactory::getApplication()->close();
	}
}