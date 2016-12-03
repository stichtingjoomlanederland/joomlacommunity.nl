<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPPDFInstallerScript
{
	public function preflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$app = JFactory::getApplication();
		
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) {
			$app->enqueueMessage('Please install the RSForm! Pro component before continuing.', 'error');
			return false;
		}
		
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php')) {
			$app->enqueueMessage('Please upgrade RSForm! Pro to at least version 1.51.0 before continuing!', 'error');
			return false;
		}
		
		$jversion = new JVersion();
		if (!$jversion->isCompatible('2.5.28')) {
			$app->enqueueMessage('Please upgrade to at least Joomla! 2.5.28 before continuing!', 'error');
			return false;
		}
		
		return true;
	}
	
	public function update($parent) {
		$this->copyFiles($parent);
		
		$db = JFactory::getDbo();
		$columns = $db->getTableColumns('#__rsform_pdfs');
		
		if (!isset($columns['useremail_userpass'])) {
			$db->setQuery("ALTER TABLE `#__rsform_pdfs` ADD `useremail_userpass` VARCHAR( 255 ) NOT NULL AFTER `useremail_layout`,".
						  "ADD `useremail_ownerpass` VARCHAR( 255 ) NOT NULL AFTER `useremail_userpass`,".
						  "ADD `adminemail_userpass` VARCHAR( 255 ) NOT NULL AFTER `adminemail_layout`,".
						  "ADD `adminemail_ownerpass` VARCHAR( 255 ) NOT NULL AFTER `adminemail_userpass`,".
						  "ADD `useremail_options` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'print,modify,copy,add' AFTER `useremail_ownerpass`,".
						  "ADD `adminemail_options` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'print,modify,copy,add' AFTER `adminemail_ownerpass`");
			$db->execute();
		}
		
		// Run our SQL file
		$source = $parent->getParent()->getPath('source');
		$this->runSQL($source, 'install');
	}
	
	public function install($parent) {
		$this->copyFiles($parent);
	}
	
	protected function copyFiles($parent) {
		$app = JFactory::getApplication();
		$installer = $parent->getParent();
		$src = $installer->getPath('source').'/admin';
		$dest = JPATH_ADMINISTRATOR.'/components/com_rsform';
		
		if (!JFolder::copy($src, $dest, '', true)) {
			$app->enqueueMessage('Could not copy to '.str_replace(JPATH_SITE, '', $dest).', please make sure destination is writable!', 'error');
		}
	}
	
	protected function runSQL($source, $file) {
		$db 	= JFactory::getDbo();
		$driver = strtolower($db->name);
		if (strpos($driver, 'mysql') !== false) {
			$driver = 'mysql';
		}
		
		$sqlfile = $source.'/sql/'.$driver.'/'.$file.'.sql';
		
		if (file_exists($sqlfile)) {
			$buffer = file_get_contents($sqlfile);
			if ($buffer !== false) {
				$queries = JInstallerHelper::splitSql($buffer);
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->execute()) {
							throw new Exception(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						}
					}
				}
			}
		}
	}
	
	public function postflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$jversion = new JVersion;
		?>
		<style type="text/css">
		.version-history {
			margin: 0 0 2em 0;
			padding: 0;
			list-style-type: none;
		}
		.version-history > li {
			margin: 0 0 0.5em 0;
			padding: 0 0 0 4em;
			text-align:left;
			font-weight:normal;
		}
		.version-new,
		.version-fixed,
		.version-upgraded {
			float: left;
			font-size: 0.8em;
			margin-left: -4.9em;
			width: 4.5em;
			color: white;
			text-align: center;
			font-weight: bold;
			text-transform: uppercase;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
		}

		.version-new {
			background: #7dc35b;
		}
		.version-fixed {
			background: #e9a130;
		}
		.version-upgraded {
			background: #61b3de;
		}
		<?php if (!$jversion->isCompatible('3.0')) { ?>
		.btn {
		  display: inline-block;
		  *display: inline;
		  padding: 4px 12px;
		  margin-bottom: 0;
		  *margin-left: .3em;
		  font-size: 14px;
		  line-height: 20px;
		  color: #333333;
		  text-align: center;
		  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
		  vertical-align: middle;
		  cursor: pointer;
		  background-color: #f5f5f5;
		  *background-color: #e6e6e6;
		  background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
		  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
		  background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
		  background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
		  background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
		  background-repeat: repeat-x;
		  border: 1px solid #cccccc;
		  *border: 0;
		  border-color: #e6e6e6 #e6e6e6 #bfbfbf;
		  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
		  border-bottom-color: #b3b3b3;
		  -webkit-border-radius: 4px;
			 -moz-border-radius: 4px;
				  border-radius: 4px;
		  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);
		  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		  *zoom: 1;
		  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
			 -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
				  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
		}

		.btn:hover,
		.btn:focus,
		.btn:active,
		.btn.active,
		.btn.disabled,
		.btn[disabled] {
		  color: #333333;
		  background-color: #e6e6e6;
		  *background-color: #d9d9d9;
		}

		.btn:active,
		.btn.active {
		  background-color: #cccccc \9;
		}

		.btn:first-child {
		  *margin-left: 0;
		}

		.btn:hover,
		.btn:focus {
		  color: #333333;
		  text-decoration: none;
		  background-position: 0 -15px;
		  -webkit-transition: background-position 0.1s linear;
			 -moz-transition: background-position 0.1s linear;
			   -o-transition: background-position 0.1s linear;
				  transition: background-position 0.1s linear;
		}

		.btn:focus {
		  outline: thin dotted #333;
		  outline: 5px auto -webkit-focus-ring-color;
		  outline-offset: -2px;
		}

		.btn.active,
		.btn:active {
		  background-image: none;
		  outline: 0;
		  -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
			 -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
				  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
		}

		.btn.disabled,
		.btn[disabled] {
		  cursor: default;
		  background-image: none;
		  opacity: 0.65;
		  filter: alpha(opacity=65);
		  -webkit-box-shadow: none;
			 -moz-box-shadow: none;
				  box-shadow: none;
		}

		.btn-large {
		  padding: 11px 19px;
		  font-size: 17.5px;
		  -webkit-border-radius: 6px;
			 -moz-border-radius: 6px;
				  border-radius: 6px;
		}

		.btn-large [class^="icon-"],
		.btn-large [class*=" icon-"] {
		  margin-top: 4px;
		}

		.btn-primary {
		  color: #ffffff !important;
		  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
		  background-color: #006dcc;
		  *background-color: #0044cc;
		  background-image: -moz-linear-gradient(top, #0088cc, #0044cc);
		  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
		  background-image: -webkit-linear-gradient(top, #0088cc, #0044cc);
		  background-image: -o-linear-gradient(top, #0088cc, #0044cc);
		  background-image: linear-gradient(to bottom, #0088cc, #0044cc);
		  background-repeat: repeat-x;
		  border-color: #0044cc #0044cc #002a80;
		  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
		  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0044cc', GradientType=0);
		  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		}

		.btn-primary:hover,
		.btn-primary:focus,
		.btn-primary:active,
		.btn-primary.active,
		.btn-primary.disabled,
		.btn-primary[disabled] {
		  color: #ffffff;
		  background-color: #0044cc;
		  *background-color: #003bb3;
		}

		.btn-primary:active,
		.btn-primary.active {
		  background-color: #003399 \9;
		}
		<?php } ?>
		</style>

		<h3>RSForm! Pro PDF Plugin v1.51.2 Changelog</h3>
		<ul class="version-history">
			<li><span class="version-new">New</span> Configuration option to grab resources (eg. images) from remote locations.</li>
		</ul>
		<a class="btn btn-primary btn-large" href="<?php echo JRoute::_('index.php?option=com_rsform&view=forms'); ?>">Manage Forms</a>
		<a class="btn" href="https://www.rsjoomla.com/support/documentation/view-article/747-rsform-pro-pdf-plugin.html" target="_blank">Read the documentation</a>
		<a class="btn" href="https://www.rsjoomla.com/support.html" target="_blank">Get Support!</a>
		<div style="clear: both;"></div>
		<?php
	}
}