<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
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

if(!defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR);
}

class com_KomentoInstallerScript
{
	var $messages;
	var $status;
	var	$sourcePath;

	function execute()
	{
		//get version number from manifest file.
		$jinstaller	= JInstaller::getInstance();
		$installer = new KomentoInstaller( $jinstaller );
		$installer->execute();

		$this->messages	= $installer->getMessages();
	}

	function install($parent)
	{
		return $this->execute();
	}

	function uninstall($parent)
	{

	}

	function update($parent)
	{
		return $this->execute();
	}

	function preflight($type, $parent)
	{
		//check if php version is supported before proceed with installation.
		$phpVersion = floatval(phpversion());
		if($phpVersion < 5 )
		{
			$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage('Installation was unsuccessful because you are using an unsupported version of PHP. Komento supports only PHP5 and above. Please kindly upgrade your PHP version and try again.', 'error');

			return false;
		}

		//get source path and version number from manifest file.
		$installer	= JInstaller::getInstance();
		$manifest	= $installer->getManifest();
		$sourcePath	= $installer->getPath('source');

		$this->message		= array();
		$this->status		= true;
		$this->sourcePath	= $sourcePath;


		$file = $this->sourcePath . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'install.default.php';
		if( JFile::exists($file) )
		{
			require_once($file);
		}

		//this is needed as joomla failed to remove it themselve during uninstallation or failed attempt of installation
		if( class_exists( 'KomentoMenuMaintenance' ) )
		{
			KomentoMenuMaintenance::removeAdminMenu();
		}

		return true;
	}

	function postflight($type, $parent)
	{
		$messages = $this->messages;

		ob_start();
	?>

	<style type="text/css">
	.adminform tr th{
		display:none;
	}

	/* TYPOGRAPHY AND SPACING */
	#komento-installer td{
		font-family:Tahoma,Arial,sans-serif;
		font-size:11px;
		line-height:1.7;
	}
	#komento-installer td table td{
		padding:5px 2px 5px 10px;
	}

	/* MESSAGES */
	#komento-message {
		border:1px solid #ccc;
		padding:13px;

		border-radius:2px;
		-moz-border-radius:2px;
		-webkit-border-radius:2px;
	}

	#komento-message.error {
		border-color:#900;
		color: #900;
	}

	#komento-message.info {
		background:#ECEFF6;
		border-color:#c4cbdd;
		color:#555;
	}

	#komento-message.warning {
		border-color:#f90;
		color: #c30;
	}
	</style>

	<table id="komento-installer" width="100%" border="0" cellpadding="0" cellspacing="0">
		<?php
			foreach ($messages as $message) {
				?>
				<tr>
					<td><div id="komento-message" class="<?php echo $message['type']; ?>"><?php echo ucfirst($message['type']) . ' : ' . $message['message']; ?></div></td>
				</tr>
				<?php
			}
		?>
		<tr>
			<td>
				<div style="width:700px; padding:10px; margin:10px 0; border: 3px solid red">
					<span style="color: red; font-size: 24px; font-weight: bold;"><u>Important!</u></span> <span style="font-size: 18px;">If you are upgrading from Komento 1.0, you will have to go to the <a href="<?php echo rtrim( JURI::root(), '/' ); ?>/administrator/index.php?option=com_komento&amp;view=system&amp;active=database" style="text-decoration: underline;">Configuration Page</a> and perform a database update for Komento 2.0 to work properly.</span>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="padding:20px 0"><img src="http://stackideas.com/images/komento/install_success1.png" /></div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="width:700px; padding-left:10px">
					Komento is a powerful comment extension that allows visitors to leave comments and more in articles, blogs and product pages.
					<br />
					It is packed with easy-to-use commenting features and highly secured for a lightweight Joomla comment addon.
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td colspan="2">To get our latest news and promotions :</td>
					</tr>
					<tr>
						<td>Like us on Facebook :</td>
						<td>
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>
							<div class="fb-like" data-href="http://www.facebook.com/StackIdeas" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
						</td>
					<tr>
						<td>Follow us on Twitter :</td>
						<td>
							<a href="https://twitter.com/stackideas" class="twitter-follow-button" data-show-count="false">Follow @stackideas</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</td>
					</tr>
					<tr>
						<td colspan="2">If you use Komento, please post a rating and a review at <a href="http://extensions.joomla.org/extensions/contacts-and-feedback/articles-comments/20527" target="_blank">Joomla! Extension Directory</a>.</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php

	$html = ob_get_contents();
	@ob_end_clean();

	echo $html;

	return true;
	}
}
