<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT.'/helpers/backup/backup.php';

class RSFormControllerBackup extends RSFormController
{
	public function start() {		
		$input 		= JFactory::getApplication()->input;
		$options	= array(
			'forms' 		=> $input->get('forms', array(0), 'array'),
			'submissions' 	=> $input->get('submissions', 0, 'int')
		);
		
		try {
			$backup = new RSFormProBackup($options);
			$backup->storeMetaData();
			
			$this->showResponse(array(
				'status'  => 'ok',
				'step'	  => 'forms',
				'key'	  => $backup->getKey()
			));
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function storeForms() {
		$input 		= JFactory::getApplication()->input;
		$options 	= array(
			'forms' => $input->get('forms', array(), 'array'),
			'key'   => $input->get('key', '', 'cmd')
		);
		
		try {
			// Need to process requested forms
			if ($options['forms']) {
				$backup = new RSFormProBackup($options);
				$backup->storeForms();
				
				$this->showResponse(array(
					'status'  => 'ok',
					'step'	  => 'forms'
				));
			} else {
				// Form structure is done, continue with submissions (if requested, will be checked by JS script)
				$this->showResponse(array(
					'status'  => 'ok',
					'step'	  => 'prepare-submissions'
				));
			}
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function storeSubmissions() {		
		$input 		= JFactory::getApplication()->input;
		$form  		= $input->get('form', 0, 'int');
		$key   		= $input->get('key', '', 'cmd');
		$start 		= $input->get('start', 0, 'int');
		$limit 		= $input->get('limit', 100, 'int');
		$header   	= $input->get('header', '', 'cmd');
		$options 	= array(
			'forms' => array($form),
			'key'	=> $key
		);
		
		try {
			$backup = new RSFormProBackup($options);
			$result = $backup->storeSubmissions($start, $limit, $header);
			
			if ($result->done) {
				// We're done with this form, jump to next form.
				$this->showResponse(array(
					'status'  => 'ok',
					'step'	  => 'next-form-submissions'
				));
			} else {
				// Continue with submissions
				$this->showResponse(array(
					'status'  => 'ok',
					'step'	  => 'submissions',
					'header'  => $result->header,
					'start'	  => $start + $limit,
					'form'	  => $form
				));
			}
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function prepareGzip() {
		$input 		 = JFactory::getApplication()->input;
		$key    	 = $input->get('key', '', 'cmd');
		$options 	 = array(
			'key' => $key
		);
		
		try {
			$backup  = new RSFormProBackup($options);
			$archive = new RSFormProTar($backup->getPath());
			
			// Need to make the archive valid now that we're done with it.
			$archive->addFooter();
			
			// Continue with GZIP archive creation
			$this->showResponse(array(
				'status'  => 'ok',
				'step'	  => 'prepare-gzip',
				'chunks'  => ceil($archive->getSize() / $archive->getChunkSize())
			));
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function compressGzip() {
		$input 		 = JFactory::getApplication()->input;
		$key    	 = $input->get('key', '', 'cmd');
		$seek		 = $input->get('seek', 0, 'cmd');
		$options 	 = array(
			'key' => $key
		);
		
		try {
			$backup  = new RSFormProBackup($options);
			$archive = new RSFormProTar($backup->getPath());
			
			// GZIP compress it.
			$seek = $archive->compress($seek);
			
			// Continue with GZIP archive creation
			$this->showResponse(array(
				'status'  => 'ok',
				'step'	  => $seek ? 'compress-gzip' : 'done',
				'seek'    => $seek
			));
		} catch (Exception $e) {
			$this->showError($e->getMessage());
		}
	}
	
	public function download() {
		$input 		 = JFactory::getApplication()->input;
		$key    	 = $input->get('key', '', 'cmd');
		$data 		 = $input->get('jform', array(), 'array');
		$options 	 = array(
			'key' => $key,
			'name' => (empty($data['name']) ? 'backup' : $data['name'])
		);
		
		try {
			$backup  = new RSFormProBackup($options);
			$backup->download();
		} catch (Exception $e) {
			JError::raiseError(500, $e->getMessage());
		}
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