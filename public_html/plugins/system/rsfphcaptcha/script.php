<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemRsfphcaptchaInstallerScript
{
	protected static $minJoomla = '3.7.0';
	protected static $minComponent = '2.3.3';

	public function preflight($type, $parent)
	{
		if ($type == 'uninstall')
		{
			return true;
		}

		try
		{
			$source = $parent->getParent()->getPath('source');

			$jversion = new JVersion();
			if (!$jversion->isCompatible(static::$minJoomla))
			{
				throw new Exception(sprintf('Please upgrade to at least Joomla! %s before continuing!', static::$minJoomla));
			}

			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php'))
			{
				throw new Exception('Please install the RSForm! Pro component before continuing.');
			}

			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php') || !file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php'))
			{
				throw new Exception(sprintf('Please upgrade RSForm! Pro to at least version %s before continuing!', static::$minComponent));
			}

			// Check version matches
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php';

			if (!class_exists('RSFormProVersion') || version_compare((string) new RSFormProVersion, static::$minComponent, '<'))
			{
				throw new Exception(sprintf('Please upgrade RSForm! Pro to at least version %s before continuing!', static::$minComponent));
			}

			// All good.
			// Copy needed files
			$this->copyFiles($source);
			
			// Update? Run our SQL file
			if ($type == 'update')
			{
				$this->runSQL($source, 'install');
			}
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		
		return true;
	}
	
	protected function copyFiles($source)
	{
		jimport('joomla.filesystem.folder');
		
		// Copy /admin files
		$src	= $source.'/admin';
		$dest 	= JPATH_ADMINISTRATOR.'/components/com_rsform';
		if (!JFolder::copy($src, $dest, '', true))
		{
			throw new Exception('Could not copy to '.str_replace(JPATH_ADMINISTRATOR, '', $dest).', please make sure destination is writable!');
		}
	}

	protected function runSQL($source, $file)
	{
		$db = JFactory::getDbo();
		$sqlfile = $source . '/sql/mysql/' . $file . '.sql';

		if (file_exists($sqlfile))
		{
			$buffer = file_get_contents($sqlfile);
			if ($buffer !== false)
			{
				$queries = $db->splitSql($buffer);
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '')
					{
						$db->setQuery($query)->execute();
					}
				}
			}
		}
	}
}