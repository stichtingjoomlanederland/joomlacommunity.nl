<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

function _validateEasyDiscussVersion()
{
	$valid		= true;
	$parser		= null;
	$version	= '';

	$xmlFile	= JPATH_ROOT . '/administrator/components/com_easydiscuss/easydiscuss.xml';
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

	}
	return $valid;
}

function com_install()
{
	$message	= array();
	$status		= true;

	//check if php version is supported before proceed with installation.
	if( _validateEasyDiscussVersion() )
	{
		require_once JPATH_ROOT . '/administrator/components/com_easydiscuss/install.default.php';

		$jinstaller = JInstaller::getInstance();
		$installer = new EasyDiscussInstaller($jinstaller);
		$installer->execute();
		$message = $installer->getMessages();
	}
	else
	{
		$msg	= array(
					'message' => 'Fatal Error : Installation was unsuccessful due to older version of EasyDiscuss detected. You will need to first perform the uninstall-and-reinstall steps to upgrade your older version of EasyDiscuss to version 3.0.x. Please be rest assured that uninstalling EasyDiscuss will not remove any data and all your records and configuration will remain intact.',
					'type' => 'message'
					);

		$message[]  = $msg;
		$status = false;
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
				<div><img src="https://stackideas.com/images/easydiscuss/success_32.png" /></div>
			</td>
		</tr>
		<?php
			foreach($message as $msgString)
			{
				$msg = $msgString['message'];
				$classname = $msgString['type'];
				?>
				<tr>
					<td><div id="easydiscuss-message" class="<?php echo $classname; ?>"><?php echo $msg; ?></div></td>
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
