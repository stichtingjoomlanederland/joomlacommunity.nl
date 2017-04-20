<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelImport extends JModelAdmin {
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		$jinput = JFactory::getApplication()->input;

		// Get the form.
		$form = $this->loadForm('com_rscomments.import', 'import', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;

		return $form;
	}

	/**
	 *	Method to get databse tables
	 */
	public function getTables() {
		return JFactory::getDbo()->getTableList();
	}

	/**
	 *	Method to get table columns
	 */
	public function getFields() {
		$db			= JFactory::getDbo();
		$table 		= JFactory::getApplication()->input->get('table', '', 'string');

		$columns = !empty($table) ? $db->getTableColumns($table, true) : array();
		return array_keys($columns);
	}

	/**
	 *	Method to get rscomments plugins
	 */
	public function getPlugins() {
		$plugins = JPluginHelper::getPlugin('rscomments'); 
		return $plugins;
	}

	/**
	 *	Method to register plugins
	 */
	public function registerPlugins() {
		$dispatcher	= JDispatcher::getInstance();
		$plugins = $this->getPlugins();

		if (!empty($plugins)) {
			foreach($plugins as $plugin) {
				$class = 'plgRSComments'.$plugin->name;
				new $class($dispatcher);
			}
		}
	}

	/**
	 * Method to save data
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function save($data = null) {
		$db 		= JFactory::getDbo();
		$query 		= $db->getQuery(true);
		$app		= JFactory::getApplication();

		$jinput		 	= $app->input;
		$jform 			= $jinput->get('jform', array(), 'array');
		$data 			= $jform['rsc_col'];
		$table			= $jinput->get('table', '');
		$plugin_class 	= $jinput->get('class', '');

		if(!empty($plugin_class)) {
			//load the plugins
			JPluginHelper::importPlugin('rscomments');
			$dispatcher	= JDispatcher::getInstance();

			$classname 	= 'plgRSComments'.strtolower($plugin_class);
			$plugin 	= new $classname($dispatcher);
			$count 		= $plugin->rscommentsImport();
			$msg		= JText::_('COM_RSCOMMENTS_IMPORT_PLUGIN_NO_OK');
			
			if ($count) {
				if ($count->comments > 0 || $count->subscriptions > 0) {
					$msg = JText::sprintf('COM_RSCOMMENTS_IMPORT_PLUGIN_OK',$count->comments,$count->subscriptions);
				}
			}
		} else {
			$rscTable = $mappedTable = array();
			
			foreach($data as $col => $val) {
				if(empty($val)) continue;
				$rscTable[] = $db->qn($col);
				// avoiding conflicts when the user selects to import duplicate values
				$mappedTable[] = $db->qn($val,$col);
			}

			if(!empty($rscTable) && !empty($mappedTable)) {
				$rscTable 		= implode(',',$rscTable);
				$mappedTable 	= implode(',',$mappedTable);

				// get the data to import
				$subquery = $db->getQuery(true);
				$subquery->select($mappedTable)->from($table);
				$db->setQuery($subquery);
				$db->execute();
				$imported_data = $db->loadAssocList();

				// import data to RSComments! comments_table
				$query->clear();
				$query->insert('#__rscomments_comments');
				$query->columns($rscTable);

				// create the set of values
				foreach($imported_data as $arrays => $columns) {
					$values = '';
					foreach($columns as $column => $value)
						$values .= $db->q($value).', ';

					$query->values(rtrim($values, ', '));
				}

				$db->setQuery($query);
				$db->execute();
				$affected 	= $db->getAffectedRows();
				$msg 		= JText::sprintf('COM_RSCOMMENTS_IMPORT_OK',$affected);
			} else {
				$msg 		= JText::_('COM_RSCOMMENTS_IMPORT_PLUGIN_NO_OK');
			}
		}
		$app->redirect('index.php?option=com_rscomments&view=import', $msg);

		return $affected;
	}

	public function getRSTabs() {
		$tabs = new RSTabs('com-rscomments-group');
		return $tabs;
	}
}