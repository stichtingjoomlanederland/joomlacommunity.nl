<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPRSEventspro extends JPlugin {

	// Main constructor
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
		$this->newComponents = array(30,31,32,33,34);
	}
	
	// Add the RSEvents!Pro Components
	public function rsfp_bk_onAfterShowComponents() {
		if (!self::canRun()) return;
		
		$html = '';
		$html .= '<li class="rsform_navtitle">'.JText::_('RSFP_RSEPRO_LABEL').'</li>';
		$html .= '<li><a href="javascript: void(0);" onclick="displayTemplate(30);return false;" id="rsfpc30"><span class="rsficon rsficon-progress-full"></span><span class="inner-text">'.JText::_('RSFP_RSEPRO_NAME').'</span></a></li>';
		$html .= '<li><a href="javascript: void(0);" onclick="displayTemplate(31);return false;" id="rsfpc31"><span class="rsficon rsficon-envelope-square"></span><span class="inner-text">'.JText::_('RSFP_RSEPRO_EMAIL').'</span></a></li>';
		$html .= '<li><a href="javascript: void(0);" onclick="displayTemplate(32);return false;" id="rsfpc32"><span class="rsficon rsficon-ticket"></span><span class="inner-text">'.JText::_('RSFP_RSEPRO_TICKETS').'</span></a></li>';
		$html .= '<li><a href="javascript: void(0);" onclick="displayTemplate(33);return false;" id="rsfpc33"><span class="rsficon rsficon-caret-square-o-down"></span><span class="inner-text">'.JText::_('RSFP_RSEPRO_PAYMENTS').'</span></a></li>';
		$html .= '<li><a href="javascript: void(0);" onclick="displayTemplate(34);return false;" id="rsfpc34"><span class="rsficon rsficon-crop_7_5"></span><span class="inner-text">'.JText::_('RSFP_RSEPRO_COUPON').'</span></a></li>';
		
		echo $html;
	}
	
	// Form Validation
	public function rsfp_f_onBeforeFormValidation($args) {
		if (!self::canRun()) return;
		
		$jinput	= JFactory::getApplication()->input;
		$form   = $jinput->get('form',array(),'array');
		$formId = (int) $form['formId'];
		
		$exists = RSFormProHelper::componentExists($formId, $this->newComponents);
		if (!empty($exists)) {
			$db 	= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$cid 	= $jinput->getInt('cid',0);
			
			if ($this->_getHasForm($cid, $formId)) {
				$query->clear()
					->select('COUNT('.$db->qn('id').')')
					->from($db->qn('#__rseventspro_users'))
					->where($db->qn('ide').' = '.$cid)
					->where($db->qn('email').' = '.$db->q($form['RSEProEmail']));
				
				$db->setQuery($query);
				$registered = (int) $db->loadResult();
				
				$multiplereg = rseventsproHelper::getConfig('multi_registration');
				if ($registered && $multiplereg == 0) {
					JError::raiseWarning(500, JText::_('RSEPRO_REGISTRATION_ERROR5'));
					$args['invalid'][] = $this->_getComponentId('RSEProEmail', $formId);
				}
				
				$query->clear()
					->select('COUNT('.$db->qn('id').')')
					->from($db->qn('#__rseventspro_tickets'))
					->where($db->qn('ide').' = '.$db->q($cid));
				$db->setQuery($query);
				if ($eventtickets = (int) $db->loadResult()) {
					$query->clear()
						->select($db->qn('ticketsconfig'))
						->from($db->qn('#__rseventspro_events'))
						->where($db->qn('id').' = '.$cid);
					$db->setQuery($query);
					$ticketsconfig = $db->loadResult();
					
					if ($ticketsconfig) {
						$tickets 	= $jinput->get('tickets', array(),'array');
						$unlimited	= $jinput->get('unlimited', array(),'array');
						$thetickets = array_merge($tickets,$unlimited);
						
						if (empty($thetickets)) {
							$args['invalid'][] = $this->_getComponentId('RSEProTickets', $formId);
						}
					} else {
						if (rseventsproHelper::getConfig('multi_tickets')) {
							$tickets = $jinput->get('tickets', array(),'array');
							
							if (empty($tickets)) {
								$args['invalid'][] = $this->_getComponentId('RSEProTickets', $formId);
							}
						}
					}
				}
			}
		}
	}
	
	// AJAX Form Validation
	public function rsfp_f_onAJAXScriptCreate($args) {
		if (!self::canRun()) return;
		
		$script =& $args['script'];
		$formId = $args['formId'];
		
		if ($componentId = RSFormProHelper::componentExists($formId, $this->newComponents)) {
			$args['script'] .= 'ajaxValidationRSEventsPro(task, formId, data);';
		}
	}
	
	// After store submissions
	public function rsfp_f_onAfterStoreSubmissions($args) {
		if (!self::canRun()) return;
		
		$exists = RSFormProHelper::componentExists($args['formId'], $this->newComponents);
		if (!empty($exists)) {
			$jinput	= JFactory::getApplication()->input;
			$cid 	= $jinput->getInt('cid');
			
			if ($cid && $jinput->get('option') == 'com_rseventspro' && $this->_getHasForm($cid, $args['formId'])) {
				$this->result = rseventsproHelper::saveRegistration($args['SubmissionId']);
				$this->updateRSForm($args['SubmissionId'], $args['formId']);
			}
		}
	}
	
	// After the form has been processed
	public function rsfp_f_onAfterFormProcess($args) {
		if (!self::canRun()) return;
		
		$exists = RSFormProHelper::componentExists($args['formId'], $this->newComponents);
		if (!empty($exists)) {
			$db 	= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$jinput	= JFactory::getApplication()->input;
			$cid 	= $jinput->getInt('cid',0);
			
			if ($cid && $jinput->get('option') == 'com_rseventspro' && $this->_getHasForm($cid, $args['formId'])) {
				$form = $jinput->get('form',array(),'array');
				if ($form['RSEProName']) {
					$hasPrice = 0;
					
					$db->setQuery('SELECT '.$db->qn('ticketsconfig').' FROM '.$db->qn('#__rseventspro_events').' WHERE '.$db->qn('id').' = '.$cid.' ');
					$ticketsconfig = $db->loadResult();
					
					if ($ticketsconfig) {
						$tickets	= array();
						$thetickets	= $jinput->get('tickets',array(),'array');
						$unlimited	= $jinput->get('unlimited',array(),'array');
						
						foreach ($thetickets as $tid => $theticket) {
							$tickets[$tid] = count($theticket);
						}
						
						if (!empty($unlimited)) {
							JArrayHelper::toInteger($unlimited);
							foreach ($unlimited as $unlimitedid => $quantity)
								$tickets[$unlimitedid] = $quantity;
						}
						
						if (!empty($tickets)) {
							foreach ($tickets as $ticket => $quantity) {
								$query->clear()
									->select($db->qn('price'))
									->from($db->qn('#__rseventspro_tickets'))
									->where($db->qn('id').' = '.(int) $ticket);
								
								$db->setQuery("SELECT price FROM #__rseventspro_tickets WHERE id = ".(int) $ticket." ");
								if ($db->loadResult() > 0) $hasPrice = 1;
							}
						}
					} else {
						$tickets = $jinput->get('tickets',array(),'array');
						
						if (empty($tickets)) {
							$query->clear()
								->select($db->qn('price'))
								->from($db->qn('#__rseventspro_tickets'))
								->where($db->qn('id').' = '.(int) $form['RSEProTickets']);
							
							$db->setQuery($query);
							if ($db->loadResult() > 0) $hasPrice = 1;
						} else {
							foreach ($tickets as $ticket => $quantity) {
								$query->clear()
									->select($db->qn('price'))
									->from($db->qn('#__rseventspro_tickets'))
									->where($db->qn('id').' = '.(int) $ticket);
								
								$db->setQuery("SELECT price FROM #__rseventspro_tickets WHERE id = ".(int) $ticket." ");
								if ($db->loadResult() > 0) $hasPrice = 1;
							}
						}
					}
					
					if ($hasPrice) {
						if ($this->result) {
							echo rseventsproHelper::redirect(true,$this->result['message'],$this->result['url'],true);
							exit();
						}
					}
				}
			}
		}
	}
	
	// Before the creation of the component body
	public function rsfp_bk_onBeforeCreateFrontComponentBody($args) {
		if (!self::canRun()) return;
		
		$config	= rseventsproHelper::getConfig();
		$jinput = JFactory::getApplication()->input;
		
		if ($jinput->getCmd('option') == 'com_rseventspro' && $jinput->getCmd('layout') == 'subscribe') {
			if (!empty($args['data']['DEFAULTVALUE'])) {
				$defaulttext = $args['data']['DEFAULTVALUE'];
				$defaulttext = $this->placeholders($defaulttext,$jinput->getInt('cid'),'');
				$args['data']['DEFAULTVALUE'] = $defaulttext;
			}
			
			if (!empty($args['data']['TEXT'])) {
				$text = $args['data']['TEXT'];
				$text = $this->placeholders($text,$jinput->getInt('cid'),'');
				$args['data']['TEXT'] = $text;
			}
			
			if ($args['data']['NAME'] == 'RSEProPayment') {
				if ($config->payment_type == 0) {
					$args['data']['ComponentTypeName'] = 'radioGroup';
				}
				
				if (empty($args['data']['ADDITIONALATTRIBUTES'])) {
					$args['data']['ADDITIONALATTRIBUTES'] = 'onchange="rse_calculatetotal();"';
				} else {
					$pattern = '#onchange=\"(.*?)\"#is';
					preg_match($pattern,$args['data']['ADDITIONALATTRIBUTES'], $match);
					if ($match && !empty($match[1])) {
						$args['data']['ADDITIONALATTRIBUTES'] = str_replace($match[1], 'rse_calculatetotal();'.$match[1],$args['data']['ADDITIONALATTRIBUTES']);
					}
				}
			}
			
			if ($args['data']['NAME'] == 'RSEProCoupon') {
				if (empty($args['data']['ADDITIONALATTRIBUTES'])) {
					$args['data']['ADDITIONALATTRIBUTES'] = 'onkeyup="rse_calculatetotal();"';
				} else {
					$pattern = '#onkeyup=\"(.*?)\"#is';
					preg_match($pattern,$args['data']['ADDITIONALATTRIBUTES'], $match);
					if ($match && !empty($match[1])) {
						$args['data']['ADDITIONALATTRIBUTES'] = str_replace($match[1], 'rse_calculatetotal();'.$match[1],$args['data']['ADDITIONALATTRIBUTES']);
					}
				}
			}
		}
	}
	
	// After the creation of the component body
	public function rsfp_bk_onAfterCreateFrontComponentBody($args) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$input	= JFactory::getApplication()->input;
		$id		= $input->getInt('id',0);
		$config	= rseventsproHelper::getConfig();
		
		$db->setQuery('SELECT '.$db->qn('id').', '.$db->qn('name').', '.$db->qn('ticketsconfig').' FROM '.$db->qn('#__rseventspro_events').' WHERE '.$db->qn('id').' = '.$id.' ');
		$event = $db->loadObject();
		
		if (empty($event)) {
			return;
		}
		
		if ($args['data']['NAME'] == 'RSEProTickets') {
			$html = '';
			$layoutName = $this->getFormLayout($args['formId']);
			
			if ($event->ticketsconfig) {
				$html .= '<a onclick="RSopenModal();" href="javascript:void(0)"><i class="icon-cart"></i> <span id="rsepro_cart">'.JText::_('COM_RSEVENTSPRO_SELECT_TICKETS').'</span></a>';
				
				$html .= '<br /> <br /> <span id="rsepro_selected_tickets_view"></span><span id="rsepro_selected_tickets"></span>';
				$html .= '<br /> <span id="paymentinfocontainer" style="display:none;"><span id="paymentinfo"></span></span>';
				$html .= '<span class="rs_clear"></span> <br /> <span id="grandtotalcontainer" style="display:none;">'.JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL').': <span id="grandtotal"></span></span>';
				$html .= '<input type="hidden" name="from" id="from" value="" />';
				$html .= '<input type="hidden" name="form['.$args['data']['NAME'].']" id="'.$args['data']['NAME'].'" value="1" />';
				$html .= '<br />';
			} else {
				$html .= '<input type="text" id="numberinp" name="numberinp" value="1" size="3" style="display: none;" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, \'\');rse_calculatetotal();" />';
				$html .= '<select '.($layoutName == 'bootstrap3' ? 'class="form-control"' : '').'name="number" id="number" onchange="rse_calculatetotal();"><option value="1">1</option></select> ';
				
				if ($layoutName == 'bootstrap3') {
					RSFormProHelper::addClass($args['data']['ADDITIONALATTRIBUTES'], ' form-control');
				}
				
				$html .= '<select name="form['.$args['data']['NAME'].']" id="'.$args['data']['NAME'].'" '.$args['data']['ADDITIONALATTRIBUTES'].' >';
				$items = RSFormProHelper::explode(RSFormProHelper::isCode($args['data']['ITEMS']));
				$special = array('[c]', '[g]', '[d]');
				foreach ($items as $item) {
					@list($val, $txt) = @explode('|', str_replace($special, '', $item), 2);
					if (is_null($txt))
						$txt = $val;
						
					// <optgroup>
					if (strpos($item, '[g]') !== false) {
						$out .= '<optgroup label="'.RSFormProHelper::htmlEscape($val).'">';
						continue;
					}
					// </optgroup>
					if(strpos($item, '[/g]') !== false) {
						$out .= '</optgroup>';
						continue;
					}
					
					$additional = '';
					// selected
					if ((strpos($item, '[c]') !== false && empty($args['value'])) || (isset($args['value'][$args['data']['NAME']]) && $val == $args['value'][$args['data']['NAME']]))
						$additional .= 'selected="selected"';
					// disabled
					if (strpos($item, '[d]') !== false)
						$additional .= 'disabled="disabled"';
					
					$html .= '<option '.$additional.' value="'.RSFormProHelper::htmlEscape($val).'">'.RSFormProHelper::htmlEscape($txt).'</option>';
				}
				$html .= '</select>';
				
				if (rseventsproHelper::getConfig('multi_tickets','int')) {
					$html .= ' <a href="javascript:void(0);" onclick="rs_add_ticket();">'.JText::_('RSEPRO_SUBSCRIBER_ADD_TICKET').'</a> ';
				}
				
				$html .= ' <img id="rs_loader" src="'.JURI::root().'components/com_rseventspro/assets/images/loader.gif" alt="" style="vertical-align: middle; display: none;" />';
				
				if (rseventsproHelper::getConfig('multi_tickets','int')) {
					$html .= '<br /> <br /> <span id="tickets"></span>';
					$html .= '<span id="hiddentickets"></span>';
				} else {
					$html .= '<br />';
				}
				
				$html .= '<br /> <span id="paymentinfocontainer" style="display:none;"><span id="paymentinfo"></span></span>';
				$html .= '<span class="rs_clear"></span> <br /> <span id="grandtotalcontainer" style="display:none;">'.JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL').': <span id="grandtotal"></span></span>';
				
				$html .= ' <br /> <span id="tdescription"></span>';
				$html .= '<input type="hidden" name="from" id="from" value="" />';
			}
			
			$args['out'] = $html;
		}
		
		if ($args['data']['NAME'] == 'RSEProCoupon') {
			
			$args['out'] .= ' <a href="javascript:void(0)" onclick="rse_verify_coupon('.$id.',document.getElementById(\'RSEProCoupon\').value)">';
			$args['out'] .= '<img src="'.JURI::root().'components/com_rseventspro/assets/images/coupon.png" alt="'.JText::_('COM_RSEVENTSPRO_COUPON_VERIFY').'" style="vertical-align:middle" />';
			$args['out'] .= '</a>';
			
			if ($event->ticketsconfig) {
				$args['out'] = str_replace('rse_calculatetotal();','rsepro_update_total();',$args['out']);
			}
		}
		
		if ($args['data']['NAME'] == 'RSEProPayment') {
			if ($config->payment_type == 0) {
				$out = '';
				$i = 0;
				$items = RSFormProHelper::explode(RSFormProHelper::isCode($args['data']['ITEMS']));
				$layoutName = $this->getFormLayout($args['formId']);
				$special = array('[c]', '[d]');
				
				foreach ($items as $item)
				{
					@list($val, $txt) = @explode('|', str_replace($special, '', $item), 2);
					if (is_null($txt))
						$txt = $val;
						
					$additional = '';
					// checked
					if ((strpos($item, '[c]') !== false && empty($value)) || (isset($value[$args['data']['NAME']]) && $val == $value[$args['data']['NAME']]))
						$additional .= 'checked="checked"';
					// disabled
					if (strpos($item, '[d]') !== false)
						$additional .= 'disabled="disabled"';
						
					switch($layoutName) {
						case 'bootstrap2':
							$out .= '<label for="'.$args['data']['NAME'].$i.'" class="radio'.($data['FLOW'] != 'VERTICAL' ? ' inline' : '').'"><input '.$additional.' name="form['.$args['data']['NAME'].']" type="radio" value="'.RSFormProHelper::htmlEscape($val).'" id="'.$args['data']['NAME'].$i.'" '.$args['data']['ADDITIONALATTRIBUTES'].' />'.$txt.'</label>';
						break;
						case 'bootstrap3':
							$out .= '<label for="'.$args['data']['NAME'].$i.'" class="radio'.($data['FLOW'] != 'VERTICAL' ? '-inline' : '').'"><input '.$additional.' name="form['.$args['data']['NAME'].']" type="radio" value="'.RSFormProHelper::htmlEscape($val).'" id="'.$args['data']['NAME'].$i.'" '.$args['data']['ADDITIONALATTRIBUTES'].' />'.$txt.'</label>';
						break;
						case 'uikit':
							$out .= '<label for="'.$args['data']['NAME'].$i.'"><input '.$additional.' name="form['.$args['data']['NAME'].']" type="radio" value="'.RSFormProHelper::htmlEscape($val).'" id="'.$args['data']['NAME'].$i.'" '.$args['data']['ADDITIONALATTRIBUTES'].' />'.$txt.'</label>';
							if ($data['FLOW'] == 'VERTICAL')
							{
								$out .= '<br />';
							}
						break;
						
						default:
							if ($args['data']['FLOW']=='VERTICAL' && $layoutName == 'responsive') {
								$out .= '<p class="rsformVerticalClear">';
							}
							$out .= '<input '.$additional.' name="form['.$args['data']['NAME'].']" type="radio" value="'.RSFormProHelper::htmlEscape($val).'" id="'.$args['data']['NAME'].$i.'" '.$args['data']['ADDITIONALATTRIBUTES'].' /><label for="'.$args['data']['NAME'].$i.'">'.$txt.'</label>';
							
							if ($args['data']['FLOW']=='VERTICAL')
							{
								if ($layoutName == 'responsive')
									$out .= '</p>';
								else
									$out .= '<br />';
							}
						break;
					}	
					$i++;
				}
				
				$args['out'] = $out;	
			}
		}
		
		if ($args['data']['NAME'] == 'RSEProPayment') {
			if ($event->ticketsconfig) {
				$args['out'] = str_replace('rse_calculatetotal();','rsepro_update_total();',$args['out']);
			}
		}
	}
	
	// After creation of preview
	public function rsfp_bk_onAfterCreateComponentPreview($args) {
		if (!self::canRun()) return;
		
		if ($args['data']['NAME'] == 'RSEProPayment' || $args['data']['NAME'] == 'RSEProTickets') {
			$args['out'] ='<td>'.$args['data']['CAPTION'].'</td><td></td>';
		}
	}
	
	// On form display
	public function rsfp_f_onInitFormDisplay($args) {
		if (!self::canRun()) return;
		
		$jinput = JFactory::getApplication()->input;
		if ($jinput->getCmd('option') == 'com_rseventspro' || $jinput->getCmd('layout') == 'subscribe') {		
			$text = $args['formLayout'];
			$text = $this->placeholders($text,$jinput->getInt('cid'),'');
			$args['formLayout'] = $text;
		}
	}
	
	// After Thankyou message
	public function rsfp_f_onAfterShowThankyouMessage($args) {
		if (!self::canRun()) return;
		
		$jinput = JFactory::getApplication()->input;
		if ($jinput->getCmd('option') == 'com_rseventspro' && $jinput->getCmd('layout') == 'subscribe') {
			$db  	= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$cid 	= $jinput->getInt('cid');
			
			$query->clear()
				->select($db->qn('name'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.$cid);
			
			$db->setQuery($query);
			$name = $db->loadResult();
			
			$text = $args['output'];
			$text = $this->placeholders($text,$cid,'');
			
			if (rseventsproHelper::getConfig('modal') == 0) {
				$replace = '<a class="btn button" href="'.rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($cid,$name)).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_BACK').'</a>';
				$pattern	= '#<input type="button" class="rsform-submit-button btn btn-primary" name="continue"(.*?)/>#is';
				preg_match($pattern,$text,$match);
				
				if (!empty($match) && isset($match[1])) {
					if (strpos($match[1],'document.location.reload') !== false) {
						$text = preg_replace($pattern,$replace,$text);
					}
				}
			}
			
			$args['output'] = $text;
		}
	}
	
	// Before the user email sending procedure
	public function rsfp_beforeUserEmail($args) {
		$jinput = JFactory::getApplication()->input;
		
		if ($jinput->getCmd('option') == 'com_rseventspro' || $jinput->getCmd('layout') == 'subscribe') {
			$subjecttext	= $this->placeholders(array('subject' => $args['userEmail']['subject'], 'body' => $args['userEmail']['text']),$jinput->getInt('cid'),'',$args['submissionId']);
			$toreplyto		= $this->placeholders(array('subject' => $args['userEmail']['to'], 'body' => $args['userEmail']['replyto']),$jinput->getInt('cid'),'',$args['submissionId']);
			$ccbcc			= $this->placeholders(array('subject' => $args['userEmail']['cc'], 'body' => $args['userEmail']['bcc']),$jinput->getInt('cid'),'',$args['submissionId']);
			$fromfromName	= $this->placeholders(array('subject' => $args['userEmail']['from'], 'body' => $args['userEmail']['fromName']),$jinput->getInt('cid'),'',$args['submissionId']);
			
			$args['userEmail']['text'] = $subjecttext['body'];
			$args['userEmail']['subject'] = $subjecttext['subject'];
			
			$args['userEmail']['to'] = $toreplyto['subject'];
			$args['userEmail']['replyto'] = $toreplyto['body'];
			
			$args['userEmail']['cc'] = $ccbcc['subject'];
			$args['userEmail']['bcc'] = $ccbcc['body'];
			
			$args['userEmail']['from'] = $fromfromName['subject'];
			$args['userEmail']['fromName'] = $fromfromName['body'];
		}
	}
	
	// Before the admin email sending procedure
	public function rsfp_beforeAdminEmail($args) {
		$jinput = JFactory::getApplication()->input;
		
		if ($jinput->getCmd('option') == 'com_rseventspro' || $jinput->getCmd('layout') == 'subscribe') {
			$subjecttext	= $this->placeholders(array('subject' => $args['adminEmail']['subject'], 'body' => $args['adminEmail']['text']),$jinput->getInt('cid'),'',$args['submissionId']);
			$toreplyto		= $this->placeholders(array('subject' => $args['adminEmail']['to'], 'body' => $args['adminEmail']['replyto']),$jinput->getInt('cid'),'',$args['submissionId']);
			$ccbcc			= $this->placeholders(array('subject' => $args['adminEmail']['cc'], 'body' => $args['adminEmail']['bcc']),$jinput->getInt('cid'),'',$args['submissionId']);
			$fromfromName	= $this->placeholders(array('subject' => $args['adminEmail']['from'], 'body' => $args['adminEmail']['fromName']),$jinput->getInt('cid'),'',$args['submissionId']);
			
			$args['adminEmail']['text'] = $subjecttext['body'];
			$args['adminEmail']['subject'] = $subjecttext['subject'];
			
			$args['adminEmail']['to'] = $toreplyto['subject'];
			$args['adminEmail']['replyto'] = $toreplyto['body'];
			
			$args['adminEmail']['cc'] = $ccbcc['subject'];
			$args['adminEmail']['bcc'] = $ccbcc['body'];
			
			$args['adminEmail']['from'] = $fromfromName['subject'];
			$args['adminEmail']['fromName'] = $fromfromName['body'];
		}
	}
	
	// Before the additional email sending procedure
	public function rsfp_beforeAdditionalEmail($args) {
		$jinput = JFactory::getApplication()->input;
		if ($jinput->getCmd('option') == 'com_rseventspro' || $jinput->getCmd('layout') == 'subscribe') {
			$subjecttext	= $this->placeholders(array('subject' => $args['additionalEmail']['subject'], 'body' => $args['additionalEmail']['text']),$jinput->getInt('cid'),'',$args['submissionId']);
			$toreplyto		= $this->placeholders(array('subject' => $args['additionalEmail']['to'], 'body' => $args['additionalEmail']['replyto']),$jinput->getInt('cid'),'',$args['submissionId']);
			$ccbcc			= $this->placeholders(array('subject' => $args['additionalEmail']['cc'], 'body' => $args['additionalEmail']['bcc']),$jinput->getInt('cid'),'',$args['submissionId']);
			$fromfromName	= $this->placeholders(array('subject' => $args['additionalEmail']['from'], 'body' => $args['additionalEmail']['fromName']),$jinput->getInt('cid'),'',$args['submissionId']);
			
			$args['additionalEmail']['text'] = $subjecttext['body'];
			$args['additionalEmail']['subject'] = $subjecttext['subject'];
			
			$args['additionalEmail']['to'] = $toreplyto['subject'];
			$args['additionalEmail']['replyto'] = $toreplyto['body'];
			
			$args['additionalEmail']['cc'] = $ccbcc['subject'];
			$args['additionalEmail']['bcc'] = $ccbcc['body'];
			
			$args['additionalEmail']['from'] = $fromfromName['subject'];
			$args['additionalEmail']['fromName'] = $fromfromName['body'];
		}
	}
	
	// Get a list of payment methods
	public static function getPayments() {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$jinput	= JFactory::getApplication()->input;
		
		if ($jinput->get('option') != 'com_rseventspro') {
			return;
		}
		
		if ($jinput->get('layout') != 'subscribe') {
			return;
		}
		
		$cid		= $jinput->getInt('cid');
		$payments	= array();
		
		$query->clear()
			->select($db->qn('payments'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$cid);
		
		$db->setQuery($query);
		$eventPayments	= $db->loadResult();
		$payment_items	= rseventsproHelper::getPayments(false,$eventPayments);
		$default_payment= rseventsproHelper::getConfig('default_payment');
		
		if (!empty($payment_items)) {
			foreach ($payment_items as $payment) {
				$default = $default_payment == $payment->value ? '[c]' : '';
				$payments[] = $payment->value.'|'.$payment->text.$default;
			}
		}
		
		if (!empty($payments))
			return implode("\n",$payments);
		
		return '';
	}
	
	// Get a list of available tickets
	public function getTickets() {
		if (!self::canRun()) return;
		
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$jinput		= JFactory::getApplication()->input;
		$user		= JFactory::getUser();
		$usergroups	= rseventsproHelper::getUserGroups();
		$joomgroups	= JAccess::getGroupsByUser($user->get('id'));
		
		if ($jinput->get('option') != 'com_rseventspro') {
			return;
		}
		
		if ($jinput->get('layout') != 'subscribe') {
			return;
		}
		
		$cid	 = $jinput->getInt('cid');
		$tickets = rseventsproHelper::getTickets($cid, true);
		$return  = array();
		
		if (!empty($tickets)) {
			foreach ($tickets as $ticket) {
				$checkticket = rseventsproHelper::checkticket($ticket->id);
				if ($checkticket == -1) continue;
				
				$price = $ticket->price > 0 ? ' - '.rseventsproHelper::currency($ticket->price) : ' - '.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE');
				$return[] = $ticket->id.'|'.$ticket->name.$price;
			}
		}
		
		if (!empty($return))
			return implode("\n",$return);
		
		return;
	}
	
	// Get the name of the user
	public static function getName() {
		if (!self::canRun()) return;
		
		$uid = JFactory::getUser()->get('id');
		return rseventsproHelper::getUser($uid);
	}
	
	// Add the RSEvents!Pro Emails Tab
	public function rsfp_bk_onAfterShowFormEditTabsTab() {
		if (!self::canRun()) return;
		
		echo '<li><a href="javascript: void(0);" id="rseproemails"><span class="rsficon rsficon-envelope-square"></span><span class="inner-text">'.JText::_('RSFP_RSEPRO_EMAILS').'</span></a></li>';
	}
	
	// Add the RSEvents!Pro Emails Tab Content
	public function rsfp_bk_onAfterShowFormEditTabs() {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$formId	= JFactory::getApplication()->input->getInt('formId',0);
		$table	= JTable::getInstance('RSForm_Rseventspro', 'Table');
		if (!$table) return;
		$table->load($formId);
		
		$query->select($db->qn('Lang'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId').' = '.(int) $formId);
		$db->setQuery($query);
		$formLang = $db->loadResult();
		
		$lang = $this->getLang();
		if ($lang != $formLang) {
			$translations = $this->getTranslations($formId, $lang);
			if ($translations)
				foreach ($translations as $field => $value) {
					if (isset($table->$field))
						$table->$field = $value;
				}
		}
		
		$lists['published']	= RSFormProHelper::renderHTML('select.booleanlist','rsepro[published]','class="inputbox"',$table->published);
		$editor				= JFactory::getConfig()->get('editor');
		$editor				= JEditor::getInstance($editor);
		$emails				= array();
		
		// Registration email
		$emails['registration'][] = array('label' => JText::_('RSFP_RSEPRO_REGISTRATION'), 'input' => RSFormProHelper::renderHTML('select.booleanlist','rsepro[registration]','class="inputbox"',$table->registration));
		$emails['registration'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_REGISTRATION_SUBJECT'), 'input' => '<input type="text" name="rsepro[registration_subject]" value="'.htmlentities($table->registration_subject).'" class="rs_inp rs_80" size="60" />');
		$emails['registration'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_REGISTRATION_MESSAGE'), 'input' => $editor->display('rsepro[registration_text]', htmlspecialchars($table->registration_text, ENT_COMPAT, 'UTF-8'), '50%', '50%', 90, 15, false, 'registration_text'));
		
		// Activation email
		$emails['activation'][] = array('label' => JText::_('RSFP_RSEPRO_ACTIVATION'), 'input' => RSFormProHelper::renderHTML('select.booleanlist','rsepro[activation]','class="inputbox"',$table->activation));
		$emails['activation'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_ACTIVATION_SUBJECT'), 'input' => '<input type="text" name="rsepro[activation_subject]" value="'.htmlentities($table->activation_subject).'" class="rs_inp rs_80" size="60" />');
		$emails['activation'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_ACTIVATION_MESSAGE'), 'input' => $editor->display('rsepro[activation_text]', htmlspecialchars($table->activation_text, ENT_COMPAT, 'UTF-8'), '50%', '50%', 90, 15, false, 'activation_text'));
		
		// Unsubscribe email
		$emails['unsubscribe'][] = array('label' => JText::_('RSFP_RSEPRO_UNSUBSCRIBE'), 'input' => RSFormProHelper::renderHTML('select.booleanlist','rsepro[unsubscribe]','class="inputbox"',$table->unsubscribe));
		$emails['unsubscribe'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_UNSUBSCRIBE_SUBJECT'), 'input' => '<input type="text" name="rsepro[unsubscribe_subject]" value="'.htmlentities($table->unsubscribe_subject).'" class="rs_inp rs_80" size="60" />');
		$emails['unsubscribe'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_UNSUBSCRIBE_MESSAGE'), 'input' => $editor->display('rsepro[unsubscribe_text]', htmlspecialchars($table->unsubscribe_text, ENT_COMPAT, 'UTF-8'), '50%', '50%', 90, 15, false, 'unsubscribe_text'));
		
		// Denied subscription email
		$emails['denied'][] = array('label' => JText::_('RSFP_RSEPRO_DENIED'), 'input' => RSFormProHelper::renderHTML('select.booleanlist','rsepro[denied]','class="inputbox"',$table->denied));
		$emails['denied'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_DENIED_SUBJECT'), 'input' => '<input type="text" name="rsepro[denied_subject]" value="'.htmlentities($table->denied_subject).'" class="rs_inp rs_80" size="60" />');
		$emails['denied'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_DENIED_MESSAGE'), 'input' => $editor->display('rsepro[denied_text]', htmlspecialchars($table->denied_text, ENT_COMPAT, 'UTF-8'), '50%', '50%', 90, 15, false, 'denied_text'));
		
		// New event subscription notification email
		$emails['notify'][] = array('label' => JText::_('RSFP_RSEPRO_NOTIFY'), 'input' => RSFormProHelper::renderHTML('select.booleanlist','rsepro[notify]','class="inputbox"',$table->notify));
		$emails['notify'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_NOTIFY_SUBJECT'), 'input' => '<input type="text" name="rsepro[notify_subject]" value="'.htmlentities($table->notify_subject).'" class="rs_inp rs_80" size="60" />');
		$emails['notify'][] = array('label' => RSFormProHelper::translateIcon().' '.JText::_('RSFP_RSEPRO_NOTIFY_MESSAGE'), 'input' => $editor->display('rsepro[notify_text]', htmlspecialchars($table->notify_text, ENT_COMPAT, 'UTF-8'), '50%', '50%', 90, 15, false, 'notify_text'));
		
		if (rseventsproHelper::pdf()) {
			$emails['ticketpdf'][] = array('label' => JText::_('RSFP_RSEPRO_TICKET_PDF'), 'input' => RSFormProHelper::renderHTML('select.booleanlist','rsepro[ticketpdf]','class="inputbox"',$table->ticketpdf));
			$emails['ticketpdf'][] = array('label' => JText::_('RSFP_RSEPRO_TICKET_PDF_LAYOUT'), 'input' => $editor->display('rsepro[ticketpdf_layout]', htmlspecialchars($table->ticketpdf_layout, ENT_COMPAT, 'UTF-8'), '50%', '50%', 90, 15, false, 'ticketpdf_layout'));
		}
		
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/adapters/tabs.php';
		$tabs = new RSTabs('com-rsform-rseventspro');
		
		if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/placeholders.php')) {
			JFactory::getLanguage()->load('com_rseventspro');
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/placeholders.php';
		}
		
		echo '<div id="rseproemailsdiv">';
			include JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rseventspro.php';
		echo '</div>';
	}
	
	// Save form
	public function rsfp_onFormSave($form) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$data	= $input->get('rsepro',array(),'array');
		$table	= JTable::getInstance('RSForm_Rseventspro', 'Table');
		$formId	= $input->getInt('formId',0);
		
		$data['form_id'] = $formId;
		
		if (!$table) {
			return;
		}
		
		if (!$table->bind($data)) {
			JError::raiseWarning(500, $table->getError());
			return false;
		}
		
		$query->select($db->qn('form_id'))
			->from($db->qn('#__rsform_rseventspro'))
			->where($db->qn('form_id').' = '.(int) $data['form_id']);
		$db->setQuery($query);
		if (!$db->loadResult()) {
			$query->clear()
				->insert($db->qn('#__rsform_rseventspro'))
				->set($db->qn('form_id').' = '.(int) $data['form_id']);
			$db->setQuery($query);
			$db->execute();
		}
		
		$this->saveTranslation($table, $this->getLang());
		
		if ($table->store()) {
			return true;
		} else {
			JError::raiseWarning(500, $table->getError());
			return false;
		}
	}
	
	// RSEvents!Pro Registration Email
	public function rseproRegistrationEmail($vars) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $vars['ids'];
		
		if ($table = $this->getTable($id)) {
			if ($table->registration) {
				$subject	= $table->registration_subject;
				$message	= $table->registration_text;
				
				JFactory::getApplication()->input->set('cid', $table->ide);
				list($replace, $with) = RSFormProHelper::sendSubmissionEmails($table->SubmissionId, true);
				
				if (strpos($message, '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($message, $replace, $with);
				}
				
				$subject = str_replace($replace,$with,$subject);
				$message = str_replace($replace,$with,$message);
				
				$vars['data']['subject']	= $subject;
				$vars['data']['body']		= $message;
			}
		}
	}
	
	// RSEvents!Pro Activation Email
	public function rseproActivationEmail($vars) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $vars['ids'];
		
		if ($table = $this->getTable($id)) {
			if ($table->activation) {
				$subject	= $table->activation_subject;
				$message	= $table->activation_text;
				
				JFactory::getApplication()->input->set('cid', $table->ide);
				list($replace, $with) = RSFormProHelper::sendSubmissionEmails($table->SubmissionId, true);
				
				if (strpos($message, '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($message, $replace, $with);
				}
				
				$subject = str_replace($replace,$with,$subject);
				$message = str_replace($replace,$with,$message);
				
				$vars['data']['subject']	= $subject;
				$vars['data']['body']		= $message;
			}
		}
	}
	
	// RSEvents!Pro Unsubscribe Email
	public function rseproUnsubscribeEmail($vars) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $vars['ids'];
		
		if ($table = $this->getTable($id)) {
			if ($table->unsubscribe) {
				$subject	= $table->unsubscribe_subject;
				$message	= $table->unsubscribe_text;
				
				JFactory::getApplication()->input->set('cid', $table->ide);
				list($replace, $with) = RSFormProHelper::sendSubmissionEmails($table->SubmissionId, true);
				
				if (strpos($message, '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($message, $replace, $with);
				}
				
				$subject = str_replace($replace,$with,$subject);
				$message = str_replace($replace,$with,$message);
				
				$vars['data']['subject']	= $subject;
				$vars['data']['body']		= $message;
			}
		}
	}
	
	// RSEvents!Pro Denied Email
	public function rseproDeniedEmail($vars) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $vars['ids'];
		
		if ($table = $this->getTable($id)) {
			if ($table->denied) {
				$subject	= $table->denied_subject;
				$message	= $table->denied_text;
				
				JFactory::getApplication()->input->set('cid', $table->ide);
				list($replace, $with) = RSFormProHelper::sendSubmissionEmails($table->SubmissionId, true);
				
				if (strpos($message, '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($message, $replace, $with);
				}
				
				$subject = str_replace($replace,$with,$subject);
				$message = str_replace($replace,$with,$message);
				
				$vars['data']['subject']	= $subject;
				$vars['data']['body']		= $message;
			}
		}
	}
	
	// RSEvents!Pro Notify Email
	public function rseproNotifyEmail($vars) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $vars['ids'];
		
		if ($table = $this->getTable($id)) {
			if ($table->notify) {
				$subject	= $table->notify_subject;
				$message	= $table->notify_text;
				
				JFactory::getApplication()->input->set('cid', $table->ide);
				list($replace, $with) = RSFormProHelper::sendSubmissionEmails($table->SubmissionId, true);
				
				if (strpos($message, '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($message, $replace, $with);
				}
				
				$subject = str_replace($replace,$with,$subject);
				$message = str_replace($replace,$with,$message);
				
				$vars['data']['subject']	= $subject;
				$vars['data']['body']		= $message;
			}
		}
	}
	
	// RSEvents!Pro Ticket PDF
	public function rseproTicketPDFLayout($vars) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= $vars['ids'];
		
		if ($table = $this->getTable($id)) {
			if ($table->ticketpdf) {
				$layout	= $table->ticketpdf_layout;
				
				JFactory::getApplication()->input->set('cid', $table->ide);
				list($replace, $with) = RSFormProHelper::sendSubmissionEmails($table->SubmissionId, true);
				
				if (strpos($layout, '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($layout, $replace, $with);
				}
				
				$vars['layout']	= str_replace($replace,$with,$layout);
			}
		}
	}
	
	// Check if we can run this plugin
	protected static function canRun() {
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php') && file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php')) {
			JFactory::getLanguage()->load('plg_system_rsfprseventspro');
			
			if (!class_exists('RSFormProHelper')) {
				require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
			}
			
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
			return true;
		}
		
		return false;
	}
	
	// Check if the current event has a RSForm!Pro form attached 
	protected function _getHasForm($ide, $formId) {
		if (!self::canRun()) return;
		
		static $cache;
		if (!isset($cache[$formId])) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('form').' = '.(int) $formId)
				->where($db->qn('id').' = '.(int) $ide);
			
			$db->setQuery($query);
			$cache[$formId] = $db->loadResult();
		}
		
		return $cache[$formId];
	}
	
	// Get the component id
	protected function _getComponentId($name, $formId) {
		if (!self::canRun()) return;
		
		if (method_exists('RSFormProHelper', 'getComponentId'))
			return RSFormProHelper::getComponentId($name, $formId);
		
		static $cache;
		if (!is_array($cache))
			$cache = array();
			
		if (empty($formId)) {
			$formId = JFactory::getApplication()->input->getInt('formId');
			if (empty($formId)) {
				$post   = JFactory::getApplication()->input->get('form',array(),'array');
				$formId = (int) @$post['formId'];
			}
		}
		
		if (!isset($cache[$formId][$name]))
			$cache[$formId][$name] = RSFormProHelper::componentNameExists($name, $formId);
		
		return $cache[$formId][$name];
	}
	
	// Get the current form layout
	protected function getFormLayout($formId) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->select($db->qn('FormLayoutName'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId').' = '.(int) $formId)
			->where($db->qn('Published').' = 1');
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	// Replace RSEvents!Pro placeholders
	protected function placeholders($text,$ide,$name,$SubmissionId = null) {
		if (!self::canRun()) return;
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		
		if (!is_null($SubmissionId)) {
			$optionals	= $this->createOptionals($SubmissionId);
			$ids		= $this->createOptionals($SubmissionId,true);
		} else {
			$optionals	= array();
			$ids		= null;
		}
		
		return $ide ? rseventsproEmails::placeholders($text,$ide, $name, $optionals, $ids) : $text;
	}
	
	// Create a list of RSEvents!Pro optionals
	protected function createOptionals($id, $ids = false) {
		if (!self::canRun()) return;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$total	= 0;
		$info	= '';
		
		$optionals = array();
		
		// Get subscription details
		$query->clear()
			->select($db->qn('id'))->select($db->qn('ide'))->select($db->qn('name'))->select($db->qn('email'))
			->select($db->qn('discount'))->select($db->qn('early_fee'))->select($db->qn('late_fee'))
			->select($db->qn('tax'))->select($db->qn('gateway'))->select($db->qn('ip'))->select($db->qn('coupon'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('SubmissionId').' = '.(int) $id);
		
		$db->setQuery($query);
		$subscription = $db->loadObject();
		
		if ($ids)
			return $subscription->id;
		
		if ($subscription) {
			// Get tickets
			$tickets = rseventsproHelper::getUserTickets($subscription->id);
			
			if (!empty($tickets)) {
				foreach ($tickets as $ticket) {
					// Calculate the total
					if ($ticket->price > 0) {
						$price = $ticket->price * $ticket->quantity;
						$total += $price;
						$info .= $ticket->quantity . ' x ' .$ticket->name.' ('.rseventsproHelper::currency($ticket->price).') '.rseventsproHelper::getSeats($subscription->id,$ticket->id).' <br />';
					} else {
						$info .= $ticket->quantity . ' x ' .$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').') <br />';
					}
				}
			}
			
			if (!empty($subscription->discount) && !empty($total)) {
				$total = $total - $subscription->discount;
			}
			
			if (!empty($subscription->early_fee) && !empty($total)) {
				$total = $total - $subscription->early_fee;
			}
			
			if (!empty($subscription->late_fee) && !empty($total)) {
				$total = $total + $subscription->late_fee;
			}
			
			if (!empty($subscription->tax) && !empty($total)) {
				$total = $total + $subscription->tax;
			}
			
			$ticketstotal		= rseventsproHelper::currency($total);
			$ticketsdiscount	= !empty($subscription->discount) ? rseventsproHelper::currency($subscription->discount) : '';
			$subscriptionTax	= !empty($subscription->tax) ? rseventsproHelper::currency($subscription->tax) : '';
			$lateFee			= !empty($subscription->late_fee) ? rseventsproHelper::currency($subscription->late_fee) : '';
			$earlyDiscount		= !empty($subscription->early_fee) ? rseventsproHelper::currency($subscription->early_fee) : '';
			$gateway			= rseventsproHelper::getPayment($subscription->gateway);
			$IP					= $subscription->ip;
			$coupon				= !empty($subscription->coupon) ? $subscription->coupon : '';
			$optionals			= array($info, $ticketstotal, $ticketsdiscount, $subscriptionTax, $lateFee, $earlyDiscount, $gateway, $IP, $coupon);
		}
		
		return $optionals;
	}
	
	// Update the RSForm!Pro submission
	protected function updateRSForm($SubmissionId, $formId) {
		if (!self::canRun()) return;
		
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$jinput		= JFactory::getApplication()->input;
		$id			= $jinput->getInt('id',0);
		$from		= $jinput->getInt('from');
		$post		= $jinput->get('form',array(),'array');
		$total		= 0;
		$thestring	= '';
		
		$query->clear()
			->select($db->qn('ticketsconfig'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$id);
		$db->setQuery($query);
		$ticketsconfig = $db->loadResult();
		
		if ($ticketsconfig) {
			$tickets	= array();
			$thetickets	= $jinput->get('tickets',array(),'array');
			$unlimited	= $jinput->get('unlimited',array(),'array');
			
			foreach ($thetickets as $tid => $theticket) {
				$tickets[$tid] = count($theticket);
			}
			
			if (!empty($unlimited)) {
				JArrayHelper::toInteger($unlimited);
				foreach ($unlimited as $unlimitedid => $quantity)
					$tickets[$unlimitedid] = $quantity;
			}
		} else {
			if (rseventsproHelper::getConfig('multi_tickets','int')) {
				$tickets = $jinput->get('tickets',array(),'array');
				
				if (empty($tickets) && !empty($post['RSEProTickets']) && $jinput->get('option') == 'com_rseventspro') {
					if ($from == 1) {
						$tickets = array($post['RSEProTickets'] => $jinput->getInt('number'));
					} else  {
						$tickets = array($post['RSEProTickets'] => $jinput->getInt('numberinp'));
					}
				}
			} else {
				$ticket = $post['RSEProTickets'];
				
				if (!empty($ticket)) {
					if ($from == 1) {
						$tickets = array($ticket => $jinput->getInt('number'));
					} else {
						$tickets = array($ticket => $jinput->getInt('numberinp'));
					}
				}
			}
		}
		
		if (!empty($tickets)) {
			$tmp = array();
			foreach ($tickets as $ticket => $quantity) {
				$query->clear()
					->select($db->qn('name'))->select($db->qn('price'))
					->from($db->qn('#__rseventspro_tickets'))
					->where($db->qn('id').' = '.(int) $ticket);
				
				$db->setQuery($query);
				$ticketDetails = $db->loadObject();
				
				$ticketno = $quantity < 0 ? 1 : $quantity;
					
				if ($ticketDetails->price) {
					$tmp[] = $ticketno.' x '.$ticketDetails->name.' ('.rseventsproHelper::currency($ticketDetails->price).')';
					$total += $ticketno * $ticketDetails->price;
				} else {
					$tmp[] = $ticketno.' x '.$ticketDetails->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').')';
				}
			}
			
			$thestring .= !empty($tmp) ? implode(' , ',$tmp) : '';
		}
		
		$query->clear()
			->select($db->qn('id'))
			->select($db->qn('discount'))->select($db->qn('early_fee'))
			->select($db->qn('late_fee'))->select($db->qn('tax'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('SubmissionId').' = '.(int) $SubmissionId);
		
		$db->setQuery($query);
		$paymentInfo = $db->loadObject();
		
		if (!empty($paymentInfo->discount))
			$total = $total - $paymentInfo->discount;
			
		if (!empty($paymentInfo->early_fee))
			$total = $total - $paymentInfo->early_fee;
		
		if (!empty($paymentInfo->late_fee))
			$total = $total + $paymentInfo->late_fee;
		
		if (!empty($paymentInfo->tax))
			$total = $total + $paymentInfo->tax;
		
		if ($total)
			$thestring .= ' , '.JText::_('COM_RSEVENTSPRO_GLOBAL_TOTAL').': '.rseventsproHelper::currency($total);
		
		$payment = $post['RSEProPayment'];
		$payment = is_array($payment) ? $payment[0] : $payment;
		
		// Update tickets
		$query->clear()
			->update($db->qn('#__rsform_submission_values'))
			->set($db->qn('FieldValue').' = '.$db->q($thestring))
			->where($db->qn('FieldName').' = '.$db->q('RSEProTickets'))
			->where($db->qn('FormId').' = '.$db->q($formId))
			->where($db->qn('SubmissionId').' = '.$db->q($SubmissionId));
		
		$db->setQuery($query);
		$db->execute();
		
		// Update payment method
		$query->clear()
			->update($db->qn('#__rsform_submission_values'))
			->set($db->qn('FieldValue').' = '.$db->q(rseventsproHelper::getPayment($payment)))
			->where($db->qn('FieldName').' = '.$db->q('RSEProPayment'))
			->where($db->qn('FormId').' = '.$db->q($formId))
			->where($db->qn('SubmissionId').' = '.$db->q($SubmissionId));
		
		$db->setQuery($query);
		$db->execute();
	}
	
	// Get details for overriding the RSEvents!Pro emails
	protected function getTable($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		// First, let's assume that we have a SubmissionId
		$query->clear()
			->select($db->qn('rr').'.*')->select($db->qn('u.ide'))
			->select($db->qn('u.lang'))->select($db->qn('u.SubmissionId'))
			->from($db->qn('#__rsform_rseventspro','rr'))
			->join('LEFT',$db->qn('#__rsform_forms','f').' ON '.$db->qn('rr.form_id').' = '.$db->qn('f.FormId'))
			->join('LEFT',$db->qn('#__rsform_submissions','s').' ON '.$db->qn('f.FormId').' = '.$db->qn('s.FormId'))
			->join('LEFT',$db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.SubmissionId').' = '.$db->qn('s.SubmissionId'))
			->where($db->qn('u.id').' = '.(int) $id);
		$db->setQuery($query);
		$table = $db->loadObject();
		
		// If not, then check for a formId in the rseventspro events table
		if (!$table) {
			$query->clear()
				->select($db->qn('rr').'.*')->select($db->qn('u.ide'))
				->select($db->qn('u.lang'))->select($db->qn('u.SubmissionId'))
				->from($db->qn('#__rsform_rseventspro','rr'))
				->join('LEFT',$db->qn('#__rsform_forms','f').' ON '.$db->qn('rr.form_id').' = '.$db->qn('f.FormId'))
				->join('LEFT',$db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.form').' = '.$db->qn('f.FormId'))
				->join('LEFT',$db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.ide').' = '.$db->qn('e.id'))
				->where($db->qn('u.id').' = '.(int) $id);
			$db->setQuery($query);
			$table = $db->loadObject();
		}
		
		if ($table) {
			if ($table->published) {
				if ($translations = $this->getTranslations($table->form_id, $table->lang)) {
					foreach ($translations as $field => $value) {
						if (isset($table->$field)) {
							$table->$field = $value;
						}
					}
				}
				
				return $table;
			}
		}
		
		return false;
	}
	
	// Save RSEvents!Pro Email translations
	protected function saveTranslation(&$table, $lang) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select($db->qn('Lang'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId').' = '.(int) $table->form_id);
		$db->setQuery($query);
		$formLang = $db->loadResult();
		
		if ($formLang == $lang) return true;
		
		$fields 	  = array('registration_subject', 'registration_text', 'activation_subject', 'activation_text', 'unsubscribe_subject', 'unsubscribe_text', 'denied_subject', 'denied_text', 'notify_subject', 'notify_text');
		$translations = $this->getTranslations($table->form_id, $lang, 'id');
		
		foreach ($fields as $field) {
			$query   = array();
			$query[] = "`form_id`='".$table->form_id."'";
			$query[] = "`lang_code`='".$db->escape($lang)."'";
			$query[] = "`reference`='rseventspro'";
			$query[] = "`reference_id`='".$db->escape($field)."'";
			$query[] = "`value`='".$db->escape($table->$field)."'";
			
			if (!isset($translations[$field])) {
				$db->setQuery("INSERT INTO #__rsform_translations SET ".implode(", ", $query));
				$db->execute();
			} else {
				$db->setQuery("UPDATE #__rsform_translations SET ".implode(", ", $query)." WHERE id='".(int) $translations[$field]."'");
				$db->execute();
			}
			unset($table->$field);
		}
	}
	
	// Get default form language
	protected function getLang($formId = null) {
		$db		 = JFactory::getDbo();
		$query	 = $db->getQuery(true);
		$session = JFactory::getSession();
		$lang 	 = JFactory::getLanguage();
		$formId	 = is_null($formId) ? JFactory::getApplication()->input->getInt('formId',0) : $formId;
		
		$query->select($db->qn('Lang'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId').' = '.(int) $formId);
		$db->setQuery($query);
		$formLang = $db->loadResult();
		
		return $session->get('com_rsform.form.formId'.$formId.'.lang', !empty($formLang) ? $formLang : $lang->getDefault());
	}
	
	// Get translations
	protected function getTranslations($formId, $lang, $select = 'value') {
		$db = JFactory::getDbo();
		
		$db->setQuery("SELECT * FROM #__rsform_translations WHERE `form_id`='".(int) $formId."' AND `lang_code`='".$db->escape($lang)."' AND `reference`='rseventspro'");
		$results = $db->loadObjectList();
		
		$return = array();
		foreach ($results as $result)
			$return[$result->reference_id] = ($select == '*') ? $result : (isset($result->$select) ? $result->$select : false);
		
		return $return;
	}
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_rseventspro')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	// Allow this plugin to inject its own settings in the backup.
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_rseventspro'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($rseventspro = $db->loadObject()) {
			// No need for a form_id
			unset($rseventspro->form_id);
			
			$xml->add('rseventspro');
			foreach ($rseventspro as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/rseventspro');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->rseventspro)) {
			$data = array(
				'form_id' => $form->FormId
			);
			
			foreach ($xml->rseventspro->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			
			$row = JTable::getInstance('RSForm_Rseventspro', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_rseventspro')
						->set(array(
								$db->qn('form_id') .'='. $db->q($form->FormId),
						));
				$db->setQuery($query)->execute();
			}
			
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_rseventspro');
	}
}