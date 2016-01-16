<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

/**
 * This file and method will automatically get called by Joomla
 * during the installation process
 **/

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_EasyDiscussInstallerScript
{
	protected $message;
	protected $status;
	protected $sourcePath;

	public function execute()
	{
		$jinstaller	= JInstaller::getInstance();
		$installer = new EasyDiscussInstaller( $jinstaller );
		$installer->execute();

		$this->messages	= $installer->getMessages();
	}

	public function install($parent)
	{
		return $this->execute();
	}

	public function uninstall($parent)
	{
		// @TODO: Unpublish plugins / modules.
	}

	public function update($parent)
	{
		return $this->execute();
	}

	public function _validateEasyDiscussVersion()
	{
		$valid 			= true;
		$parser         = null;
		$version        = '';

		$xmlFile        = JPATH_ROOT . '/administrator/components/com_easydiscuss/easydiscuss.xml';
		if( JFile::exists( $xmlFile ) )
		{

			$jVerArr		= explode('.', JVERSION);
			$joomlaVersion	= $jVerArr[0] . '.' . $jVerArr[1];

			$contents	= JFile::read( $xmlFile );

			if( $joomlaVersion >= '3.0' )
			{
				$parser 	= JFactory::getXML( $contents , false );
				$version	= $parser->xpath( 'version' );
			}
			else
			{
				$parser 	= JFactory::getXMLParser('Simple');
				$parser->loadString( $contents );

				$element 	= $parser->document->getElementByPath( 'version' );
				$version 	= $element->data();
			}

			if( $version < '3.0.0' )
			{
				$valid  = false;
			}


			// If the current installed version is lower than attachment bug patch 3.0.8598
			if( $version < '3.0.8597' )
			{
				$valid = 'warning';
			}
		}

		return $valid;
	}

	public function preflight($type, $parent)
	{
		//check if php version is supported before proceed with installation.
		$temp = self::_validateEasyDiscussVersion();
    	if( !$temp  )
    	{
			$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage('WARNING: PLEASE REMEMBER TO BACKUP THE ATTACHMENTS FOLDER OR IT WILL BE DELETED.You can find it at JOOMLA/media/com_easydiscuss/attachments.<br /><br />', 'message');
			$mainframe->enqueueMessage('Older version of EasyDiscuss detected. You will need to first perform the uninstall-and-reinstall steps to upgrade your older version of EasyDiscuss to version 3.0.x. Please be rest assured that uninstalling EasyDiscuss will not remove any data and all your records and configuration will remain intact.', 'message');

			return false;
		}

		if( $temp === 'warning' )
		{
			$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage('WARNING: Please install the latest patch 3.0.8598 (Bug: Attachments folder will be deleted if perform uninstallation prior of this patch), then only install EasyDiscuss 3.1.<br /><br />', 'message');

			return false;
		}

		//get source path and version number from manifest file.
		$installer	= JInstaller::getInstance();
		$manifest	= $installer->getManifest();
		$sourcePath	= $installer->getPath('source');

		$this->message		= array();
		$this->status		= true;
		$this->sourcePath	= $sourcePath;

		// if this is a uninstallation process, do not execute anything, just return true.
		if( $type == 'install' || $type == 'update' || $type == 'discover_install')
		{
			require_once( $this->sourcePath . '/admin/install.default.php' );

			//this is needed as joomla failed to remove it themselve during uninstallation or failed attempt of installation
			EasyDiscussInstaller::removeAdminMenu();
		}

		return true;
	}

	public function postflight($type, $parent)
	{
		$message	= $this->message;
		$status		= $this->status;


		// if this is a uninstallation process, do not execute anything.
		if( $type == 'install' || $type == 'update' || $type == 'discover_install')
		{
			require_once( $this->sourcePath . '/admin/install.default.php' );

			// fix invalid admin menu id with Joomla 1.6 or above
			EasyDiscussInstaller::fixMenuIds();

			//check menu items.
			EasyDiscussInstaller::checkMenu();
		}

		ob_start();
		?>

		<style type="text/css">
		/**
		 * Messages
		 */

		#easydiscuss-message {
			color: red;
			font-size:13px;
			margin-bottom: 15px;
			padding: 5px 10px 5px 35px;
		}

		#easydiscuss-message.error {
			border-top: solid 2px #900;
			border-bottom: solid 2px #900;
			color: #900;
		}

		#easydiscuss-message.info {
			border-top: solid 2px #06c;
			border-bottom: solid 2px #06c;
			color: #06c;
		}

		#easydiscuss-message.warning {
			border-top: solid 2px #f90;
			border-bottom: solid 2px #f90;
			color: #c30;
		}
		</style>

		<table width="100%" border="0">
			<tr>
				<td>
					<div><img src="http://stackideas.com/images/easydiscuss/success_32.png" /></div>
				</td>
			</tr>
			<?php
				foreach($message as $msgString)
				{
					$msg = explode(":", $msgString);
					switch(trim($msg[0]))
					{
						case 'Fatal Error':
							$classname = 'error';
							break;
						case 'Warning':
							$classname = 'warning';
							break;
						case 'Success':
						default:
							$classname = 'info';
							break;
					}
					?>
					<tr>
						<td><div id="easydiscuss-message" class="<?php echo $classname; ?>"><?php echo $msg[0] . ' : ' . $msg[1]; ?></div></td>
					</tr>
					<?php
				}
			?>
		</table>

		<?php
		$html = ob_get_contents();
		@ob_end_clean();

		echo $html;

		return $status;
	}
}
