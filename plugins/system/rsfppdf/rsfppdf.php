<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPPDF extends JPlugin
{	
	public function rsfp_onFormSave($form)
	{
		$post 			 = JFactory::getApplication()->input->get('pdf', array(), 'array');
		$post['form_id'] = JFactory::getApplication()->input->getInt('formId');
		
		if ($row = JTable::getInstance('RSForm_PDFs', 'Table')) {
			if (!isset($post['adminemail_options'])) {
				$post['adminemail_options'] = array();
			}
			
			if (!isset($post['useremail_options'])) {
				$post['useremail_options'] = array();
			}
			
			$post['adminemail_options'] = implode(',', $post['adminemail_options']);
			$post['useremail_options']  = implode(',', $post['useremail_options']);
			
			if (!$row->bind($post)) {
				JError::raiseWarning(500, $row->getError());
				return false;
			}
			
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true)
						 ->select($db->qn('form_id'))
						 ->from($db->qn('#__rsform_pdfs'))
						 ->where($db->qn('form_id').'='.$db->q($post['form_id']));
			if (!$db->setQuery($query)->loadResult()) {
				$query = $db->getQuery(true)
							->insert($db->qn('#__rsform_pdfs'))
							->set($db->qn('form_id').'='.$db->q($post['form_id']));
				$db->setQuery($query)->execute();
			}
			
			if ($row->store()) {
				return true;
			} else {
				JError::raiseWarning(500, $row->getError());
				return false;
			}
		}
	}
	
	protected function _createPDF($type, $args, $output=false)
	{
		$id  = $this->_createId($type, $args['submissionId']);
		$tmp = $this->_getTmp();
		
		// $args['form'], $args['placeholders'], $args['values'], $args['submissionId'], $args['userEmail']
		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/pdf/pdf.php';
		
		$cached_info = $this->_getInfo($args['form']->FormId);
		if (!empty($cached_info))
		{
			$info = clone $cached_info;
			jimport('joomla.filesystem.file');
			
			$pdf = new RSFormPDF();
			
			if (!empty($info->{$type.'email_php'}))
				eval($info->{$type.'email_php'});
			
			$info->{$type.'email_layout'}   = str_replace($args['placeholders'], $args['values'], $info->{$type.'email_layout'});
			$info->{$type.'email_filename'} = $this->_getFilename($info->{$type.'email_filename'}, $args['placeholders'], $args['values']);
			
			if (strlen($info->{$type.'email_userpass'}) && strpos($info->{$type.'email_userpass'}, '{') !== false) {
				$info->{$type.'email_userpass'} = str_replace($args['placeholders'], $args['values'], $info->{$type.'email_userpass'});
			}
			if (strlen($info->{$type.'email_ownerpass'}) && strpos($info->{$type.'email_ownerpass'}, '{') !== false) {
				$info->{$type.'email_ownerpass'} = str_replace($args['placeholders'], $args['values'], $info->{$type.'email_ownerpass'});
			}
			
			// Sitepath placeholder
			$info->{$type.'email_layout'} = str_replace('{sitepath}', JPATH_SITE, $info->{$type.'email_layout'});
			
			// Create the allowed options
			if (strlen($info->{$type.'email_options'})) {
				$options = explode(',', $info->{$type.'email_options'});
			} else {
				$options = array();
			}
			
			if (!strlen($info->{$type.'email_layout'})) {
				return;
			}
			
			// Render the PDF
			$pdf->render($info->{$type.'email_filename'}, $info->{$type.'email_layout'});
			
			if ($info->{$type.'email_userpass'} || $info->{$type.'email_ownerpass'} || count($options) < 4) {
				$pdf->dompdf->get_canvas()->get_cpdf()->setEncryption($info->{$type.'email_userpass'}, $info->{$type.'email_ownerpass'}, $options);
			}
			
			if ($output) {
				ob_end_clean();
				$pdf->dompdf->stream($info->{$type.'email_filename'});
				
				JFactory::getApplication()->close();
			} elseif ($info->{$type.'email_send'}) {
				$path 	= $tmp.'/'.$id.'/'.$info->{$type.'email_filename'};
				$buffer = $pdf->dompdf->output();
				
				if (JFile::write($path, $buffer)) {
					$args[$type.'Email']['files'][] = $path;
				}
			}
		}
	}
	
	public function rsfp_beforeUserEmail($args)
	{
		$this->_createPDF('user', $args);
	}
	
	public function rsfp_beforeAdminEmail($args)
	{
		$this->_createPDF('admin', $args);
	}
	
	protected function _getInfo($formId)
	{
		static $cache;
		if (!is_array($cache))
			$cache = array();
		
		$formId = (int) $formId;
		
		if (!isset($cache[$formId]))
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__rsform_pdfs WHERE form_id='".(int) $formId."'");
			$cache[$formId] = $db->loadObject();
		}
		
		return $cache[$formId];
	}
	
	protected function _getFilename($filename, $replace, $with)
	{
		$filename = str_replace($replace, $with, $filename);
		$filename = str_replace(array('\\', '/'), '', $filename);
		if (empty($filename))
			$filename = 'attachment';
		
		return $filename.'.pdf';
	}
	
	protected function _createId($suffix, $sid)
	{
		static $hash;
		if (!is_array($hash)) {
			$hash = array();
		}
		if (!isset($hash[$sid])) {
			$hash[$sid] = md5(uniqid($sid));
		}
		
		return $hash[$sid].'_'.$suffix;
	}
	
	protected function _getTmp()
	{
		static $tmp;
		if (!$tmp)
		{
			$mainframe = JFactory::getApplication();
			$tmp = $mainframe->getCfg('tmp_path');
		}
		
		return $tmp;
	}
	
	public function rsfp_f_onAfterFormProcess($args)
	{
		// $args['SubmissionId'], $args['formId']
		// cleanup
		
		$info = $this->_getInfo($args['formId']);
		
		if (!empty($info) && ($info->useremail_send || $info->adminemail_send))
		{
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			list($replace, $with) = RSFormProHelper::getReplacements($args['SubmissionId']);
			$tmp = $this->_getTmp();
			
			if ($info->useremail_send)
			{
				$id = $this->_createId('user', $args['SubmissionId']);
				$info->useremail_filename = $this->_getFilename($info->useremail_filename, $replace, $with);
				$dir  = $tmp.'/'.$id;
				$path = $dir.'/'.$info->useremail_filename;
				if (file_exists($path) && is_file($path))
					JFile::delete($path);
				if (is_dir($dir))
					JFolder::delete($dir);
			}
			if ($info->adminemail_send)
			{
				$id = $this->_createId('admin', $args['SubmissionId']);
				$info->adminemail_filename = $this->_getFilename($info->adminemail_filename, $replace, $with);
				$dir  = $tmp.'/'.$id;
				$path = $dir.'/'.$info->adminemail_filename;
				if (file_exists($path) && is_file($path))
					JFile::delete($path);
				if (is_dir($dir))
					JFolder::delete($dir);
			}
		}
	}
	
	public function rsfp_bk_onAfterShowFormScriptsTabsTab()
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppdf');
		
		echo '<li><a href="javascript: void(0);" id="rsfppdf"><span class="rsficon rsficon-file-pdf-o"></span><span class="inner-text">'.JText::_('RSFP_PHP_PDF_SCRIPTS').'</span></a></li>';
	}
	
	public function rsfp_bk_onAfterShowFormScriptsTabs()
	{
		if (!$this->_loadRow()) return;
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppdf');
		?>
		<div id="pdf_scripts">
		<table class="admintable table">
			<tr class="info">
				<td width="250" align="right" class="key" style="width: 250px;"><?php echo JText::_('RSFP_PDF_SEND_USER_EMAIL_PHP'); ?></td>
				<td><?php echo JText::_('RSFP_PDF_SEND_USER_EMAIL_PHP_DESC'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><textarea rows="20" cols="75" style="width:100%;" class="codemirror-php" name="pdf[useremail_php]" id="useremail_php"><?php echo RSFormProHelper::htmlEscape($this->row->useremail_php); ?></textarea></td>
			</tr>
			<tr class="info">
				<td width="250" align="right" class="key" style="width: 250px;"><?php echo JText::_('RSFP_PDF_SEND_ADMIN_EMAIL_PHP'); ?></td>
				<td><?php echo JText::_('RSFP_PDF_SEND_ADMIN_EMAIL_PHP_DESC'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><textarea rows="20" cols="75" style="width:100%;" class="codemirror-php" name="pdf[adminemail_php]" id="adminemail_php"><?php echo RSFormProHelper::htmlEscape($this->row->adminemail_php); ?></textarea></td>
			</tr>
		</table>
		</div>
		<?php
	}
	
	public function rsfp_bk_onAfterShowUserEmail()
	{
		if (!$this->_loadRow()) return;
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppdf');
		
		$options = array(
			JHtml::_('select.option', 'print', JText::_('RSFP_PDF_OPTION_PRINT')),
			JHtml::_('select.option', 'modify', JText::_('RSFP_PDF_OPTION_MODIFY')),
			JHtml::_('select.option', 'copy', JText::_('RSFP_PDF_OPTION_COPY')),
			JHtml::_('select.option', 'add', JText::_('RSFP_PDF_OPTION_ADD'))
		);
		
		$lists['useremail_send'] = RSFormProHelper::renderHTML('select.booleanlist', 'pdf[useremail_send]', 'class="inputbox"', $this->row->useremail_send);
		$lists['options'] = JHTML::_('select.genericlist', $options, 'pdf[useremail_options][]', 'multiple="multiple"', 'value', 'text', explode(',',$this->row->useremail_options));
		?>
		<fieldset>
		<legend><?php echo JText::_('RSFP_PDF_ATTACHMENT'); ?></legend>
		<table style="width: 100%;" class="table">
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_SEND_USER_EMAIL_DESC'); ?>"><?php echo JText::_('RSFP_PDF_SEND_USER_EMAIL'); ?></span></td>
				<td><?php echo $lists['useremail_send']; ?></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_SEND_USER_EMAIL_FILENAME_DESC'); ?>"><?php echo JText::_('RSFP_PDF_SEND_USER_EMAIL_FILENAME'); ?></span></td>
				<td><input type="text" class="rs_inp rs_80" name="pdf[useremail_filename]" id="useremail_filename" value="<?php echo RSFormProHelper::htmlEscape($this->row->useremail_filename); ?>" size="35" /></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_SEND_USER_EMAIL_LAYOUT_DESC'); ?>"><?php echo JText::_('RSFP_PDF_SEND_USER_EMAIL_LAYOUT'); ?></span></td>
				<td><textarea rows="20" cols="75" style="width:100%;" class="rs_textarea codemirror-html" name="pdf[useremail_layout]" id="useremail_layout"><?php echo RSFormProHelper::htmlEscape($this->row->useremail_layout); ?></textarea></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_USER_PASSWORD_DESC'); ?>"><?php echo JText::_('RSFP_PDF_USER_PASSWORD'); ?></span></td>
				<td><input type="password" class="rs_inp rs_80" name="pdf[useremail_userpass]" id="useremail_userpass" value="<?php echo RSFormProHelper::htmlEscape($this->row->useremail_userpass); ?>" size="35" /></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_OWNER_PASSWORD_DESC'); ?>"><?php echo JText::_('RSFP_PDF_OWNER_PASSWORD'); ?></span></td>
				<td><input type="password" class="rs_inp rs_80" name="pdf[useremail_ownerpass]" id="useremail_ownerpass" value="<?php echo RSFormProHelper::htmlEscape($this->row->useremail_ownerpass); ?>" size="35" /></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_OPTIONS_DESC'); ?>"><?php echo JText::_('RSFP_PDF_OPTIONS'); ?></span></td>
				<td><?php echo $lists['options']; ?></td>
			</tr>
		</table>
		</fieldset>
		<?php
	}
	
	public function rsfp_bk_onAfterShowAdminEmail()
	{
		if (!$this->_loadRow()) return;
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppdf');
		
		$options = array(
			JHtml::_('select.option', 'print', JText::_('RSFP_PDF_OPTION_PRINT')),
			JHtml::_('select.option', 'modify', JText::_('RSFP_PDF_OPTION_MODIFY')),
			JHtml::_('select.option', 'copy', JText::_('RSFP_PDF_OPTION_COPY')),
			JHtml::_('select.option', 'add', JText::_('RSFP_PDF_OPTION_ADD'))
		);
		
		$lists['adminemail_send'] = RSFormProHelper::renderHTML('select.booleanlist', 'pdf[adminemail_send]', 'class="inputbox"', $this->row->adminemail_send);
		$lists['options'] = JHTML::_('select.genericlist', $options, 'pdf[adminemail_options][]', 'multiple="multiple"', 'value', 'text', explode(',',$this->row->adminemail_options));
		?>
		<fieldset>
		<legend><?php echo JText::_('RSFP_PDF_ATTACHMENT'); ?></legend>
		<table style="width: 100%;">
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_SEND_ADMIN_EMAIL_DESC'); ?>"><?php echo JText::_('RSFP_PDF_SEND_ADMIN_EMAIL'); ?></span></td>
				<td><?php echo $lists['adminemail_send']; ?></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_SEND_ADMIN_EMAIL_FILENAME_DESC'); ?>"><?php echo JText::_('RSFP_PDF_SEND_ADMIN_EMAIL_FILENAME'); ?></span></td>
				<td><input type="text" class="rs_inp rs_80" name="pdf[adminemail_filename]" id="adminemail_filename" value="<?php echo RSFormProHelper::htmlEscape($this->row->adminemail_filename); ?>" size="35" /></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_SEND_ADMIN_EMAIL_LAYOUT_DESC'); ?>"><?php echo JText::_('RSFP_PDF_SEND_ADMIN_EMAIL_LAYOUT'); ?></span></td>
				<td><textarea rows="20" cols="75" style="width:100%;" class="rs_textarea codemirror-html" name="pdf[adminemail_layout]" id="adminemail_layout"><?php echo RSFormProHelper::htmlEscape($this->row->adminemail_layout); ?></textarea></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_USER_PASSWORD_DESC'); ?>"><?php echo JText::_('RSFP_PDF_USER_PASSWORD'); ?></span></td>
				<td><input type="password" class="rs_inp rs_80" name="pdf[adminemail_userpass]" id="adminemail_userpass" value="<?php echo RSFormProHelper::htmlEscape($this->row->adminemail_userpass); ?>" size="35" /></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_OWNER_PASSWORD_DESC'); ?>"><?php echo JText::_('RSFP_PDF_OWNER_PASSWORD'); ?></span></td>
				<td><input type="password" class="rs_inp rs_80" name="pdf[adminemail_ownerpass]" id="adminemail_ownerpass" value="<?php echo RSFormProHelper::htmlEscape($this->row->adminemail_ownerpass); ?>" size="35" /></td>
			</tr>
			<tr>
				<td width="25%" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_PDF_OPTIONS_DESC'); ?>"><?php echo JText::_('RSFP_PDF_OPTIONS'); ?></span></td>
				<td><?php echo $lists['options']; ?></td>
			</tr>
		</table>
		</fieldset>
		<?php
	}
	
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs)
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfppdf');
		
		$data 	= array();
		$data[] = JHTML::_('select.option', 'dejavu sans', JText::_('RSFP_PDF_DEJAVU_SANS'), 'value', 'text', !file_exists(JPATH_COMPONENT.'/helpers/pdf/dompdf6/lib/fonts/DejaVuSans.ufm'));
		$data[] = JHTML::_('select.option', 'fireflysung', JText::_('RSFP_PDF_FIREFLYSUNG'), 'value', 'text', !file_exists(JPATH_COMPONENT.'/helpers/pdf/dompdf6/lib/fonts/fireflysung.ufm'));
		// get fonts
		$data[] = JHTML::_('select.option', 'courier', JText::_('RSFP_PDF_COURIER'));
		$data[] = JHTML::_('select.option', 'helvetica', JText::_('RSFP_PDF_HELVETICA'));
		$data[] = JHTML::_('select.option', 'times', JText::_('RSFP_PDF_TIMES'));
		
		$lists['font'] = JHTML::_('select.genericlist', $data, 'rsformConfig[pdf.font]', null, 'value', 'text', RSFormProHelper::getConfig('pdf.font'));
		
		// orientation
		$data = array(
			JHTML::_('select.option', 'portrait', JText::_('RSFP_PDF_PORTRAIT')),
			JHTML::_('select.option', 'landscape', JText::_('RSFP_PDF_LANDSCAPE'))
		);
		$lists['orientation'] = JHTML::_('select.genericlist', $data, 'rsformConfig[pdf.orientation]', null, 'value', 'text', RSFormProHelper::getConfig('pdf.orientation'));
		
		// paper size
		$data = array(
			JHTML::_('select.option', '4a0'),
			JHTML::_('select.option', '2a0'),
			JHTML::_('select.option', 'a0'),
			JHTML::_('select.option', 'a1'),
			JHTML::_('select.option', 'a2'),
			JHTML::_('select.option', 'a3'),
			JHTML::_('select.option', 'a4'),
			JHTML::_('select.option', 'a5'),
			JHTML::_('select.option', 'a6'),
			JHTML::_('select.option', 'a7'),
			JHTML::_('select.option', 'a8'),
			JHTML::_('select.option', 'a9'),
			JHTML::_('select.option', 'a10'),
			JHTML::_('select.option', 'b0'),
			JHTML::_('select.option', 'b1'),
			JHTML::_('select.option', 'b2'),
			JHTML::_('select.option', 'b3'),
			JHTML::_('select.option', 'b4'),
			JHTML::_('select.option', 'b5'),
			JHTML::_('select.option', 'b6'),
			JHTML::_('select.option', 'b7'),
			JHTML::_('select.option', 'b8'),
			JHTML::_('select.option', 'b9'),
			JHTML::_('select.option', 'b10'),
			JHTML::_('select.option', 'c0'),
			JHTML::_('select.option', 'c1'),
			JHTML::_('select.option', 'c2'),
			JHTML::_('select.option', 'c3'),
			JHTML::_('select.option', 'c4'),
			JHTML::_('select.option', 'c5'),
			JHTML::_('select.option', 'c6'),
			JHTML::_('select.option', 'c7'),
			JHTML::_('select.option', 'c8'),
			JHTML::_('select.option', 'c9'),
			JHTML::_('select.option', 'c10'),
			JHTML::_('select.option', 'ra0'),
			JHTML::_('select.option', 'ra1'),
			JHTML::_('select.option', 'ra2'),
			JHTML::_('select.option', 'ra3'),
			JHTML::_('select.option', 'ra4'),
			JHTML::_('select.option', 'sra0'),
			JHTML::_('select.option', 'sra1'),
			JHTML::_('select.option', 'sra2'),
			JHTML::_('select.option', 'sra3'),
			JHTML::_('select.option', 'sra4'),
			JHTML::_('select.option', 'letter'),
			JHTML::_('select.option', 'legal'),
			JHTML::_('select.option', 'ledger'),
			JHTML::_('select.option', 'tabloid'),
			JHTML::_('select.option', 'executive'),
			JHTML::_('select.option', 'folio'),
			JHTML::_('select.option', 'commercial #10 envelope'),
			JHTML::_('select.option', 'catalog #10 1/2 envelope'),
			JHTML::_('select.option', '8.5x11'),
			JHTML::_('select.option', '8.5x14'),
			JHTML::_('select.option', '11x17')
		);
		
		$lists['paper'] = JHTML::_('select.genericlist', $data, 'rsformConfig[pdf.paper]', null, 'value', 'text', RSFormProHelper::getConfig('pdf.paper'));
		
		$lists['remote'] = RSFormProHelper::renderHTML('select.booleanlist', 'rsformConfig[pdf.remote]', null, RSFormProHelper::getConfig('pdf.remote'));
		
		$tabs->addTitle(JText::_('RSFP_PDF_CONFIG'), 'form-pdf');
		
		ob_start();
		
		?>
		<div class="alert alert-info"><?php echo JText::_('RSFP_PDF_FONT_DESCRIPTION'); ?><br /><a href="http://www.rsjoomla.com/support/documentation/view-article/747-rsform-pro-pdf-plugin.html#unicode" target="_blank"><?php echo JText::_('RSFP_PDF_FONT_HOW_TO_ADD'); ?></a></div>
		<table class="admintable">
			<tr>
				<td align="right" class="key" nowrap="nowrap">
					<label><span title="<?php echo JText::_('RSFP_PDF_FONT_DESC'); ?>" class="hasTip"><?php echo JText::_('RSFP_PDF_FONT'); ?></span></label>
				</td>
				<td>
					<?php echo $lists['font']; ?>
				</td>
			</tr>
			<tr>
				<td align="right" class="key" nowrap="nowrap">
					<label><span title="<?php echo JText::_('RSFP_PDF_ORIENTATION_DESC'); ?>" class="hasTip"><?php echo JText::_('RSFP_PDF_ORIENTATION'); ?></span></label>
				</td>
				<td valign="middle">
					<?php echo $lists['orientation']; ?>
				</td>
			</tr>
			<tr>
				<td align="right" class="key" nowrap="nowrap">
					<label><span title="<?php echo JText::_('RSFP_PDF_PAPER_DESC'); ?>" class="hasTip"><?php echo JText::_('RSFP_PDF_PAPER'); ?></span></label>
				</td>
				<td>
					<?php echo $lists['paper']; ?>
				</td>
			</tr>
			<tr>
				<td align="right" class="key" nowrap="nowrap">
					<label><span title="<?php echo JText::_('RSFP_PDF_ALLOW_REMOTE_RESOURCES_DESC'); ?>" class="hasTip"><?php echo JText::_('RSFP_PDF_ALLOW_REMOTE_RESOURCES'); ?></span></label>
				</td>
				<td>
					<?php echo $lists['remote']; ?>
				</td>
			</tr>
		</table>
		<?php
		
		$contents = ob_get_contents();
		ob_end_clean();
		
		$tabs->addContent($contents);
	}
	
	public function rsfp_onAfterCreatePlaceholders($args)
	{
		// index.php?option=com_rsform&task=plugin&plugin_task=
		$hash = md5($args['submission']->SubmissionId.'{user}'.$args['submission']->DateSubmitted);
		$args['placeholders'][] = '{user_pdf}';
		$args['values'][] = JURI::root().'index.php?option=com_rsform&task=plugin&plugin_task=user_pdf&hash='.$hash;
		$hash = md5($args['submission']->SubmissionId.'{admin}'.$args['submission']->DateSubmitted);
		$args['placeholders'][] = '{admin_pdf}';
		$args['values'][] = JURI::root().'index.php?option=com_rsform&task=plugin&plugin_task=admin_pdf&hash='.$hash;
	}
	
	public function rsfp_f_onSwitchTasks()
	{
		$task = JRequest::getCmd('plugin_task');
		if ($task == 'user_pdf' || $task == 'admin_pdf')
		{
			$hash = JRequest::getCmd('hash');
			if (strlen($hash) == 32)
			{
				$type = $task == 'user_pdf' ? 'user' : 'admin';
				$db = JFactory::getDBO();
				$db->setQuery("SELECT SubmissionId, FormId FROM #__rsform_submissions WHERE MD5(CONCAT(SubmissionId, '{".$type."}', DateSubmitted)) = '".$db->escape($hash)."'");
				if ($submission = $db->loadObject())
				{
					$form = new stdClass();
					$form->FormId = $submission->FormId;
					
					@list($placeholders, $values) = RSFormProHelper::getReplacements($submission->SubmissionId);
					
					$args = array(
						'SubmissionId' => $submission->SubmissionId,
						'submissionId' => $submission->SubmissionId,
						'form' => $form,
						'placeholders' => $placeholders,
						'values' => $values,
					);
					if ($task == 'user_pdf')
						$this->_createPDF('user', $args, true);
					elseif ($task == 'admin_pdf')
						$this->_createPDF('admin', $args, true);
				}
			}
		}
	}
	
	protected function _loadRow()
	{
		if (empty($this->row))
		{
			$this->row = JTable::getInstance('RSForm_PDFs', 'Table');
			if (empty($this->row))
				return false;
			$formId = JFactory::getApplication()->input->getInt('formId');
			$this->row->load($formId);
		}
		
		return true;
	}
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_pdfs')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_pdfs'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($pdf = $db->loadObject()) {
			// No need for a form_id
			unset($pdf->form_id);
			
			$xml->add('pdf');
			foreach ($pdf as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/pdf');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->pdf)) {
			$data = array(
				'form_id' => $form->FormId
			);
			
			foreach ($xml->pdf->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			
			$row = JTable::getInstance('RSForm_PDFs', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_pdfs')
						->set(array(
								$db->qn('form_id') .'='. $db->q($form->FormId),
						));
				$db->setQuery($query)->execute();
			}
			
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_pdfs');
	}
}