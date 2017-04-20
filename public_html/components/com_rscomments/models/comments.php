<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelComments extends JModelLegacy
{
	protected $_data = null;
	protected $_total = 0;
	protected $_query = '';
	protected $_pagination = null;
	protected $_id = null;
	protected $_option = null;
	protected $_template = null;
	protected $_overwrite = null;
	protected $_IdComment = null;
	
	public function __construct($id = null, $option = null, $page = null, $template = null, $overwrite = null, $IdComment = null) {
		parent::__construct();
		$this->_id 			= $id;
		$this->_option 		= $option;
		$this->_template 	= $template;
		$this->_overwrite 	= $overwrite;
		$this->_IdComment 	= $IdComment;
		$app				= JFactory::getApplication();
		$jinput				= $app->input;
		$pagination			= $page != null ? $page : $app->getCfg('list_limit');
		
		// Get pagination request variables 
		$limit 		= $jinput->getInt('limit', $pagination); 
		$limitstart = $jinput->getInt('limitstart', 0); 
		// In case limit has been changed, adjust it 
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); 
		
		$this->setState('rscomments.comments.limit', $limit);
		$this->setState('rscomments.comments.limitstart', $limitstart);
	}
	
	// Build commenting levels
	public function buildDataArray() {
		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$permissions	= RSCommentsHelper::getPermissions();
		$config			= RSCommentsHelper::getConfig();
		$order			= $config->default_order;
		$levels			= array();
		
		$query->clear()
			->select($db->qn('IdComment'))->select($db->qn('IdParent'))
			->from($db->qn('#__rscomments_comments'))
			->where($db->qn('option').' = '.$db->q($this->_option))
			->where($db->qn('id').' = '.$db->q($this->_id))
			->order($db->qn('date').' '.$db->escape($order));
		
		if ((isset($permissions['publish_comments']) && $permissions['publish_comments']) || RSCommentsHelper::admin()) {
			$query->where($db->qn('published').' IN (0,1)');
		} else {
			$query->where($db->qn('published').' = 1');
		}
		
		$db->setQuery($query);
		$tmp = $db->loadObjectList();
		$this->renderTree($tmp, $tree, $levels);
		
		return $levels;
	}

	protected function renderTree($tmp, &$tree = array(), &$levels = array(), $IdParent = 0, $level = 0) {
		foreach ($tmp as $row) {
			if ($row->IdParent == $IdParent) {
				$levels[$row->IdComment] 	= $level;
				$tree[$row->IdComment] 		= array();
				$this->renderTree($tmp, $tree[$row->IdComment], $levels, $row->IdComment, $level+1);
			}
		}
	}

	protected function renderFlatTree($tree) {
		$list = array();
		foreach($tree as $key => $children) {
			$list[] = $key;
			if (count($children)) {
				$tmp_list = $this->renderFlatTree($children);
				foreach ($tmp_list as $tmp_key)
					$list[] = $tmp_key;
			}
		}
	
		return $list;
	}
	
	// Build comments
	public function getComments() {
		$db				 = JFactory::getDbo();
		$query			 = $db->getQuery(true);
		$jinput			 = JFactory::getApplication()->input;
		$this->_data	 = $this->buildDataArray();
		$commentIds		 = array();
		$return_comments = array();
		
		if (!is_null($this->_IdComment)) {
			$commentIds = array($this->_IdComment);
		} else {
			$commentIds = array_slice(array_keys($this->_data), $jinput->getInt('limitstart'), $this->getState('rscomments.comments.limit'));
		}
		JArrayHelper::toInteger($commentIds);
		
		if (empty($commentIds)) {
			return $return_comments;
		}
			
		$query->clear()
			->select('*')
			->from($db->qn('#__rscomments_comments'))
			->where($db->qn('IdComment').' IN ('.implode(',',$commentIds).')')
			->order('FIELD('.$db->qn('IdComment').', '.implode(',',$commentIds).')');		
		
		$db->setQuery($query);
		if ($paged_comments = $db->loadObjectList()) {
			foreach($paged_comments as $comment) {
				$pos = (int) RSCommentsHelper::getPositiveVotes($comment->IdComment);
				$neg = (int) RSCommentsHelper::getNegativeVotes($comment->IdComment);
				
				$comment->pos	= $pos;
				$comment->neg	= $neg;
				$comment->level = $this->_data[$comment->IdComment];
				$return_comments[] = $comment;
			}
		}
		
		return $return_comments;
	}
	
	// Get total number of comments
	public function getTotal() {
		if (empty($this->_total))
			$this->_total = count($this->buildDataArray());

		return $this->_total;
	}
	
	// Get commenting pagination
	public function getPagination() {
		if (empty($this->_pagination)) {
			require_once JPATH_SITE.'/components/com_rscomments/helpers/pagination.php';
			$this->_pagination = new RSPagination($this->getTotal(), $this->getState('rscomments.comments.limitstart'), $this->getState('rscomments.comments.limit'),$this->_option,$this->_id,$this->_template,$this->_overwrite);
		}
		
		return $this->_pagination;
	}

	// Get comment details
	public function getComment() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rscomments_comments'))
			->where($db->qn('IdComment').' = '.$id);
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	// Subscribe user
	public function subscribeuser() {
		jimport('joomla.mail.helper');
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$jinput	= JFactory::getApplication()->input;
		$name	= $jinput->getString('name');
		$email	= $jinput->getString('email');
		$opt	= $jinput->get('theoption');
		$id		= $jinput->get('theid');
		
		if (!JMailHelper::isEmailAddress($email)) {
			return array('status' => false, 'message' => JText::_('COM_RSCOMMENTS_INVALID_EMAIL_ADDRESS'));
		}
		
		$query->clear()
			->select('COUNT('.$db->qn('IdSubscription').')')
			->from($db->qn('#__rscomments_subscriptions'))
			->where($db->qn('email').' = '.$db->q($email))
			->where($db->qn('option').' = '.$db->q($opt))
			->where($db->qn('id').' = '.$db->q($id));
		
		$db->setQuery($query,0,1);
		$already = $db->loadResult();
		
		if ($already == 0) {
			$query->clear()
				->insert($db->qn('#__rscomments_subscriptions'))
				->set($db->qn('email').' = '.$db->q($email))
				->set($db->qn('option').' = '.$db->q($opt))
				->set($db->qn('id').' = '.$db->q($id))
				->set($db->qn('name').' = '.$db->q($name));
			
			$db->setQuery($query);
			$db->execute();
			
			return array('status' => true, 'message' => JText::_('COM_RSCOMMENTS_SUBSCRIBED'));
		} else {
			return array('status' => false, 'message' => JText::_('COM_RSCOMMENTS_ALREADY_SUBSCRIBED'));
		}
	}
	
	// Subscribe user
	public function subscribe() {
		$jinput	= JFactory::getApplication()->input;
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $jinput->get('id');
		$option	= $jinput->get('opt');
		
		$query->clear()
			->select('COUNT('.$db->qn('IdSubscription').')')
			->from($db->qn('#__rscomments_subscriptions'))
			->where($db->qn('email').' = '.$db->q($user->get('email')))
			->where($db->qn('option').' = '.$db->q($option))
			->where($db->qn('id').' = '.$db->q($id));
		
		$db->setQuery($query,0,1);
		$already = $db->loadResult();

		if($already == 0) {
			$query->clear()
				->insert($db->qn('#__rscomments_subscriptions'))
				->set($db->qn('email').' = '.$db->q($user->get('email')))
				->set($db->qn('option').' = '.$db->q($option))
				->set($db->qn('id').' = '.$db->q($id))
				->set($db->qn('name').' = '.$db->q($user->get('name')));
			
			$db->setQuery($query);
			$db->execute();
			return true;
		} else {
			return false;
		}
	}

	
	
	// Unsubscribe function
	public function unsubscribe() {
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$jinput	= JFactory::getApplication()->input;
		$id		= $jinput->get('id');
		$option	= $jinput->get('opt');
		$task	= $jinput->get('task');
		$get 	= JRequest::get('get');
		
		// unsubscribing from email link
		if ($task == 'unsubscribeemail') {
			$query->clear()
				->select($db->qn('email'))
				->from($db->qn('#__rscomments_subscriptions'))
				->where('MD5(CONCAT('.$db->qn('email').','.$db->qn('option').','.$db->qn('id').')) = '.$db->q($jinput->getString('hash')));
			
			$db->setQuery($query);
			$email = $db->loadResult();
			
			if (!empty($email) && $email == $db->escape($jinput->getString('uemail'))) {
				$query->clear()
					->delete()
					->from($db->qn('#__rscomments_subscriptions'))
					->where($db->qn('id').' = '.$db->q($id))
					->where($db->qn('option').' = '.$db->q($option))
					->where($db->qn('email').' = '.$db->q($jinput->getString('uemail')));
				
				$db->setQuery($query);
				$db->execute();
				return true;
			} else {
				return false;
			}
		} else  {
			// unsubscribing from unsubscribe button
			$query->clear()
				->select('COUNT('.$db->qn('IdSubscription').')')
				->from($db->qn('#__rscomments_subscriptions'))
				->where($db->qn('email').' = '.$db->q($user->get('email')))
				->where($db->qn('option').' = '.$db->q($option))
				->where($db->qn('id').' = '.$db->q($id));
			
			
			$db->setQuery($query,0,1);
			$already = $db->loadResult();
			
			if ($already == 0) {
				return false;
			} else {
				$query->clear()
					->delete()
					->from($db->qn('#__rscomments_subscriptions'))
					->where($db->qn('id').' = '.$db->q($id))
					->where($db->qn('option').' = '.$db->q($option))
					->where($db->qn('email').' = '.$db->q($user->get('email')));
				
				$db->setQuery($query);
				$db->execute();
				return true;
			}
		}
	}
	
	// Vote comment
	public function vote($id, $state = 1) {
		$user			= JFactory::getUser();
		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$permissions	= RSCommentsHelper::getPermissions();
		
		if (!isset($permissions['vote_comments'])) {
			return false;
		}

		$state = ($state == 1) ? 'positive' : 'negative';

		if ($user->get('guest')) {
			$query->clear()
				->select($db->qn('IdVote'))
				->from($db->qn('#__rscomments_votes'))
				->where($db->qn('IdComment').' = '.$db->q($id))
				->where($db->qn('ip').' = '.$db->q(RSCommentsHelper::getIp(true)));
		} else {
			$query->clear()
				->select($db->qn('IdVote'))
				->from($db->qn('#__rscomments_votes'))
				->where($db->qn('IdComment').' = '.$db->q($id))
				->where('('.$db->qn('ip').' = '.$db->q(RSCommentsHelper::getIp(true)).' OR '.$db->qn('uid').' = '.$db->q($user->get('id')).')');
		}
		
		$db->setQuery($query);
		$voted = $db->loadResult();
		
		if (empty($voted)) {
			$query->clear()
				->insert($db->qn('#__rscomments_votes'))
				->set($db->qn('IdComment').' = '.$db->q($id))
				->set($db->qn('uid').' = '.$db->q($user->get('id')))
				->set($db->qn('ip').' = '.$db->q(RSCommentsHelper::getIp(true)))
				->set($db->qn('value').' = '.$db->q($state));
			
			$db->setQuery($query);
			$db->execute();
		}
		
		$query->clear()
			->select('COUNT('.$db->qn('IdVote').')')
			->from($db->qn('#__rscomments_votes'))
			->where($db->qn('IdComment').' = '.(int) $id)
			->where($db->qn('value').' = '.$db->q('positive'));
		$db->setQuery($query);
		$pos = $db->loadResult();
		
		$query->clear()
			->select('COUNT('.$db->qn('IdVote').')')
			->from($db->qn('#__rscomments_votes'))
			->where($db->qn('IdComment').' = '.(int) $id)
			->where($db->qn('value').' = '.$db->q('negative'));
		$db->setQuery($query);
		$neg = $db->loadResult();
		
		$vote_yes_image = RSCommentsHelper::ImagePath('voteyes.png');
		$vote_no_image 	= RSCommentsHelper::ImagePath('voteno.png');
		
		$votes = $pos - $neg;
		
		if ($votes > 0) {
			return '<i class="rscomm-meta-icon fa fa-thumbs-up"></i> <span class="rsc_green">'.$votes.'</span>';
		} else {
			return '<i class="rscomm-meta-icon fa fa-thumbs-down"></i> <span class="rsc_red">'.$votes.'</span>';
		}
	}
	
	// Delete comment 
	public function remove($id) {
		jimport('joomla.filesystem.file');
		
		$db					= JFactory::getDbo();
		$query				= $db->getQuery(true);
		$remove 			= array();
		$owner 				= RSCommentsHelper::isAuthor($id); 
		$permissions 		= RSCommentsHelper::getPermissions();
		$download_folder	= JPATH_SITE.'/components/com_rscomments/assets/files/';
		
		if (!(((isset($permissions['delete_own_comment']) && $permissions['delete_own_comment']) && $owner ) || (isset($permissions['delete_comments']) && $permissions['delete_comments'] ))) {
			return array('error'=> JText::_('COM_RSCOMMENTS_ERROR_DELETE_PERMISSIONS'));
		}

		// select and delete comments children (replies)
		$query->clear()
			->select($db->qn('IdComment'))
			->from($db->qn('#__rscomments_comments'))
			->where($db->qn('IdParent').' = '.(int) $id);
		
		$db->setQuery($query);
		$children = $db->loadObjectList();
		
		if ($children) {
			foreach($children as $child) {
				$remove[] = $child->IdComment;
				if ($newchild = $this->remove($child->IdComment)) {
					$remove = array_merge($remove, $newchild);
				}
			}
		}
		
		$query->clear()
			->select($db->qn('file'))
			->from($db->qn('#__rscomments_comments'))
			->where($db->qn('IdComment').' = '.(int) $id);
		
		$db->setQuery($query);
		$file = $db->loadResult();

		if (!empty($file) && file_exists($download_folder.$file)) {
			JFile::delete($download_folder.$file);
		}
		
		$query->clear()
			->delete()
			->from($db->qn('#__rscomments_votes'))
			->where($db->qn('IdComment').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		$query->clear()
			->delete()
			->from($db->qn('#__rscomments_comments'))
			->where($db->qn('IdComment').' = '.(int) $id);
		
		$db->setQuery($query);
		if (!$db->execute()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		return $remove;
	}
	
	
	// Publis/Unpublish comments 
	public function publish($id, $publish = 1) {
		$permissions	= RSCommentsHelper::getPermissions();
		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		
		if(!isset($permissions['publish_comments'])) 
			return;
		
		$query->clear()
			->update($db->qn('#__rscomments_comments'))
			->set($db->qn('published').' = '.(int) $publish)
			->where($db->qn('IdComment').' = '.(int) $id);
		
		$db->setQuery($query);
		if (!$db->execute()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		return $id;
	}
	
	// Validate comment form
	public function validate() {
		jimport('joomla.mail.helper');
		
		$config 		= RSCommentsHelper::getConfig();
		$permissions 	= RSCommentsHelper::getPermissions();
		$row 		 	= JTable::getInstance('Comment','RscommentsTable');
		$app			= JFactory::getApplication();
		$user			= JFactory::getUser();
		$jinput			= $app->input;
		$jform			= $jinput->get('jform', array(), 'array');
		$ip				= RSCommentsHelper::getIp(true);
		$return			= array();
		
		$return['success']	= true;
		$return['errors']	= array();
		
		$row->load($jinput->get('IdComment'));

		if (!$row->bind($jform)) {
			$return['errors'][] = $row->getError();
		}

		if (empty($row->ip)) {
			$row->ip = $ip;
		}
		
		// Check for flood commenting
		if (!RSCommentsHelper::flood($row->ip) && (isset($permissions['flood_control']) && $permissions['flood_control']) && empty($row->IdComment)) {
			$return['errors'][] = addslashes(JText::sprintf('COM_RSCOMMENTS_WAIT_FOR_COMMENT',intval($config->flood_interval)));
		}

		// Check for comment length
		if (function_exists('mb_strlen')) {
			if (mb_strlen($row->comment,'UTF-8') < $config->min_comm_len) {
				$return['errors'][] = JText::_('COM_RSCOMMENTS_COMMENT_TO_SHORT',true);		
				$return['fields'][] = 'comment';
			}
			
			if (mb_strlen($row->comment,'UTF-8') > $config->max_comm_len) {
				$return['errors'][] = JText::_('COM_RSCOMMENTS_COMMENT_TO_LONG',true);
				$return['fields'][] = 'comment';
			}
		} else {
			if (strlen(utf8_decode($row->comment)) < $config->min_comm_len) {
				$return['errors'][] = JText::_('COM_RSCOMMENTS_COMMENT_TO_SHORT',true);
				$return['fields'][] = 'comment';
			}
			
			if (strlen(utf8_decode($row->comment)) > $config->max_comm_len) {
				$return['errors'][] = JText::_('COM_RSCOMMENTS_COMMENT_TO_LONG',true);
				$return['fields'][] = 'comment';
			}
		}
	
		// Check for blocked users
		if (trim($config->blocked_users) != '') {
			$bad_users = explode("\n",$config->blocked_users);
			if (!empty($bad_users)) {
				foreach($bad_users as $bad_user) {
					if ($bad_user == $user->get('username')) {
						$return['errors'][] = JText::_('COM_RSCOMMENTS_BLOCKED_USER',true);
					}
				}
			}
		}
		
		// Check for blocked IPs
		if (trim(trim($config->blocked_ips) != '')) {
			$bad_ips = explode("\n",$config->blocked_ips);
			if (!empty($bad_ips)) {
				foreach($bad_ips as $bad_ip) {
					if ($bad_ip == $ip) {
						$return['errors'][] = JText::_('COM_RSCOMMENTS_BLOCKED_IP',true);
					}
				}
			}
		}

		// Remove any bad words
		if (isset($permissions['censored']) && $permissions['censored']) {
			$row->comment = RSCommentsHelper::censor($row->comment);
			$row->subject = RSCommentsHelper::censor($row->subject);
		}

		// Check for forbidden names
		if (isset($permissions['check_names']) && $permissions['check_names']) {
			if (RSCommentsHelper::forbiddenNames($row->name)) {
				$return['errors'][] = JText::_('COM_RSCOMMENTS_BAD_NAME',true);
				$return['fields'][] = 'name';
			}
		}
		
		// Check for name validation
		if (empty($row->name)) {
			$return['errors'][] = JText::_('COM_RSCOMMENTS_NO_NAME',true);
			$return['fields'][] = 'name';
		}
		
		// Check for email validation
		if (!empty($row->email) && !JMailHelper::isEmailAddress($row->email)) {
			$return['errors'][] = JText::_('COM_RSCOMMENTS_NO_VALID_EMAIL',true);
			$return['fields'][] = 'email';
		} elseif(empty($row->email)) {
			$return['errors'][] = JText::_('COM_RSCOMMENTS_NO_EMAIL',true);
			$return['fields'][] = 'email';
		}
		
		// Check for comment validation
		if (empty($row->comment)) {
			$return['errors'][] = JText::_('COM_RSCOMMENTS_NO_COMMENT',true);
			$return['fields'][] = 'comment';
		}
		
		// Check for terms validation
		if ($config->terms) {
			if (empty($jform['rsc_terms'])) {
				$return['errors'][] = JText::_('COM_RSCOMMENTS_AGREE_TERMS',true);
				$return['fields'][] = 'rsc_terms';
			}
		}

		// Check for captcha validation
		if (isset($permissions['captcha']) && $permissions['captcha']) {
			if ($config->captcha == 0) {
				require_once JPATH_SITE.'/components/com_rscomments/helpers/securimage/securimage.php';
				$captcha_image = new JSecurImage();
				$valid = $captcha_image->check($jform['captcha'],'form');
				if (!$valid) {
					$return['errors'][] = JText::_('COM_RSCOMMENTS_INVALID_CAPTCHA',true);
					$return['fields'][] = 'captcha';
				}
			} elseif ($config->captcha == 1) {
				require_once JPATH_SITE.'/components/com_rscomments/helpers/recaptcha/recaptchalib.php';
				$privatekey = $config->rec_private;

				$response = RSCommentsReCAPTCHA::checkAnswer($privatekey, RSCommentsHelper::getIp(true), @$jinput->getString('recaptcha_challenge_field'), @$jinput->getString('recaptcha_response_field'));
				if ($response === false || !$response->is_valid) {
					$return['errors'][] = JText::_('COM_RSCOMMENTS_INVALID_CAPTCHA',true);
				}
			} else {
				try {
					$response = $jinput->get('g-recaptcha-response', '', 'raw');
					$ip		  = RSCommentsHelper::getIp(true);
					$secretKey= $config->recaptcha_new_secret_key;
					
					jimport('joomla.http.factory');
					$http = JHttpFactory::getHttp();
					if ($request = $http->get('https://www.google.com/recaptcha/api/siteverify?secret='.urlencode($secretKey).'&response='.urlencode($response).'&remoteip='.urlencode($ip))) {
						$json = json_decode($request->body);
					}
				} catch (Exception $e) {
					$return['errors'][] = $e->getMessage();
				}
				
				if (!$json->success) {
					$return['errors'][] = JText::_('COM_RSCOMMENTS_INVALID_CAPTCHA',true);
				}
			}
		}

		// Check for website validation
		if (RSCommentsHelper::getConfig('enable_website_field') && $row->website != '' && !RSCommentsHelper::checkURL($row->website)) {
			$return['errors'][] = JText::_('COM_RSCOMMENTS_INVALID_WEBSITE', true);
			$return['fields'][] = 'website';
		}
		
		if (count($return['errors']) > 0) {
			$return['success'] = false;
		}
		
		if ($return['success']) {
			JFactory::getSession()->set('com_rscomments.validated',md5(serialize($jform)));
		}
		
		return $return;
	}
	
	// Save comment
	public function save() {
		jimport('joomla.mail.helper');
		
		$app			= JFactory::getApplication();
		$user 		 	= JFactory::getUser();
		$db 		 	= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$cfg 		 	= JFactory::getConfig();
		$jform 		 	= $app->input->get('jform',array(),'array');
		$permissions 	= RSCommentsHelper::getPermissions();
		$row 		 	= JTable::getInstance('Comment','RscommentsTable');
		$session 		= JFactory::getSession();
		$config			= RSCommentsHelper::getConfig();
		$comment 	 	= $jform['comment'];
		$isNew			= empty($jform['IdComment']);
		$root			= JUri::getInstance()->toString(array('scheme','host','port'));

		if (!isset($permissions['new_comments'])) {
			throw new Exception(JText::_('COM_RSCOMMENTS_CANNOT_POST_COMMENT'));
		}

		if ($session->get('com_rscomments.validated') != md5(serialize($jform))) {
			throw new Exception(JText::_('COM_RSCOMMENTS_INVALID_COMMENT'));
		} else {
			$session->clear('com_rscomments.validated');
		}
		
		// Get the comment ID
		$IdComment = $session->get('com_rscomments.IdComment','');

		// Clear the session
		if ($session->get('com_rscomments.IdComment')) {
			$session->clear('com_rscomments.IdComment');
		}

		$row->load($IdComment);
		
		// Set the comment ID
		$jform['IdComment'] = $IdComment;
		
		if (!$row->bind($jform)) {
			throw new Exception($row->getError());
		}

		$comment		= str_replace('+','%2B',$comment);
		$row->comment 	= urldecode($comment);

		if (isset($permissions['censored']) && $permissions['censored']) {
			$row->subject = RSCommentsHelper::censor($row->subject);
			$row->comment = RSCommentsHelper::censor($row->comment);
		}

		$row->email 	= trim($row->email);
		$row->option 	= $jform['obj_option'];
		$row->id 		= $jform['obj_id'];

		if ($jform['IdComment'] == 0 || empty($jform['IdComment']) || ($IdComment != '' && !empty($row->file))) {
			$row->uid 		= $user->get('id');
			$row->ip 		= RSCommentsHelper::getIp(true);
			$row->date 		= JFactory::getDate()->toSql();
			$row->published = (isset($permissions['autopublish']) && $permissions['autopublish']) ? 1 : 0;
		}
		
		if (!empty($jform['IdComment'])) {
			$row->modified_by = JFactory::getUser()->get('id');
			$row->modified = JFactory::getDate()->toSql();
		}

		// Clean comment
		if ($config->enable_bbcode == 0 && !isset($permissions['bbcode'])) {
			$row->comment = RSCommentsHelper::cleanComment($row->comment);
		}

		// Akismet protection
		if (!empty($config->akismet_key) && !RSCommentsHelper::admin()) {
			$url  = JURI::root();
			$data = array();
			
			$data['comment_type'] 			= 'comment';
			$data['comment_author'] 		= $row->name; 
			$data['comment_author_email']	= $row->email;
			$data['comment_content'] 		= $row->comment;
			$data['comment_author_url'] 	= $config->enable_title_field == 1 ? $row->website : '';
			$data['permalink'] 				= $url;
			
			try {
				$akismet = new RSJAkismet($config->akismet_key, $url);
				
				if ($akismet->valid_key()) {
					if ($akismet->is_spam($data)) {
						$row->published = 0;
					}
				}
			} catch (Exception $e) {}
		}

		$auto_subscribe_thread = isset($permissions['auto_subscribe_thread']) ? (int) $permissions['auto_subscribe_thread'] : 0;
		if ((isset($jform['subscribe_thread']) && $jform['subscribe_thread'] && $config->show_subcription_checkbox == 1) || $auto_subscribe_thread) {
			// Autosubscribe user
			if (!empty($user->id)) {
				$app->input->set('opt', $jform['obj_option']);
				$app->input->set('id', $jform['obj_id']);
				$this->subscribe();
			} else {
				$app->input->set('theoption', $jform['obj_option']);
				$app->input->set('theid', $jform['obj_id']);
				$app->input->set('name', $jform['name']);
				$app->input->set('email', $jform['email']);
				$this->subscribeuser();
			}
		}
		
		$filter = new JFilterInput();
		$row->name = $filter->clean($row->name);
		$row->name = htmlentities($row->name,ENT_COMPAT,'UTF-8');
		
		$row->subject = $filter->clean($row->subject);
		$row->subject = htmlentities($row->subject,ENT_COMPAT,'UTF-8');
		
		$row->website = RSCommentsHelper::checkURL($row->website) ? $db->escape($row->website) : '';

		if ($row->store()) {
			// Send email notifications
			if ($config->email_notification && $isNew) {
				$secret		= $cfg->get('secret');
				$message	= RSCommentsHelper::getMessage('notification_message');
				$preview 	= '<a href="'.JURI::root().base64_decode($row->url).'" target="_blank">'.JURI::root().base64_decode($row->url).'</a>'; 
				$comment 	= RSCommentsHelper::parseComment($row->comment,$permissions);
				$comment 	= RSCommentsEmoticons::cleanText($comment);
				$username	= empty($user->username) ? JText::_('COM_RSCOMMENTS_GUEST') : $user->username;
				$uemail		= empty($user->id) ? $row->email : $user->email;
				
				$replace = array('{username}', '{email}', '{ip}', '{link}', '{message}');
				$with = array($username, $uemail, $row->ip, $preview, $comment);
				
				$message = str_replace($replace,$with,$message);
				$message = html_entity_decode($message,ENT_COMPAT,'UTF-8');
				$emails	 = $config->notification_emails;
				
				if (!empty($emails)) {
					$emails = explode(',',$emails);
					if (!empty($emails)) {
						foreach ($emails as $email) {
							$email		= trim($email);
							$hash		= md5($email.$row->IdComment.$secret);
							$approveURL	= $root.JRoute::_('index.php?option=com_rscomments&task=approve&id='.$row->IdComment.'&hash='.$hash, false);
							$approve	= $row->published == 0 ? '<a href="'.$approveURL.'" target="_blank">'.JText::_('COM_RSCOMMENTS_APPROVE_COMMENT').'</a>' : '';
							$deleteURL	= $root.JRoute::_('index.php?option=com_rscomments&task=delete&id='.$row->IdComment.'&hash='.$hash, false);
							$delete		= '<a href="'.$deleteURL.'" target="_blank">'.JText::_('COM_RSCOMMENTS_DELETE_COMMENT').'</a>';
							$message	= str_replace(array('{approve}', '{delete}'), array($approve, $delete), $message);
							
							$mailer	= JFactory::getMailer();
							$mailer->sendMail($cfg->get('mailfrom'), $cfg->get('fromname'), $email, JText::_('COM_RSCOMMENTS_NOTIFICATION_SUBJECT'), $message, 1);
						}
					}
				}
			}
			
			// Send email subscriptions
			if ($isNew && $row->published) {
				$query->clear()
					->select($db->qn('name'))->select($db->qn('email'))
					->select($db->qn('option'))->select($db->qn('id'))
					->from($db->qn('#__rscomments_subscriptions'))
					->where($db->qn('id').' = '.(int) $row->id)
					->where($db->qn('option').' = '.$db->q($row->option));
				
				$db->setQuery($query);
				$subscribers = $db->loadObjectList();

				if (!empty($subscribers)) {
					foreach($subscribers as $subscriber) {
						$hash				= md5($subscriber->email.$subscriber->option.$subscriber->id);
						$msg 	 			= RSCommentsHelper::getMessage('subscription_message');
						$preview 			= '<a href="'.JURI::root().base64_decode($row->url).'" target="_blank">'.JURI::root().base64_decode($row->url).'</a>'; 
						$unsubscribelink 	= '<a href="'.JURI::root().'index.php?option=com_rscomments&task=comments.unsubscribeemail&opt='.$row->option.'&id='.$row->id.'&uemail='.urlencode($subscriber->email).'&hash='.$hash.'&redirect_url='.$row->url.'">'.JURI::root().'index.php?option=com_rscomments&task=comments.unsubscribeemail&opt='.$row->option.'&id='.$row->id.'&uemail='.$subscriber->email.'&hash='.$hash.'&redirect_url='.$row->url.'</a>';
						$replace 			= array('{name}','{author}','{message}','{link}','{unsubscribelink}');
						$with 	 			= array($subscriber->name,'"'.$row->name.'"',RSCommentsHelper::parseComment($row->comment,$permissions),$preview,$unsubscribelink);
						$msg 	 			= str_replace($replace,$with,$msg);
						$msg 	 			= html_entity_decode($msg,ENT_COMPAT,'UTF-8');
						
						$mailer	= JFactory::getMailer();
						$mailer->sendMail($cfg->get('mailfrom'), $cfg->get('fromname'), $subscriber->email , JText::_('COM_RSCOMMENTS_NEW_COMMENT_SUBJECT') , $msg , 1);
					}
				}
			}
			
			return $row;
		} else {
			throw new Exception($row->getError());
		}
	}
	
	// Open thread
	public function openthread() {
		$app			= JFactory::getApplication();
		$jinput			= $app->input;
		$db 		 	= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$option 		= $jinput->getString('opt');
		$id 			= $jinput->getString('id');
		$permissions 	= RSCommentsHelper::getPermissions();

		if ($permissions['close_thread']) {
			if (!empty($id) && !empty($option)) {
				$msg = JText::_('COM_RSCOMMENTS_ERROR_OPENTHREAD');
				
				$query->clear()
					->select('COUNT('.$db->qn('id').')')
					->from($db->qn('#__rscomments_threads'))
					->where($db->qn('id').' = '.$db->q($id))
					->where($db->qn('option').' = '.$db->q($option));
				$db->setQuery($query);
				$thread = $db->loadResult();
				
				if ($thread > 0){
					$query->clear()
						->delete()
						->from($db->qn('#__rscomments_threads'))
						->where($db->qn('id').' = '.$db->q($id))
						->where($db->qn('option').' = '.$db->q($option));
					
					$db->setQuery($query);
					$db->execute();
					$msg = JText::_('COM_RSCOMMENTS_STATUS_SUCCESS_THREAD_OPEN');
					return array('status' => true, 'message' => $msg);
				}
			} else {
				return array('status' => false, 'message' => JText::_('COM_RSCOMMENTS_ERROR_INVALID_DETAILS'));
			}
		} else {
			return array('status' => false, 'message' => JText::_('COM_RSCOMMENTS_ERROR_CLOSE_THREAD_NOT_ALLOWED'));
		}
	}
	
	// Close thread
	public function closethread() {
		$app			= JFactory::getApplication();
		$jinput			= $app->input;
		$db 		 	= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$option 		= $jinput->getString('opt');
		$id 			= $jinput->getString('id');
		$permissions 	= RSCommentsHelper::getPermissions();

		if ($permissions['close_thread']) {
			if (!empty($id) && !empty($option)) {
				$msg = JText::_('COM_RSCOMMENTS_ERROR_CLOSETHREAD');
				
				$query->clear()
					->select('COUNT('.$db->qn('id').')')
					->from($db->qn('#__rscomments_threads'))
					->where($db->qn('id').' = '.$db->q($id))
					->where($db->qn('option').' = '.$db->q($option));
				$db->setQuery($query);
				$thread = $db->loadResult();
				
				if ($thread == 0) {
					$query->clear()
						->insert($db->qn('#__rscomments_threads'))
						->set($db->qn('id').' = '.$db->q($id))
						->set($db->qn('option').' = '.$db->q($option));
					
					$db->setQuery($query);
					$db->execute();
					$msg = JText::_('COM_RSCOMMENTS_STATUS_SUCCESS_THREAD_CLOSED');
					return array('status' => true, 'message' => $msg);
				}
			} else {
				return array('status' => false, 'message' => JText::_('COM_RSCOMMENTS_ERROR_INVALID_DETAILS'));
			}
		} else {
			return array('status' => false, 'message' => JText::_('COM_RSCOMMENTS_ERROR_CLOSE_THREAD_NOT_ALLOWED'));
		}
	}
	
	// Report comment
	public function report() {
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$config	= RSCommentsHelper::getConfig();
		$id		= $input->getId('id',0);
		$report	= $input->getString('report','');
		$uid	= JFactory::getUser()->get('id');
		$ip		= RSCommentsHelper::getIp(true);
		$root	= JURI::getInstance()->toString(array('scheme','host'));
		$data	= array();
		
		if (!$config->enable_reports) {
			$data['success'] = false;
			$data['message'] = JText::_('COM_RSCOMMENTS_REPORTS_DISABLED',true);
			
			return $data;
		}
		
		$data['success'] = true;
		
		if ($config->enable_captcha_reports) {
			if ($config->captcha == 0) {
				require_once JPATH_SITE.'/components/com_rscomments/helpers/securimage/securimage.php';
				$captcha_image = new JSecurImage();
				$valid = $captcha_image->check($input->getString('captcha',''),'report');
				if (!$valid) {
					$data['success'] = false;
					$data['message'] = JText::_('COM_RSCOMMENTS_INVALID_CAPTCHA',true);
				}
			} else if ($config->captcha == 1) {
				require_once JPATH_SITE.'/components/com_rscomments/helpers/recaptcha/recaptchalib.php';
				
				$response = RSCommentsReCAPTCHA::checkAnswer($config->rec_private, RSCommentsHelper::getIp(true), @$input->getString('recaptcha_challenge_field'), @$input->getString('recaptcha_response_field'));
				if ($response === false || !$response->is_valid) {
					$data['success'] = false;
					$data['message'] = JText::_('COM_RSCOMMENTS_INVALID_CAPTCHA',true);
				}
			} else {
				try {
					$response = $input->get('g-recaptcha-response', '', 'raw');
					$ip		  = RSCommentsHelper::getIp(true);
					$secretKey= $config->recaptcha_new_secret_key;
					
					jimport('joomla.http.factory');
					$http = JHttpFactory::getHttp();
					if ($request = $http->get('https://www.google.com/recaptcha/api/siteverify?secret='.urlencode($secretKey).'&response='.urlencode($response).'&remoteip='.urlencode($ip))) {
						$json = json_decode($request->body);
					}
				} catch (Exception $e) {
					$data['success'] = false;
					$data['message'] = $e->getMessage();
				}
				
				if (!$json->success) {
					$data['success'] = false;
					$data['message'] = JText::_('COM_RSCOMMENTS_INVALID_CAPTCHA',true);
				}
			}
		}
		
		if ($data['success']) {
			$query->insert($db->qn('#__rscomments_reports'))
				->set($db->qn('report').' = '.$db->q($report))
				->set($db->qn('IdComment').' = '.$db->q($id))
				->set($db->qn('uid').' = '.$db->q($uid))
				->set($db->qn('ip').' = '.$db->q($ip))
				->set($db->qn('date').' = '.$db->q(JFactory::getDate()->toSql()));
			$db->setQuery($query);
			$db->execute();
			
			$data['message'] = JText::_('COM_RSCOMMENTS_REPORT_SAVED',true);
			
			// Send emails
			if ($config->enable_email_reports) {
				jimport('joomla.mail.helper');
				$cfg = new JConfig;
				
				if ($emails = $config->report_emails) {
					if ($emails = explode(',',$emails)) {
						$message = RSCommentsHelper::getMessage('report_message');
						$user 	 = JFactory::getUser();
						$name	 = $user->get('id') ? $user->get('name') : JText::_('COM_RSCOMMENTS_GUEST');
						
						$query->clear()
							->select($db->qn('uid'))->select($db->qn('email'))
							->select($db->qn('date'))->select($db->qn('url'))
							->from($db->qn('#__rscomments_comments'))
							->where($db->qn('IdComment').' = '.(int) $id);
						$db->setQuery($query);
						$comment = $db->loadObject();
						
						$author = $comment->uid ? JFactory::getUser($comment->uid)->get('name') : JText::_('COM_RSCOMMENTS_GUEST').' ('.$comment->email.')';
						$preview = JURI::root().base64_decode($comment->url).'#rscomment'.$id;
						
						$replace = array('{user}','{author}','{date}','{preview}','{report}');
						$with	 = array($name, $author, RSCommentsHelper::showDate($comment->date), $preview, $report);
						$message = str_replace($replace, $with, $message);
						
						foreach ($emails as $email) {
							$email = trim($email);
							if (!JMailHelper::isEmailAddress($email)) {
								continue;
							}
							
							$mailer	= JFactory::getMailer();
							$mailer->sendMail($cfg->mailfrom , $cfg->fromname , $email , JText::_('COM_RSCOMMENTS_REPORT_EMAIL_SUBJECT') , $message , 1);
						}
					}
				}
			}
		}
		
		return $data;
	}
}