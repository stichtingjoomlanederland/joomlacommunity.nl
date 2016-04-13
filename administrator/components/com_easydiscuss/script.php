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
	/**
	 * Triggered after the installation is completed
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function postflight()
	{
		ob_start();
		?>
<style type="text/css">
#j-main-container > .adminform > tbody > tr:first-child {
	display: none !important;
}
</style>

<table border="0" cellpadding="0" cellspacing="0" style="
	background: #fff;
	background: #25384b;
	font: 12px/1.5 Arial, sans-serif;
	color: rgba(255,255,255,.5);
	width: 100%;
	max-width: 100%;
	border-radius: 4px;
	overflow: hidden;
	box-shadow: 0 1px 1px rgba(0,0,0,.08);
	text-align: left;
	margin: 0 auto 20px;
	">
	<tbody>
		<tr>
			<td style="padding: 40px; font-size: 12px;">
				<div style="margin-bottom: 20px;">
					<div style="display: table-cell; vertical-align: middle; padding-right: 15px">
						<img src="<?php echo JURI::root();?>/administrator/components/com_easydiscuss/setup/assets/images/logo.png" height="48" style="height:48px !important;">
					</div>
					<div style="display: table-cell; vertical-align: middle;">
						<b style="font-size: 26px; color: #fff; font-weight: normal; line-height: 1; margin: 5px 0;">EasyDiscuss</b>
					</div>
				</div>

				<p style="font-size: 14px; color: rgba(255,255,255,.8);">
					Thank you for your recent purchase of EasyDiscuss, the best Q&A component for Joomla! This is a confirmation message that the necessary setup files are already loaded on the site.</p>
				<p style="font-size: 14px; color: rgba(255,255,255,.8);">You will need to proceed with the installation process by clicking on the button below.</p>

				<br />

				<a href="<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&amp;install=true" style="
						background-color: #6c5;
						border-radius: 4px;
						color: #fff;
						display: inline-block;
						font-weight: bold;
						font-size: 16px;
						padding: 10px 15px;
						text-decoration: none !important;
				">
					Proceed With Installation &rarr;
				</a>
			</td>
		</tr>
	</tbody>
</table>
		<?php
		$contents 	= ob_get_contents();
		ob_end_clean();

		echo $contents;
	}


	/**
	 * Triggered before the installation is complete
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preflight()
	{
		// During the preflight, we need to create a new installer file in the temporary folder
		$file = JPATH_ROOT . '/tmp/easydiscuss.installation';

		// Determines if the installation is a new installation or old installation.
		$obj = new stdClass();
		$obj->new = false;
		$obj->step = 1;
		$obj->status = 'installing';

		$contents = json_encode($obj);

		if (!JFile::exists($file)) {
			JFile::write($file, $contents);
		}

		// remove old constant.php if exits.
		$this->removeConstantFile();

		if ($this->isUpgradeFrom3x()) {

			// remove older helper files
			$this->removeOldHelpers();
		}

		// now let check the eb config
		$this->checkEDVersionConfig();

	}

	/**
	 * Responsible to check ed configs db version
	 *
	 * @since	4.0
	 * @access	public
	 * @param
	 * @return
	 */
	public function checkEDVersionConfig()
	{
		// if there is the config table but no dbversion, we know this upgrade is coming from pior 5.0. lets add on dbversion into config table.
		if ($this->isUpgradeFrom3x()) {

			// get current installed ed version.
			$xmlfile = JPATH_ROOT. '/administrator/components/com_easydiscuss/easydiscuss.xml';

			// set this to version prior 3.8.0 so that it will execute the db script from 3.9.0 as well incase
			// this upgrade is from very old version.
			$version = '3.1.0';

			if (JFile::exists($xmlfile)) {
				$contents = JFile::read($xmlfile);
				$parser = simplexml_load_string($contents);
				$version = $parser->xpath('version');
				$version = (string) $version[0];
			}

			$db = JFactory::getDBO();

			// ok, now we got the version. lets add this version into dbversion.
			$query = 'INSERT INTO ' . $db->quoteName('#__discuss_configs') . ' (`name`, `params`) VALUES';
			$query .= ' (' . $db->Quote('dbversion') . ',' . $db->Quote($version) . '),';
			$query .= ' (' . $db->Quote('scriptversion') . ',' . $db->Quote($version) . ')';

			$db->setQuery($query);
			$db->query();
		}
	}

	private function isUpgradeFrom3x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {

			$isUpgrade = false;

			$db = JFactory::getDBO();

			$jConfig = JFactory::getConfig();
			$prefix = $jConfig->get('dbprefix');

			$query = "SHOW TABLES LIKE '%" . $prefix . "discuss_configs%'";
			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result) {
				// this is an upgrade. lets check if the upgrade from 3.x or not.
				$query = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__discuss_configs') . ' WHERE ' . $db->quoteName('name') . '=' . $db->Quote('dbversion');
				$db->setQuery($query);

				$exists = $db->loadResult();
				if (!$exists) {
					$isUpgrade = true;
				}
			}
		}

		return $isUpgrade;
	}

	/**
	 * Responsible to remove old constant.php file to avoid redefine of same constant error
	 *
	 * @since	4.0
	 * @access	public
	 * @param
	 * @return
	 */
	public function removeConstantFile()
	{
		$file = JPATH_ROOT. '/components/com_easydiscuss/constants.php';
		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	/**
	 * Responsible to remove old helper files
	 *
	 * @since	4.0
	 * @access	public
	 * @param
	 * @return
	 */
	public function removeOldHelpers()
	{
		// helpers
		$path = JPATH_ROOT . '/components/com_easydiscuss/helpers';
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}

	public function uninstall()
	{
		// @TODO: Unpublish plugins / modules.
	}

	public function update()
	{
		// return $this->execute();
	}



}
