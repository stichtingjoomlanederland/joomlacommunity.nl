<?php

/**
 * @package     JoomlatoolsUpdater
 * @copyright   Copyright (C) 2021 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemJoomlatoolsupdaterControllerLicense extends KControllerModel
{
    /** @var PlgSystemJoomlatoolsupdaterLicense */
    protected $_license;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_license = $this->getObject('license');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append([
            'model' => 'lib:model.empty',
            'view'  => 'com:default.view.json',
            'formats' => ['json']
        ]);

        parent::_initialize($config);

        $config->behaviors = [];
    }

    protected function _authenticate()
    {
        $query = $this->getRequest()->getQuery();

        $signature = $query->get('signature', 'raw');

        if (!$signature) {
            return false;
        }

        $postdata = file_get_contents("php://input");

        try {
            $public_key = $this->_license->getPublicKey();

            // Load required libraries
            $this->getObject('plg:system.joomlatoolsupdater.crypto.token');

            $rsa = new \Joomlatools\RSA\Crypt_RSA();
            $rsa->loadKey($public_key);

            if (!$rsa->verify($postdata, $this->_fromBase64url($signature))) {
                return false;
            }

            $timestamp = $this->getRequest()->getData()->get('timestamp', 'int');

            // Only allow requests signed in the last (or next since server clocks might go haywire) 15 minutes
            if (!$timestamp || abs(time() - $timestamp) > 900) {
                return false;
            }
        }
        catch (\Exception $e) {
            return false;
        }

        return true;
    }

    protected function _actionInstall(KControllerContext $context)
    {
        if (!$this->_authenticate()) {
            //throw new KControllerExceptionRequestForbidden('Invalid signature');
        }

        $data = $context->getRequest()->getData();
        $url  = $data->url;
        $error = null;

        try {
            jimport( 'joomla.installer.helper' );

            JFactory::getLanguage()->load('com_installer', JPATH_ADMINISTRATOR);

            $downloadedPackage = JInstallerHelper::downloadPackage($url);

            if (!$downloadedPackage) {
                throw new PlgSystemJoomlatoolsupdaterLicenseException('Unable to download');
            }

            $tmp_dest = rtrim(JFactory::getConfig()->get('tmp_path'), '/\\');
            $package  = JInstallerHelper::unpack($tmp_dest . '/' . $downloadedPackage, true);

            if (empty($package['dir'])) {
                throw new PlgSystemJoomlatoolsupdaterLicenseException('Unable to unpack');
            }

            $installer = new PlgSystemJoomlatoolsupdaterInstaller();
            $result = $installer->install($package['dir']);

            if ($result === false) {
                throw new PlgSystemJoomlatoolsupdaterLicenseException($installer->error);
            }

            try {
                JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
            }
            catch (\Exception $e) {}
        }
        catch (PlgSystemJoomlatoolsupdaterLicenseException $e) {
            $error = $e->getMessage();
        }
        catch (\Exception $e) {
            $error = $e->getMessage() ?: JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL');
        }

        if ($error) {
            throw new KControllerExceptionRequestInvalid($error);
        }

        return new KObjectConfigJson(['success' => true]);
    }

    protected function _actionInfo(KControllerContext $context)
    {
        if (!$this->_authenticate()) {
            throw new KControllerExceptionRequestForbidden('Invalid signature');
        }

        return new KObjectConfigJson($this->_license->getSiteData());
    }

    protected function _actionUpdate(KControllerContext $context)
    {
        if (!$this->_authenticate()) {
            throw new KControllerExceptionRequestForbidden('Invalid signature');
        }

        $data = $context->getRequest()->getData();

        if ($data->public_key) {
            $this->_license->setPublicKey($data->public_key);
        }

        if ($data->license) {
            $this->_license->setLicense($data->license);
        }

        if ($data->site_key) {
            $this->_license->setSiteKey($data->site_key);
        }

        if ($data->api_key) {
            $this->_license->setApiKey($data->api_key);
        }

        return $this->_actionInfo($context);
    }

    protected function _actionRefresh(KControllerContext $context)
    {
        if (!$this->_authenticate()) {
            throw new KControllerExceptionRequestForbidden('Invalid signature');
        }

        /** @var PlgSystemJoomlatoolsupdaterLicense $license */
        $license = $this->getObject('license');
        $license->refresh();

        return $this->_actionInfo($context);
    }

    protected function _fromBase64url($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder)
        {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }
}
