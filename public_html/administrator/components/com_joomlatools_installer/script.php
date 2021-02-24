<?php
/**
 * @package     Joomlatools Installe
 * @copyright   Copyright (C) 2016 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
class com_joomlatools_installerInstallerScript
{
    protected function _getComponentVersion($component)
    {
        $query = /** @lang text */"SELECT manifest_cache FROM #__extensions WHERE type = 'component' AND element = '%s'";
        $query = sprintf($query, 'com_'.$component);

        if ($result = JFactory::getDbo()->setQuery($query)->loadResult())
        {
            $manifest = new JRegistry($result);

            return $manifest->get('version', null);
        }

        return null;
    }

    public function preflight($type, $installer)
    {
        $docman_version = $this->_getComponentVersion('docman');

        if ($docman_version && version_compare($docman_version, '2.1.5', '<'))
        {
            $warning = 'Your site has DOCman %s installed. Please upgrade DOCman first to 2.1.6 and then to 3.0 in this order. 
            This will ensure your data is properly migrated.
            We advise you to read our <a target="_blank" href="https://www.joomlatools.com/extensions/docman/documentation/upgrading/">upgrading guide</a>.';

            $installer->getParent()->abort(sprintf($warning, $docman_version));

            return false;
        }

        if(version_compare(phpversion(), '5.6', '<'))
        {
            $installer->getParent()->abort(sprintf(JText::_('Your server is running PHP %s which is an old and insecure version.
            It also contains a bug affecting the operation of our extensions.
            Please contact your host and ask them to upgrade PHP to at least 5.4 version on your server.'), phpversion()));

            return false;
        }

        if (version_compare(JVERSION, '3.9', '<'))
        {
            $installer->getParent()->abort(sprintf(JText::_('Your site is running Joomla %s which is an unsupported version.
            Please upgrade Joomla to the latest version first.'), JVERSION));

            return false;
        }

        return true;
    }

    public function postflight($type, $installer)
    {
        if ($type === 'discover_install') {
            return;
        }

        JFactory::getApplication()->setUserState('com_installer.redirect_url', 'index.php?option=com_joomlatools_installer&task=display');
        $installer->getParent()->setRedirectUrl('index.php?option=com_joomlatools_installer&task=display');
    }
}