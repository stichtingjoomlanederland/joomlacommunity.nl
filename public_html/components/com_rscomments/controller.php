<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsController extends JControllerLegacy
{
	// Main constructor
	public function __construct() {
		parent::__construct();
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rscomments/tables');
	}
	
	// Load captcha image
	public function captcha() {
		require_once JPATH_SITE.'/components/com_rscomments/helpers/securimage/securimage.php';
		
		ob_end_clean();
		$captcha	= new JSecurImage();
		$config		= RSCommentsHelper::getConfig();
		$lines		= $config->captcha_lines;
		$characters = $config->captcha_chars;
		
		$captcha->num_lines = $lines ? 8 : 0;
		$captcha->code_length = $characters;
		$captcha->image_width = 30*$characters + 50;
		$captcha->show();
		JFactory::getApplication()->close();
	}
	
	// Load pagination
	public function pagination() {
		$app = JFactory::getApplication();
		$app->input->set('limitstart',$app->input->getInt('limitstart',0));
		$app->input->set('pagination',0);
		
		$option		= $app->input->get('content','');
		$id			= $app->input->getInt('id',0);
		$template	= $app->input->get('rsctemplate','default');
		$override 	= $app->input->getInt('override');

		echo RSCommentsHelper::showComments($option,$id,$template,1,$override,'items');
		$app->close();
	}
	
	// Show terms and conditions
	public function terms() {
		$config = RSCommentsHelper::getConfig();
		$tag 	= JFactory::getLanguage()->getTag();
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('content'))
			->from($db->qn('#__rscomments_messages'))
			->where($db->qn('tag').' = '.$db->q($tag))
			->where($db->qn('type').' = '.$db->q('terms'));
		
		$db->setQuery($query);
		$terms = $db->loadResult();

		if ($config->terms && $terms != '') {
			echo '<div class="rscomments-white-popup">';
			echo $terms;
			
			if ($config->modal == 2) {
				echo '<div class="text-right"><a href="javascript:void(0)" class="btn btn-primary" onclick="RSComments.agree();">'.JText::_('COM_RSCOMMENTS_I_AGREE').'</a> <a href="javascript:void(0)" onclick="jQuery.magnificPopup.close();" class="btn">'.JText::_('COM_RSCOMMENTS_CLOSE').'</a></div>';
			}
			
			echo '</div>';
		}
		
		JFactory::getApplication()->close();
	}
	
	// Show subscribe view
	public function subscribe() {
		$view = new JViewLegacy(array(
			'name' => 'rscomments',
			'layout' => 'subscribe',
			'base_path' => JPATH_SITE.'/components/com_rscomments'
		));
		
		$view->addTemplatePath(JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_rscomments/' . $view->getName());
		
		$view->config	= RSCommentsHelper::getConfig();
		
		echo $view->loadTemplate();
	}
	
	// Show the report view
	public function report() {
		$view = new JViewLegacy(array(
			'name' => 'rscomments',
			'layout' => 'report',
			'base_path' => JPATH_SITE.'/components/com_rscomments'
		));
		
		$view->addTemplatePath(JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_rscomments/' . $view->getName());
		
		RSCommentsHelper::loadRecaptcha('report');
		
		$view->config	= RSCommentsHelper::getConfig();
		$view->root 	= JURI::getInstance()->toString(array('scheme','host'));
		
		echo $view->loadTemplate();
	}
	
	// Show a list of the user's comments
	public function mycomments() {
		$view = new JViewLegacy(array(
			'name' => 'rscomments',
			'layout' => 'mycomments',
			'base_path' => JPATH_SITE.'/components/com_rscomments'
		));
		
		$view->addTemplatePath(JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_rscomments/' . $view->getName());
		$model 				= $this->getModel('Comments');
		
		$view->setModel($model);
		
		$view->config		= RSCommentsHelper::getConfig();
		$view->root 		= JURI::getInstance()->toString(array('scheme','host'));
		$view->pagination	= $model->getUserCommentsPagination();
		$view->comments		= $model->getUserComments();		
		$view->permissions	= RSCommentsHelper::getPermissions();		
		$view->user			= JFactory::getUser();
		$view->sid			= JFactory::getSession()->getId();
		
		echo $view->loadTemplate();
	}
	
	public function removecomment() {
		$model	= $this->getModel('Comments');
		$return	= $model->remove(JFactory::getApplication()->input->getInt('id'));
		
		if (isset($return['error'])) {
			JFactory::getApplication()->enqueueMessage($return['error'], 'error');
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_RSCOMMENTS_COMMENT_REMOVED'));
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_rscomments&task=mycomments&tmpl=component', false));
	}
	
	// Show the upload form
	public function upload() {
		echo '<form name="frameform" id="frameform" action="'.JRoute::_('index.php?option=com_rscomments&task=uploadfile').'" method="post" enctype="multipart/form-data">';
		echo '<input type="file" name="file" size="40" />';
		echo '<input type="hidden" name="rsc_id" id="rsc_id" value="" />';
		echo '<input type="hidden" name="rsc_option" id="rsc_option" value="" />';
		echo '</form>';
		JFactory::getApplication()->close();
	}
	
	// Reset function
	public function reset() {
		$session = JFactory::getSession();
		if($session->get('com_rscomments.IdComment') != '')
			$session->clear('com_rscomments.IdComment');
		JFactory::getApplication()->close();
	}
	
	// Upload a file
	public function uploadfile() {
		$app		= JFactory::getApplication();
		$db			= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$session	= JFactory::getSession();
		$config 	= RSCommentsHelper::getConfig();
		$file 		= $app->input->files->get('file');
		$IdComment	= $session->get('com_rscomments.IdComment','');
		$id			= $app->input->getInt('rsc_id');
		$option		= $app->input->getString('rsc_option');
		
		if (!$config->enable_upload) 
			return;

		jimport('joomla.filesystem.file');
		$uploadFolder = JPATH_SITE.'/components/com_rscomments/assets/files/';

		$valid 	= true;
		$msg	= '';

		if (!empty($file) && empty($file['error'])) {
			$src		= $file['tmp_name'];
			$filename	= JFile::makeSafe($file['name']);
			$ext		= strtolower(JFile::getExt($filename));
			$filename	= JFile::stripExt($filename);
			
			$extensions = strtolower($config->allowed_extensions);
			$extensions = trim($extensions);
			$extensions = str_replace("\r",'',$extensions);
			$extensions = explode("\n",$extensions);

			if (!empty($extensions) && is_array($extensions) && in_array($ext,$extensions)) {
				$max_size = empty($config->max_size) ? 10 : $config->max_size;
				$max	  = $max_size;
				$max_size = $max_size * 1024 * 1024;

				if ($max_size > $file['size']) {
					// in case we edit the comment
					if($IdComment != '') {
						$query->clear()
							->select($db->qn('file'))
							->from($db->qn('#__rscomments_comments'))
							->where($db->qn('IdComment').' = '.$db->q($IdComment));
						
						$db->setQuery($query);
						$delete_comment = $db->loadObject();
						
						// remove the previous file
						if(JFile::exists($uploadFolder.$delete_comment->file))
							JFile::delete($uploadFolder.$delete_comment->file);

						while (JFile::exists($uploadFolder.$filename.'.'. $ext))
							$filename .= rand(10, 99);

						// upload the new file 
						$dest = $uploadFolder.$filename.'.'.$ext;
						JFile::upload($src, $dest);
					
						//update the database 
						$query->clear()
							->update($db->qn('#__rscomments_comments'))
							->set($db->qn('file').' = '.$db->q($filename.'.'.$ext))
							->where($db->qn('IdComment').' = '.$db->q($IdComment));
						
						$db->setQuery($query);
						$db->execute();
					} else {
						while (JFile::exists($uploadFolder.$filename.'.'. $ext))
							$filename .= rand(10, 99);
						
						$dest = $uploadFolder.$filename.'.'.$ext;
						JFile::upload($src, $dest);
						
						$query->clear()
							->insert($db->qn('#__rscomments_comments'))
							->set($db->qn('url').' = '.$db->q(''))
							->set($db->qn('comment').' = '.$db->q(''))
							->set($db->qn('file').' = '.$db->q($filename.'.'.$ext));
						
						$db->setQuery($query);
						$db->execute();
						$IdComment = $db->insertid();
					}
					
					$session->set('com_rscomments.IdComment', $IdComment);
					$valid = true;
					
				} else { $msg = JText::sprintf('COM_RSCOMMENTS_ERROR_SIZE',$max); $valid = false; }
			} else { $msg = JText::sprintf('COM_RSCOMMENTS_ERROR_EXTENSION',implode(', ',$extensions)); $valid = false; }
		}
		
		echo '<form name="frameform" id="frameform" action="'.JRoute::_('index.php?option=com_rscomments&task=uploadfile').'" method="post" enctype="multipart/form-data">';
		echo '<input type="file" name="file" size="40" />';
		echo '<input type="hidden" name="rsc_id" id="rsc_id" value="" />';
		echo '<input type="hidden" name="rsc_option" id="rsc_option" value="" />';
		echo '</form>';
		
		if ($valid) {
			echo "<script type=\"text/javascript\">
				var object = window.parent.jQuery('.rscomments[data-rsc-id=\"".$id."\"][data-rsc-option=\"".$option."\"]').find('[data-rsc-task=\"validate\"]')[0];
				window.parent.RSComments.save(object);
			</script>";
		} else {
			if($msg != ''){
			echo "
			<script type=\"text/javascript\">
				alert('".$msg."');

				if (window.parent.jQuery('.rscomments[data-rsc-id=\"".$id."\"][data-rsc-option=\"".$option."\"]').find('.rscomments-refresh-captcha').length) {
					window.parent.jQuery('.rscomments[data-rsc-id=\"".$id."\"][data-rsc-option=\"".$option."\"]').find('.rscomments-refresh-captcha').click();
				}
				
				if (window.parent.jQuery('.rscomments[data-rsc-id=\"".$id."\"][data-rsc-option=\"".$option."\"]').find('.g-recaptcha-response').length) {
					var cid = window.parent.jQuery('.rscomments[data-rsc-id=\"".$id."\"][data-rsc-option=\"".$option."\"]').find('.g-recaptcha-response').parents('div[id^=\"rsc-g-recaptcha-\"]').prop('id').replace('rsc-g-recaptcha-', '');
					window.parent.grecaptcha.reset(window.parent.RSCommentsReCAPTCHAv2.ids[cid]);
				}
			</script>";
			}
		}
		$app->close();
	}
	
	// Download file
	public function download() {
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $app->input->getInt('id',0);
		
		if ($id) {
			$query->clear()
				->select($db->qn('file'))
				->from($db->qn('#__rscomments_comments'))
				->where($db->qn('IdComment').' = '.$id);
			
			$db->setQuery($query);
			$file = $db->loadResult();

			$download_folder	= JPATH_SITE.'/components/com_rscomments/assets/files/';
			$fullpath			= $download_folder.$file;			
			
			if (strpos(realpath($fullpath), realpath($download_folder)) !== 0) {
				throw new Exception(JText::_('COM_RSCOMMENTS_ACCESS_DENIED'), 500);
			}
			
			if(is_file($fullpath)) {
				@ob_end_clean();
				$filename = basename($fullpath);
				header("Cache-Control: public, must-revalidate");
				header('Cache-Control: pre-check=0, post-check=0, max-age=0');
				header("Pragma: no-cache");
				header("Expires: 0"); 
				header("Content-Description: File Transfer");
				header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
				if (preg_match('#Opera#', $_SERVER['HTTP_USER_AGENT']))
					header("Content-Type: application/octetstream"); 
				else 
					header("Content-Type: application/octet-stream");
				header("Content-Length: ".(string) filesize($fullpath));
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header("Content-Transfer-Encoding: binary\n");
				RSCommentsHelper::readfile_chunked($fullpath);
				$app->close();
			} else {
				throw new Exception(JText::_('COM_RSCOMMENTS_ACCESS_DENIED'), 500);
			}
		}
		$app->close();
	}
	
	public function approve() {
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $app->input->getInt('id',0);
		$hash	= $app->input->getString('hash','');
		
		// Check for a valid comment
		if (!RSCommentsHelper::valid($id)) {
			$app->enqueueMessage(JText::_('COM_RSCOMMENTS_INVALID_COMMENT'),'error');
			$app->redirect(JURI::root());
		}
		
		if ($this->checkPermission($id, $hash)) {
			$query->clear()
				->select($db->qn('id'))->select($db->qn('option'))->select($db->qn('url'))
				->from($db->qn('#__rscomments_comments'))
				->where($db->qn('IdComment').' = '.$db->q($id));
			$db->setQuery($query);
			$comment = $db->loadObject();
			
			$query->clear()
				->update($db->qn('#__rscomments_comments'))
				->set($db->qn('published').' = '.$db->q(1))
				->where($db->qn('IdComment').' = '.$db->q($id));
			$db->setQuery($query);
			$db->execute();
			
			RSCommentsHelper::removeCache($comment->id.$comment->option);
			
			$app->enqueueMessage(JText::_('COM_RSCOMMENTS_COMMENT_APPROVED'));
			$app->redirect($comment->url ? JURI::root().base64_decode($comment->url).'#rscomment'.$id : JURI::root());
			
		} else {
			$app->enqueueMessage(JText::_('COM_RSCOMMENTS_APPROVAL_ERROR'),'error');
			$app->redirect(JURI::root());
		}
	}
	
	public function delete() {
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $app->input->getInt('id',0);
		$hash	= $app->input->getString('hash','');
		
		// Check for a valid comment
		if (!RSCommentsHelper::valid($id)) {
			$app->enqueueMessage(JText::_('COM_RSCOMMENTS_INVALID_COMMENT'),'error');
			$app->redirect(JURI::root());
		}
		
		if ($this->checkPermission($id, $hash)) {
			$query->clear()
				->select($db->qn('url'))
				->from($db->qn('#__rscomments_comments'))
				->where($db->qn('IdComment').' = '.$db->q($id));
			$db->setQuery($query);
			$url = $db->loadResult();
			
			RSCommentsHelper::remove($id);
			
			$app->enqueueMessage(JText::_('COM_RSCOMMENTS_COMMENT_REMOVED'));
			$app->redirect($url ? JURI::root().base64_decode($url) : JURI::root());
		} else {
			$app->enqueueMessage(JText::_('COM_RSCOMMENTS_REMOVAL_ERROR'),'error');
			$app->redirect(JURI::root());
		}
	}
	
	protected function checkPermission($id, $hash) {
		$config = RSCommentsHelper::getConfig();
		$secret	= JFactory::getConfig()->get('secret');
		
		if ($emails	= $config->notification_emails) {
			if ($emails = explode(',',$emails)) {
				foreach ($emails as $email) {
					$email = trim($email);
					if ($hash == md5($email.$id.$secret)) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
}