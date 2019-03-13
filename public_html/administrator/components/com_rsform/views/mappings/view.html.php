<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewMappings extends JViewLegacy
{
	public function display( $tpl = null )
	{
        if (!JFactory::getUser()->authorise('forms.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		$model 			= $this->getModel('mappings');
		$this->formId   = JFactory::getApplication()->input->getInt('formId');
		$this->fields   = $this->get('quickFields');
		$this->mapping 	= $this->get('mapping');
		$this->config 	= array(
			'connection' => $this->mapping->connection,
			'host' 		 => $this->mapping->host,
			'driver' 	 => !empty($this->mapping->driver) ? $this->mapping->driver : JFactory::getConfig()->get('dbtype'),
			'port' 		 => $this->mapping->port,
			'username'   => $this->mapping->username,
			'password' 	 => $this->mapping->password,
			'database'   => $this->mapping->database,
			'table' 	 => $this->mapping->table
		);
		try {
			$tables = $model->getTables($this->config);
		} catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}
		
		// Connection type
		$lists['MappingConnection'] = JHtml::_('select.booleanlist', 'connection', 'class="inputbox" onclick="enableDbDetails(this.value)"', $this->mapping->connection, JText::_('RSFP_FORM_MAPPINGS_CONNECTION_REMOTE'), JText::_('RSFP_FORM_MAPPINGS_CONNECTION_LOCAL'));
		
		// Driver
		$connectors = JDatabaseDriver::getConnectors();
		$supported = array('mysql', 'mysqli', 'pdomysql', 'postgresql', 'sqlsrv', 'sqlazure');
		$mconnectors = array();
		if ($connectors) {
			foreach ($connectors as $connector) {
				if (in_array($connector, $supported)) {
					$mconnectors[] = JHtml::_('select.option', $connector);
				}
			}
		}
		$lists['MappingDriver'] = JHtml::_('select.genericlist', $mconnectors, 'driver', 'class="inputbox"', 'value', 'text', $this->mapping->driver);
		
		// Method
		$lists['MappingMethod'] = JHtml::_('select.radiolist',  array(
					JHtml::_('select.option',  '0', JText::_( 'RSFP_FORM_MAPPINGS_METHOD_INSERT' ) ),
					JHtml::_('select.option',  '3', JText::_( 'RSFP_FORM_MAPPINGS_METHOD_REPLACE' ) ),
					JHtml::_('select.option',  '1', JText::_( 'RSFP_FORM_MAPPINGS_METHOD_UPDATE' ) ),
					JHtml::_('select.option',  '2', JText::_( 'RSFP_FORM_MAPPINGS_METHOD_DELETE' ) )
				), 'method', 'class="inputbox"', 'value', 'text', (int) $this->mapping->method);
		
		$mtables = array(
			JHtml::_('select.option', '0', JText::_( 'RSFP_FORM_MAPPINGS_SELECT_TABLE'))
		);
		if (!empty($tables)) {
			foreach ($tables as $table) {
				$mtables[] = JHtml::_('select.option',  $table, $table);
			}
		}
		
		// Tables
		$lists['tables'] = JHtml::_('select.genericlist',  $mtables, 'table', 'class="inputbox" onchange="mpColumns(this.value)"', 'value', 'text', $this->mapping->table);

		// Assing lists
		$this->lists = $lists;

        JHtml::script('com_rsform/admin/placeholders.js', array('relative' => true, 'version' => 'auto'));

        $displayPlaceholders = array(
            '{last_insert_id}',
            '{global:username}',
            '{global:userid}',
            '{global:useremail}',
            '{global:fullname}',
            '{global:userip}',
            '{global:date_added}',
            '{global:sitename}',
            '{global:siteurl}',
            '{global:confirmation}',
            '{global:deletion}',
            '{global:submissionid}',
            '{global:submission_id}',
            '{global:mailfrom}',
            '{global:fromname}',
            '{global:formid}',
            '{global:language}',
        );
		
		foreach ($this->fields as $fields){
			$displayPlaceholders = array_merge($displayPlaceholders, $fields['display']);
		};

		RSFormProAssets::addScriptDeclaration('
				var $displayPlaceholders = "' . implode(',', $displayPlaceholders) . '";
				RSFormPro.Placeholders = $displayPlaceholders.split(\',\');
			');
		
		parent::display($tpl);
	}
}