<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerMappings extends RsformController
{
	public function getTables() {
		$model		= $this->getModel('mappings');
		$input		= JFactory::getApplication()->input;
		
		$config = array(
			'connection' => $input->getInt('connection'),
			'host' 		 => $input->get('host', '', 'raw'),
			'driver'	 => $input->getCmd('driver'),
			'port' 		 => $input->getInt('port'),
			'username' 	 => $input->get('username', '', 'raw'),
			'password' 	 => $input->get('password', '', 'raw'),
			'database'   => $input->get('database', '', 'raw')
		);

		$html = '';
		
		try {
			$tables = $model->getTables($config);
			if (!is_array($tables)) {
				$msg = 0;
			} else {
				$msg = 1;
				$mtables = array(
					JHtml::_('select.option', '0', JText::_('RSFP_FORM_MAPPINGS_SELECT_TABLE'))
				);
				if (!empty($tables) && is_array($tables)) {
					foreach ($tables as $table) {
						$mtables[] = JHtml::_('select.option',  $table);
					}
				}
				
				$html = '<table class="admintable">
							<tr>
								<td width="160" style="width: 160px;" align="right" class="key">'.JText::_('RSFP_FORM_MAPPINGS_TABLE').'</td>
								<td>' . JHtml::_('select.genericlist',  $mtables, 'table', 'class="inputbox" onchange="mpColumns(this.value)"', 'value', 'text') . JHtml::image('com_rsform/admin/loading.gif', '', 'id="mappingloader2" style="vertical-align: middle; display: none;"',true) . '</td>
							</tr>
						</table>';
			}
			
			echo $msg.'|'.$html;
		} catch (Exception $e) {
			echo $e->getMessage().'|';
		}
		
		JFactory::getApplication()->close();
	}
	
	public function getColumns() {
		try {
			$input = JFactory::getApplication()->input;
			$cid   = $input->getInt('cid');
			
			if ($cid) {
				$row = JTable::getInstance('RSForm_Mappings', 'Table');
				$row->load($cid);
			}
			
			$config = array(
				'connection' => $input->getInt('connection'),
				'host' 		 => $input->get('host', '', 'raw'),
				'driver'	 => $input->getCmd('driver'),
				'port' 		 => $input->getInt('port'),
				'username' 	 => $input->get('username', '', 'raw'),
				'password' 	 => $input->get('password', '', 'raw'),
				'database'   => $input->get('database', '', 'raw'),
				'table'   	 => $input->get('table', '', 'raw')
			);
			
			echo RSFormProHelper::mappingsColumns($config, $input->getCmd('type', 'set'), !empty($row) ? $row : null);
		} catch (Exception $e) {
			echo $e->getMessage().'|';
		}
		
		JFactory::getApplication()->close();
	}
	
	public function save() {
		$model 	= $this->getModel('mappings');
		$row 	= $model->save();

		?>
		<script type="text/javascript">
		window.close();
		
		<?php if ($row !== false) { ?>
			window.opener.ShowMappings(<?php echo $row->formId; ?>);
		<?php } ?>
		</script>
		<?php
		JFactory::getApplication()->close();
	}
	
	public function ordering() {
		$db   		= JFactory::getDbo();
		$post 		= RSFormProHelper::getRawPost();
		
		foreach ($post as $key => $val) {
			$key = (int) str_replace('mpid_', '', $key);
			$val = (int) $val;
			if (empty($key)) {
				continue;
			}
			
			$query = $db->getQuery(true)
						->update($db->qn('#__rsform_mappings'))
						->set($db->qn('ordering').'='.$db->q($val))
						->where($db->qn('id').'='.$db->q($key));
			
			$db->setQuery($query)
			   ->execute();
		}
		
		JFactory::getApplication()->close();
	}
	
	public function remove() {
		$input  = JFactory::getApplication()->input;
		$model  = $this->getModel('mappings');
		$formId = $input->getInt('formId');
		
		$model->remove();
		
		$input->set('view', 	'forms');
		$input->set('layout', 	'edit_mappings');
		$input->set('tmpl', 	'component');
		$input->set('formId', 	$formId);
		
		parent::display();
		
		JFactory::getApplication()->close();
	}
	
	public function showMappings() {
		$input  = JFactory::getApplication()->input;
		$formId = $input->getInt('formId');
		
		$input->set('view', 	'forms');
		$input->set('layout', 	'edit_mappings');
		$input->set('tmpl', 	'component');
		$input->set('formId', 	$formId);
		
		parent::display();
		
		JFactory::getApplication()->close();
	}
}