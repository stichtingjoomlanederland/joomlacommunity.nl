<?php

/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.model');
jimport('joomla.utilities.simplexml');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
include_once 'ftp.php';

class SPUpgradeModelCom_Media extends SPUpgradeModelCom {
    
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->status = 'completed';
    }

    public function images($name = null) {
        
        $source = $this->source;

        JFolder::move('images', 'images_bkp' . JFactory::getDate()->format('_Y_m_d_G_i_s'), JPATH_SITE);
                
        $result_create = JFolder::create(JPATH_SITE . '/images');
        if ($result_create !== true) {
            jexit('error created images folder');
        }
        
        $ftp = new SPUpgradeModelFTP();
        $ftp->transfer($source->source_path . '/images', 'images');
        //JFolder::copy($source->source_path . '/images', JPATH_SITE . '/images');
        
    }

    public function template($name = null) {

        $factory = $this->factory;
        //$source = $this->source;
        $name = JRequest::getVar('input_template', '');
        $params = JComponentHelper::getParams('com_spupgrade');
        $ftp_root = str_replace('//', '/', $params->get("ftp_root", '/'));
        $source_path = str_replace('//', '/', $ftp_root . '/templates/' . $name);
        $destination_path = JPATH_SITE . '/tmp/' . $name;
        
        $ftp = new SPUpgradeModelFTP();

        //Check if exists in source
        if (empty($name)) {
            jexit('<p>' . JText::plural('COM_SPUPGRADE_TEMPLATE_ERROR', $source_path) . '</p>');
        }

        if (!$ftp->chdir($source_path)) {
            jexit('<p>' . JText::plural('COM_SPUPGRADE_TEMPLATE_ERROR', $source_path) . '</p>');
        }

        //delete if exists in destination
        if (JFolder::exists($destination_path))
            JFolder::delete($destination_path);

        //copy from source to destination
        if (!$ftp->transfer('/templates/' . $name, '/tmp/' . $name)) {
            jexit('<p>' . JText::_('COM_SPUPGRADE_MSG_ERROR_TEMPLATE') . '</p>');
        }

        //Copy error file
        JFile::copy(JPATH_SITE . '/templates/protostar/error.php', $destination_path . '/error.php');

        //Delete params file
        JFile::delete($destination_path . '/params.ini');

        //Rename templateDetails.xml
        if (!JFile::exists($destination_path . '/v15_templateDetails.xml'))
            JFile::move('templateDetails.xml', 'v15_templateDetails.xml', $destination_path);

        //Process individual files
        $files = JFolder::files($destination_path);
        foreach ($files as $fileName) {
            switch ($fileName) {

                case 'template_thumbnail.png':
                    JFile::copy('template_thumbnail.png', 'template_preview.png', $destination_path);
                    break;

                case 'v15_templateDetails.xml':
                    $xml_source = new JSimpleXML();
                    $xml_source->loadFile($destination_path . '/v15_templateDetails.xml');
                    $implementation = new DOMImplementation();
                    $dtd = $implementation->createDocumentType('install', '-//Joomla! 3.0//DTD template 1.0//EN', 'http://www.joomla.org/xml/dtd/2.5/template-install.dtd');
                    $xml_destination = $implementation->createDocument(null, null, $dtd);
                    $xml_destination->version = '1.0';
                    $xml_destination->encoding = 'utf-8';
                    $xml_destination->formatOutput = true;
                    $xml_destination->preserveWhiteSpace = false;

                    //set extension
                    $el_extension = $xml_destination->createElement('extension');
                    $el_extension->setAttribute('version', '3.0');
                    $el_extension->setAttribute('type', 'template');
                    $el_extension->setAttribute('client', 'site');

                    //get install
                    $el_install = $xml_source->document->_children;
                    $el_extension = $this->xml2dom($xml_destination, $xml_source, $el_install, $el_extension);

                    $xml_destination->appendChild($el_extension);
                    $xml_destination->save($destination_path . '/templateDetails.xml');

                default:
                    //Modify php files
                    $jAp = JFactory::getApplication();
                    $fileName = $destination_path . '/' . $fileName;
                    $strLenght = strlen($fileName);
                    if (substr($fileName, $fileName - 4, 4) == '.php') {
                        $this->fix_php($fileName);
                    }
                    break;
            }
        }
        
        jexit(JText::plural('COM_SPUPGRADE_MSG_TEMPLATE', $destination_path));
    }

    private function xml2dom($xml_destination, $xml_source, $el_install, $el_extension) {
        foreach ($el_install as $value) {
            switch ($value->_name) {
                case 'images':
                    break;
                case 'css':
                    break;
                case 'files':
                    $name = JRequest::getVar('input_template', '');
                    $destination_path = JPATH_SITE . '/tmp/' . $name;
                    $folders = JFolder::folders($destination_path);
                    $files = JFolder::files($destination_path);
                    $el_files = $xml_destination->createElement('files');
                    foreach ($files as $file) {
                        $el_filename = $xml_destination->createElement('filename', $file);
                        $el_files->appendChild($el_filename);
                    }
                    $el_files->appendChild($el_filename);
                    foreach ($folders as $folder) {
                        $el_foldername = $xml_destination->createElement('folder', $folder);
                        $el_files->appendChild($el_foldername);
                    }
                    $el_extension->appendChild($el_files);
                    break;

                case 'params':
                    $config = $xml_destination->createElement('config');
                    $fields = $xml_destination->createElement('fields');
                    $fields->setAttribute('name', 'params');
                    $fieldset = $xml_destination->createElement('fieldset');
                    $fieldset->setAttribute('name', 'advanced');
                    $fieldset = $this->xml2dom($xml_destination, $xml_source, $value->_children, $fieldset);
                    $fields->appendChild($fieldset);
                    $config->appendChild($fields);
                    $el_extension->appendChild($config);
                    break;

                default:
                    if ($value->_name == 'param')
                        $value->_name = 'field';
                    $el_install_child = $xml_destination->createElement($value->_name, trim($value->_data));
                    if (!empty($value->_attributes)) {
                        foreach ($value->_attributes as $key => $attribute) {
                            $el_install_child->setAttribute($key, $attribute);
                        }
                    }
                    if (!empty($value->_children))
                        $el_install_child = $this->xml2dom($xml_destination, $xml_source, $value->_children, $el_install_child);
                    $el_extension->appendChild($el_install_child);
                    break;
            }
        }
        return $el_extension;
    }

    private function fix_php($fileName) {
        $factory = $this->factory;
        $file = $fileName;
        $backupfile = $fileName . '.bak';

        if (!copy($file, $backupfile)) {
            jexit(JText::sprintf('COM_SPUPGRADE_MSG_ERROR_FAILEDTOCOPY', $backupfile));
        }

        $backuphandle = @fopen($backupfile, "r+");
        $handle = @fopen($file, "w");
        if ($handle) {

            while (($buffer = fgets($backuphandle, 4096)) !== false) {

                $buffer = str_replace("defined( '_JEXEC' ) or die( 'Restricted access' );", "defined('_JEXEC') or die;JHtml::_('behavior.framework', true);", $buffer);
                $buffer = str_replace("\$mainframe->", "JFactory::getApplication()->", $buffer);

                IF (fwrite($handle, $buffer) === FALSE) {
                    jexit(JText::sprintf('COM_SPUPGRADE_MSG_ERROR_CANNOTWRITEFILE', $file));
                }
            }
            if (!feof($backuphandle)) {
                jexit('<p><b><font color="red">' . JText::_('COM_SPUPGRADE_MSG_ERROR_UNEXPECTEDFGETS') . '</font></b></p>');
            }
            fflush($handle);
            fclose($handle);
            fclose($backuphandle);
            unlink($backupfile = $fileName . '.bak');
        }
    }

}
