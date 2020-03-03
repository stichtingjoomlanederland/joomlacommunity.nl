<?php

/**
 * @copyright     Copyright (c) 2009-2020 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
require_once WF_EDITOR_LIBRARIES . '/classes/manager.php';

JLoader::registerNamespace('Michelf', __DIR__ . '/vendor/php-markdown/Michelf', false, false, 'psr4');

use Michelf\Markdown;

final class WFTemplateManagerPlugin extends WFMediaManager
{
    protected $_filetypes = 'html=html,htm;text=txt,md';

    public function __construct($config = array())
    {
        parent::__construct($config);

        // add a request to the stack
        $request = WFRequest::getInstance();
        $request->setRequest(array($this, 'loadTemplate'));

        if ($this->getParam('allow_save', 1)) {
            $request->setRequest(array($this, 'createTemplate'));
            $this->addFileBrowserAction('save', array('action' => 'createTemplate', 'title' => JText::_('WF_TEMPLATEMANAGER_CREATE')));
        }
    }

    /**
     * Display the plugin.
     */
    public function display()
    {
        parent::display();

        $document = WFDocument::getInstance();

        $document->addScript(array('templatemanager'), 'plugins');
        $document->addStyleSheet(array('templatemanager'), 'plugins');

        $document->addScriptDeclaration('TemplateManager.settings=' . json_encode($this->getSettings()) . ';');
    }

    public function onUpload($file, $relative = '')
    {
        parent::onUpload($file, $relative);

        $app = JFactory::getApplication();

        if ($app->input->getInt('inline', 0) === 1) {
            $result = array(
                'file' => $relative,
                'name' => basename($file),
            );

            // get the relative filesystem path
            $relative = $this->getFileBrowser()->getFileSystem()->toRelative($file);

            $result['data'] = $this->loadTemplate($relative);

            return $result;
        }

        return array();
    }

    public function createTemplate($dir, $name)
    {
        $browser = $this->getFileBrowser();

        $app = JFactory::getApplication();

        // check path
        WFUtility::checkPath($dir);

        // check name
        WFUtility::checkPath($name);

        // validate name
        if (WFUtility::validateFileName($name) === false) {
            throw new InvalidArgumentException('INVALID FILE NAME');
        }

        // get data
        $data = $app->input->post->get('data', '', 'RAW');
        $data = rawurldecode($data);

        $name = JFile::makeSafe($name) . '.html';
        $path = WFUtility::makePath($dir, $name);

        // Remove any existing template div
        $data = preg_replace('/<div(.*?)class="mceTmpl"([^>]*?)>([\s\S]*?)<\/div>/i', '$3', $data);

        // if the template contains any variables, then treat it as a dynamic template
        if ($this->isDynamicTemplate($data)) {
            $data = '<div class="mceTmpl">' . $data . '</div>';
        }

        $content = "<!DOCTYPE html>\n";
        $content .= "<html>\n";
        $content .= "<head>\n";
        $content .= '<title>' . $name . "</title>\n";
        $content .= "<meta charset=\"utf-8\">\n";
        $content .= "</head>\n";
        $content .= "<body>\n";
        $content .= $data;
        $content .= "\n</body>\n";
        $content .= '</html>';

        if (!$browser->getFileSystem()->write($path, stripslashes($content))) {
            $browser->setResult(JText::_('WF_TEMPLATEMANAGER_WRITE_ERROR'), 'error');
        }

        return $browser->getResult();
    }

    protected function isDynamicTemplate($content)
    {
        return preg_match('/\{\$(.+?)\}/i', $content);
    }

    protected function replaceValuesToArray()
    {
        $data = array();
        $params = $this->getParam('replace_values');

        if ($params) {
            foreach (explode(',', $params) as $param) {
                list($key, $value) = preg_split('/[:=]/', $param);
                $data[$key] = trim($value);
            }
        }

        return $data;
    }

    protected function replaceVars($matches)
    {
        $key = $matches[1];

        switch ($key) {
            case 'modified':
                return strftime($this->getParam('mdate_format', '%Y-%m-%d %H:%M:%S'));
                break;
            case 'created':
                return strftime($this->getParam('cdate_format', '%Y-%m-%d %H:%M:%S'));
                break;
            case 'username':
            case 'usertype':
            case 'name':
            case 'email':
                $user = JFactory::getUser();

                return isset($user->$key) ? $user->$key : $key;
                break;
            default:

                // Replace other pre-defined variables
                $values = $this->replaceValuesToArray();

                if (isset($values[$key])) {
                    return $values[$key];
                }

                break;
        }
    }

    public function loadTemplate($file)
    {
        $browser = $this->getFileBrowser();

        // check path
        WFUtility::checkPath($file);

        $ext = WFUtility::getExtension($file);

        // read content
        $content = $browser->getFileSystem()->read($file);

        // Remove body etc.
        if (preg_match('/<body[^>]*>([\s\S]+?)<\/body>/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        // process markdown
        if (strtolower($ext) === 'md') {
            $content = Markdown::defaultTransform($content);
        }

        // Replace variables
        $content = preg_replace_callback('/\{\$(.+?)\}/i', array($this, 'replaceVars'), $content);

        return $content;
    }

    public function getViewable()
    {
        return $this->getFileTypes('list');
    }

    protected function getFileBrowserConfig($config = array())
    {
        $config['expandable'] = false;
        $config['position'] = 'bottom';

        return parent::getFileBrowserConfig($config);
    }

    public function getTemplateList()
    {
        $list = array();
        $items = $this->getFileBrowser()->getItems('', 0);

        foreach ($items['files'] as $item) {
            if ($item['name'] === "index.html") {
                continue;
            }

            $name = WFUtility::getFilename($item['name']);
            $value = $item['properties']['preview'];

            $list[$name] = $value;
        }

        return $list;
    }
}
