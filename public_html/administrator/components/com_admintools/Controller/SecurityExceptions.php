<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use Akeeba\AdminTools\Admin\Model\BlacklistedAddresses;
use FOF30\Controller\DataController;
use JText;

class SecurityExceptions extends DataController
{
	use CustomACL;

	public function ban()
	{
		$this->csrfProtection();

		$id = $this->input->getString('id', '');

		if (empty($id))
		{
			throw new \Exception(JText::_('COM_ADMINTOOLS_ERR_SECURITYEXCEPTION_BAN_NOID'), 500);
		}

		/** @var \Akeeba\AdminTools\Admin\Model\SecurityExceptions $model */
		$model = $this->getModel();
		/** @var \Akeeba\AdminTools\Admin\Model\SecurityExceptions item */
		$item = $model->find($id);

		/** @var BlacklistedAddresses $banModel */
		$banModel = $this->container->factory->model('BlacklistedAddresses')->tmpInstance();
		$data     = array(
			'id'          => 0,
			'ip'          => $item->ip,
			'description' => JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON_' . strtoupper($item->reason))
		);
		$banModel->save($data);

		$this->setRedirect('index.php?option=com_admintools&view=SecurityExceptions', JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_SAVED'));
	}

	public function unban()
	{
		$this->csrfProtection();

		$id = $this->input->getString('id', '');
		if (empty($id))
		{
			throw new \Exception(JText::_('COM_ADMINTOOLS_ERR_SECURITYEXCEPTION_BAN_NOID'), 500);
		}

		/** @var \Akeeba\AdminTools\Admin\Model\SecurityExceptions $model */
		$model = $this->getModel();
		/** @var \Akeeba\AdminTools\Admin\Model\SecurityExceptions item */
		$item = $model->find($id);

		/** @var BlacklistedAddresses $banModel */
		$banModel = $this->container->factory->model('BlacklistedAddresses')->tmpInstance();

		$banModel->ip($item->ip);
		$items = $banModel->get();

		foreach ($items as $banItem)
		{
			$banModel->delete($banItem->id);
		}

		$this->setRedirect('index.php?option=com_admintools&view=SecurityExceptions', JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_DELETED'));
	}
}