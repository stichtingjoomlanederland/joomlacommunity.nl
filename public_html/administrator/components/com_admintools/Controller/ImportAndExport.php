<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use Akeeba\AdminTools\Admin\Helper\Storage;
use FOF30\Controller\Controller;

class ImportAndExport extends Controller
{
	use CustomACL;

	public function export()
	{
		$this->layout = 'export';

		parent::display();
	}

	public function import()
	{
		$this->layout = 'import';

		parent::display();
	}

	public function doexport()
	{
		/** @var \Akeeba\AdminTools\Admin\Model\ImportAndExport $model */
		$model = $this->getModel();
		$data  = $model->exportData();

		if($data)
		{
			$json = json_encode($data);

			// Clear cache
			while (@ob_end_clean())
			{
				;
			}

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public", false);

			// Send MIME headers
			header("Content-Description: File Transfer");
			header('Content-Type: json');
			header("Accept-Ranges: bytes");
			header('Content-Disposition: attachment; filename="admintools_settings.json"');
			header('Content-Transfer-Encoding: text');
			header('Connection: close');
			header('Content-Length: ' . strlen($json));

			echo $json;

			\JFactory::getApplication()->close();
		}
		else
		{
			$this->setRedirect('index.php?option=com_admintools&view=ImportAndExport&task=export', \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_SELECT_DATA_WARN'), 'warning');
		}
	}

	public function doimport()
	{
		$params = Storage::getInstance();
		$params->setValue('quickstart', 1, true);

		/** @var \Akeeba\AdminTools\Admin\Model\ImportAndExport $model */
		$model  = $this->getModel();

		try
		{
			$model->importData();

			$type = null;
			$msg  = \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_IMPORT_OK');
		}
		catch (\Exception $e)
		{
			$type = 'error';
			$msg  = $e->getMessage();
		}

		$this->setRedirect('index.php?option=com_admintools&view=ImportAndExport&task=import', $msg, $type);
	}
}