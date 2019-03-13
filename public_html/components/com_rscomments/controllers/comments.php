<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsControllerComments extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();

		// Comments Tasks
		$this->registerTask('publish', 	  		'changestatus');
		$this->registerTask('unpublish',   		'changestatus');
		
		$this->registerTask('voteup', 	   		'changevote');
		$this->registerTask('votedown',    		'changevote');
		
		$this->registerTask('openthread',  		'changethreadstatus');
		$this->registerTask('closethread', 		'changethreadstatus');
		
		$this->registerTask('subscribe',   		'subscribe');
		$this->registerTask('unsubscribe', 		'subscribe');
		$this->registerTask('unsubscribeemail', 'subscribe');
	}
	
	// Subscribe user
	public function subscribeuser() {
		// Get the model
		$model 	= $this->getModel('comments');
		$result = $model->subscribeuser();
		
		echo json_encode($result);
		JFactory::getApplication()->close();
	}
	
	// Subscribe / Unsubscribe user
	public function subscribe() {
		$app	= JFactory::getApplication();
		$jinput	= $app->input;
		$task	= $jinput->get('task','');
		$model	= $this->getModel('comments');
		$id		= $jinput->get('id');
		$option = $jinput->get('opt');
		$row	= $task == 'subscribe' ? $model->subscribe() : $model->unsubscribe();
		$return	= array();
		
		if($task == 'subscribe') {
			$msg = $row ? JText::_('COM_RSCOMMENTS_SUBSCRIBED') : JText::_('COM_RSCOMMENTS_ALREADY_SUBSCRIBED');
			$function = 'data-rsc-task="unsubscribe"';
			$text = JText::_('COM_RSCOMMENTS_UNSUBSCRIBE');
		} else {
			$msg = $row ? JText::_('COM_RSCOMMENTS_UNSUBSCRIBED') : JText::_('COM_RSCOMMENTS_ALREADY_UNSUBSCRIBED');
			
			if($task == 'unsubscribeemail') {
				$redirect_url = $jinput->getString('redirect_url');
				if($row == false && !empty($redirect_url)) $msg = JText::_('COM_RSCOMMENTS_INVALID_LINK');
				$app->redirect(JURI::root().base64_decode(JRoute::_($redirect_url)),$msg);
			}
			
			$function = 'data-rsc-task="subscribe"';
			$text = JText::_('COM_RSCOMMENTS_SUBSCRIBE');
		}
		
		$return['message'] = $msg;
		$return['html'] = '<a class="'.RSTooltip::tooltipClass().'" href="javascript:void(0);" '.$function.' title="'.RSTooltip::tooltipText($text).'"><i class="fa fa-envelope"></i> '.$text.'</a>';
		
		echo json_encode($return);
		$app->close();
	}
	
	// Vote comment
	public function changevote() {		
		$app	= JFactory::getApplication();
		$model	= $this->getModel('comments');
		$value	= $app->input->get('task','') == 'voteup' ? 1 : 0;
		$return	= array();
		
		if ($result = $model->vote($app->input->getInt('id'), $value)) {
			$return['vote'] = $result;
		}
		
		echo json_encode($return);
		$app->close();
	}
	
	// Quote a comment
	public function quote() {
		$return	= array();
		$model	= $this->getModel('comments');
		
		if ($row = $model->getComment()) {
			$return['comment'] = $row->comment;
		}
		
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
	
	// Edit comment
	public function edit() {
		$model 			= $this->getModel('comments');
		$session 		= JFactory::getSession();
		$id 			= JFactory::getApplication()->input->get('id');
		$owner 			= RSCommentsHelper::isAuthor($id); 
		$permissions 	= RSCommentsHelper::getPermissions();
		$row 			= $model->getComment();
		$ThreadClosed	= RSCommentsHelper::getThreadStatus($row->id,$row->option);

		if (!(((isset($permissions['edit_own_comment']) && $permissions['edit_own_comment']) && $owner && !$ThreadClosed) || (isset($permissions['edit_comments']) && $permissions['edit_comments'] && !$ThreadClosed))) {
			echo json_encode(array('error' => JText::_('RSC_ERROR_EDIT_PERMISSIONS')));
			JFactory::getApplication()->close();
		}

		$session->set('com_rscomments.IdComment', $row->IdComment);

		$return = array();
		$return['IdComment'] = $row->IdComment;
		$return['IdParent']	 = $row->IdParent;
		$return['name'] 	 = html_entity_decode($row->name,ENT_COMPAT,'UTF-8');
		$return['email'] 	 = $row->email;
		$return['subject'] 	 = html_entity_decode($row->subject,ENT_COMPAT,'UTF-8');
		$return['website'] 	 = $row->website;
		$return['comment'] 	 = $row->comment;
		
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
	
	// Delete comment
	public function remove() {
		$model	= $this->getModel('comments');
		$return	= $model->remove(JFactory::getApplication()->input->getInt('id'));
		
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
	
	// Publish/Unpublish comment
	public function changestatus() {
		$app	= JFactory::getApplication();
		$model	= $this->getModel('comments');
		$value	= $app->input->get('task','') == 'publish' ? 1 : 0;
		$id		= $app->input->getInt('id',0);
		$return = array();
		
		$model->publish($id, $value);
		
		$publish 	= ($value == 1) ? 'fa-minus-circle' : 'fa-check';
		$function 	= ($value == 1) ? ' data-rsc-task="unpublish"' : ' data-rsc-task="publish"'; 
		$message 	= ($value == 1) ? JText::_('COM_RSCOMMENTS_UNPUBLISH') : JText::_('COM_RSCOMMENTS_PUBLISH');
		
		$return['message'] = '<a class="'.RSTooltip::tooltipClass().'" href="javascript:void(0);"'.$function.' title="'.RSTooltip::tooltipText($message).'"><i class="rscomm-meta-icon fa '.$publish.'"></i></a>';
		
		echo json_encode($return);
		$app->close();
	}
	
	// Validate form	
	public function validate() {
		$model 	= $this->getModel('comments');
		$return = $model->validate();
		
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
	
	// Save form
	public function save() {
		$app		= JFactory::getApplication();
		$jinput		= $app->input;
		$model		= $this->getModel('comments');
		$config		= RSCommentsHelper::getConfig();
		$return		= array();
		
		try {
			$return['success']	= true;
			
			$comment	= $model->save();
			$template	= RSCommentsHelper::getTemplate();
			$jform		= $jinput->get('jform', array(), 'array');
			$override	= $jform['override'];
			$class 		= new RSCommentsModelComments($comment->id, $comment->option, $config->nr_comments, $template, $override);
			$pagination = $class->getPagination();
			$last_page 	= $pagination->get('pages.stop');
			$limitstart = $last_page > 0 ? ($last_page -1) * $pagination->limit : 0;
			
			$jinput->set('limitstart',$limitstart);
			$jinput->set('pagination',0);

			$permissions 		= RSCommentsHelper::getPermissions();
			$ThreadClosed		= RSCommentsHelper::getThreadStatus($comment->id,$comment->option);
			$data				= $class->buildDataArray();
			$comments_flat_list = $data;
			$comments_keys 		= array_keys($comments_flat_list);
			$comment_position	= 0;
			
			foreach ($comments_keys as $key => $val) {
				if ($val == $comment->IdComment) {
					$comment_position = $key; 
					break;
				}
			}
			
			if (!empty($data) && array_key_exists($comment->IdComment, $data)) {
				$return['IdComment'] 		= $comment->IdComment;
				$return['Level']  			= !empty($data) ? $data[$comment->IdComment] : 0;
				$return['HtmlComment'] 		= RSCommentsHelper::showComments($comment->option, $comment->id,$template,null,$override,'items',$comment->IdComment);
				$return['NrComments'] 		= $config->nr_comments;
				$return['Option']			= $comment->option;
				$return['OptionId']			= $comment->id;
				$return['Template']			= $template;
				$return['CommentsOrdering']	= $config->default_order;
				$return['Referrer']			= ($comment_position != 0) ? $comments_keys[($comment_position-1)] : 0;
			}
			
			$return['SuccessMessage'] = isset($permissions['autopublish']) && empty($permissions['autopublish']) ? JText::_('COM_RSCOMMENTS_COMMENT_SAVED_UNPUBL') : JText::_('COM_RSCOMMENTS_COMMENT_SAVED');
			
		} catch (Exception $e) {
			$return['success'] = false;
			$return['error'] = $e->getMessage();
		}
		
		echo json_encode($return);
		$app->close();
	}
	
	// Open/Close thread
	public function changethreadstatus() {
		$model	= $this->getModel('comments');
		$app	= JFactory::getApplication();
		$jinput	= $app->input;
		$task	= $jinput->get('task','');
		$id		= $jinput->getString('id',0);
		$option	= $jinput->getString('opt','');
		$return	= $task == 'openthread' ? $model->openthread() : $model->closethread();
		$override = $jinput->getInt('override','');
		$response = array();
		
		if($task == 'openthread') {
			$msg		= $return['message'];
			$function	= $return['status'] ? 'data-rsc-task="close"' : 'data-rsc-task="open"';
			$text		= JText::_('COM_RSCOMMENTS_CLOSE_THREAD');
		} else {
			$msg		= $return['message'];
			$function	= $return['status'] ? 'data-rsc-task="open"' : 'data-rsc-task="close"';
			$text		= JText::_('COM_RSCOMMENTS_OPEN_THREAD');
		}
		
		$response['status']		= $return['status'];
		$response['message']	= $msg;
		$response['link']		= '<a class="'.RSTooltip::tooltipClass().'" href="javascript:void(0);" '.$function.' data-rsc-override="'.$override.'" title="'.RSTooltip::tooltipText($text).'"><i class="fa fa-tag"></i> '.$text.'</a>';
		$response['form']		= RSCommentsHelper::displayForm($option, $id, $override);
		
		echo json_encode($response);
		$app->close();
	}
	
	// Get user comments for CB
	public function cbcomments() {
		$jinput	= JFactory::getApplication()->input;
		$userid	= $jinput->getString('userid');
		$start	= $jinput->getString('start');
		$limit	= $jinput->getString('limit');
		
		$comments = RSCommentsHelper::getCBUserComments($userid,$start,$limit);
		echo json_encode($comments);
		JFactory::getApplication()->close();
	}
	
	// Report comment
	public function report() {
		$model	= $this->getModel('comments');		
		$data	= $model->report();
		
		echo json_encode($data); 
		JFactory::getApplication()->close();
	}
}